<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Handles create/edit/view associations
 * @package App\Http\Controllers
 * @author dplazao
 */
class AssociationController extends Controller
{
    // Allows for `/association/view/@me` to view your own association
    private $MY_ASSOCIATION = '@me';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Gates

    public function getMemberAssociation($userID) {
        $association = DB::selectOne('SELECT associationID FROM MEMBER WHERE id = ?', [$userID]);
        return empty($association) ? null : $association->associationID;
    }

    public function canModifyAssociation($user, $associationID) {
        if ($associationID === $this->MY_ASSOCIATION) $associationID = $this->getMemberAssociation($user->id);
        // sysadmin can modify any
        if ($user->privilege === 'sysadmin') return true;
        // non-owners can't modify any
        if ($user->privilege !== 'admin') return false;

        $ass = DB::selectOne(
            'SELECT * FROM ASSOCIATION JOIN ASSOCIATIONOWNER A on ASSOCIATION.id = A.associationID WHERE id = ? AND memberID = ?'
            , [$associationID, $user->id]
        );

        // Association exists & member is marked as an owner
        return !empty($ass);
    }

    public function canViewAssociation($user, $associationID) {
        if ($associationID === $this->MY_ASSOCIATION) $associationID = $this->getMemberAssociation($user->id);
        // is the user the sysadmin or owner of the association
        if ($this->canModifyAssociation($user, $associationID)) return true;

        $memberInAssociation = DB::selectOne(
            'SELECT * FROM MEMBER WHERE id = ? AND associationID = ?',
            [$user->id, $associationID]
        );

        // user is part of the association and can view it
        return !empty($memberInAssociation);
    }

    // DB functions

    private function getAssociation(int $assID)
    {
        return DB::selectOne('SELECT * FROM ASSOCIATION WHERE id = ?', [$assID]);
    }

    private function getAllAssociations(): array {
        return DB::select('
            SELECT A.id, A.name, coalesce(counts.member_count, 0) as member_count FROM ASSOCIATION A
            LEFT JOIN (
                SELECT A2.ID AS count_id, COUNT(*) as member_count FROM ASSOCIATION A2
                JOIN MEMBER M on A2.id = M.associationID
                GROUP BY A2.id
            ) as counts
            ON A.id = count_id;
        ');
    }

    private function getMembersOfAssociation(int $assID): array {
        return DB::select('
                SELECT M.id, M.name, M.internalEmailAddress,
                       M.id IN (SELECT memberID FROM ASSOCIATIONOWNER AO WHERE A.id = AO.associationID) as isOwner
                    FROM ASSOCIATION A
                    JOIN MEMBER M on A.id = M.associationID
                    WHERE A.id = ?
                    ORDER BY isOwner DESC, id
                ', [$assID]);
    }

    // Routes/Actions

    public function list()
    {
        $associations = $this->getAllAssociations();
        return view('association.list', ['associations' => $associations]);
    }

    public function view($assID)
    {
        if ($assID === $this->MY_ASSOCIATION) $assID = $this->getMemberAssociation(Auth::id());
        $association = $this->getAssociation($assID);
        $members = [];
        $user = [];

        if (!empty($association)) {
            $members = $this->getMembersOfAssociation($assID);
            $userID = Auth::id();
            $memberIndex = array_search($userID, array_column($members, 'id'));
            $inAssociation = $memberIndex !== false;
            $user = [
                'id' => $userID,
                'isOwner' => $inAssociation && $members[$memberIndex]->isOwner,
                'isInAssociation' => $inAssociation,
                'isSysadmin' => Auth::user()->privilege === 'sysadmin',
            ];
        }

        return view('association.view', [
            'association' => $association,
            'members' => $members,
            'user' => $user,
        ]);
    }

    private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('association.list')
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToViewWithMessage($associationID, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->action([AssociationController::class, 'view'], ['associationID' => $associationID])
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateWithMessage(string $message, $input, bool $success = null): RedirectResponse {
        return redirect()
            ->route('association.create')
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToEditWithMessage(int $associationID, $input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('association.editView', $associationID)
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateMemberWithMessage(int $associationID, $input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('association.createMemberView', $associationID)
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    public function createMemberView(int $associationID) {
        $association = $this->getAssociation($associationID);

        if (empty($association)) return abort(404);
        return view('association.createMember', [ 'association' => $association, 'userIsSysadmin' => Auth::user()->privilege === 'sysadmin' ]);
    }

    public function createMemberAction(Request $request) {
        $user = Auth::user();

        $privilege = $request->get('memberPrivilege');
        $email = $request->get('memberEmail');
        $internalEmail = $request->get('memberInternalEmail');
        $password = $request->get('memberPassword');
        $name = $request->get('memberName');
        $address = $request->get('memberAddress');
        $associationID = $request->get('memberAssociationID');

        $association = $this->getAssociation($associationID);

        $validationFailed = function ($message) use (&$associationID, &$request) {
            return $this->redirectToCreateMemberWithMessage($associationID, $request->input(), $message, false);
        };

        if (empty($association))
            return $this->redirectToListWithMessage('That association was not found.', false);

        if (!$this->canModifyAssociation($user, $association->id))
            return $this->redirectToViewWithMessage('@me','You do not have permission to modify that association.', false);

        if (empty($privilege))
            return $validationFailed('Member privilege is required.');

        $privilege = strtolower(trim($privilege));

        if ($privilege !== 'owner' && $privilege !== 'admin')
            return $validationFailed('Member privilege must be owner or admin.');

        if ($privilege === 'admin' && $user->privilege !== 'sysadmin')
            return $validationFailed('Only sysadmins can create admin members. Want another? Contact your sysadmin.');

        if (preg_match('/.+@.{2,}/', $email) === 0)
            return $validationFailed('Invalid member email format.');

        $otherMember = DB::selectOne('SELECT id FROM MEMBER WHERE email = ?', [trim($email)]);

        if (!empty($otherMember))
            return $validationFailed('There is already a member with that email, did you already register them?');

        if (empty($internalEmail))
            return $validationFailed('Member needs an internal email address.');

        if (empty($password))
            return $validationFailed('Member needs a password.');

        if (empty($name))
            return $validationFailed('Member needs a name.');

        if (empty($address))
            return $validationFailed('Member needs an address');

        DB::beginTransaction();

        try {
            // Create the group and insert the user into it
            DB::insert(
                'INSERT INTO MEMBER (privilege, status, password, remember_token, mustChangePassword, name, email, address, internalEmailAddress, associationID)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$privilege, 'active', Hash::make($password), null, false, trim($name), trim($email), trim($address), trim($internalEmail), $associationID]
            );

            if ($privilege === 'admin') {
                // Make them an admin
                $memberID = DB::connection()->getPdo()->lastInsertId();

                DB::insert(
                    'INSERT INTO ASSOCIATIONOWNER (associationID, memberID) VALUES (?, ?)',
                    [$associationID, $memberID]
                );
            }

            DB::commit();
            return $this->redirectToViewWithMessage($associationID, "Member $name was created!", true);
        } catch (Exception $e) {
            DB::rollBack();
            return $validationFailed('There was an error trying to create that member, try again later.');
        }
    }

    public function createAction(Request $request): RedirectResponse {
        $associationName = $request->get('associationName');

        $validationFailed = function ($message) use (&$request) {
            return $this->redirectToCreateWithMessage($request->input(), $message, false);
        };

        if (empty($associationName))
            return $validationFailed('Association name cannot be empty.');

        if (strlen($associationName) > 255)
            return $validationFailed('Association name is too long.');

        $otherAssociation = DB::selectOne('SELECT id FROM ASSOCIATION WHERE name = ?', [trim($associationName)]);

        if (!empty($otherAssociation))
            return $validationFailed('Association name already exists.');

        DB::beginTransaction();

        try {
            DB::insert(
                'INSERT INTO ASSOCIATION (name) VALUES (?)',
                [trim($associationName)]
            );

            $associationID = DB::connection()->getPdo()->lastInsertId();

            DB::commit();
            return $this->redirectToViewWithMessage($associationID, 'Your association was created. Create some users.', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->redirectToCreateWithMessage('There was an error trying to create your association, try again later.', false);
        }
    }

    public function editView(int $associationID)
    {
        $association = $this->getAssociation($associationID);

        if (empty($association))
            return $this->redirectToListWithMessage('That association does not exist.', false);;

        if (!$this->canModifyAssociation(Auth::user(), $associationID))
            return $this->redirectToListWithMessage('You cannot modify that association.', false);

        return view('association.edit', [
            'association' => $association,
        ]);
    }

    public function editAction(Request $request, int $associationID): RedirectResponse {
        $user = Auth::user();
        $associationName = $request->get('associationName');

        $associationExists = DB::selectOne('SELECT id FROM ASSOCIATION WHERE id = ?', [$associationID]);

        if (empty($associationExists))
            return $this->redirectToListWithMessage('That association does not exist', false);

        $validationFailed = function ($message) use (&$associationID, &$request) {
            return $this->redirectToEditWithMessage($associationID, $request->input(), $message, false);
        };

        if (empty($associationName))
            return $validationFailed('Association name cannot be empty.');

        if (strlen($associationName) > 255)
            return $validationFailed('Association name is too long.');

        $otherAssociation = DB::selectOne('SELECT id FROM ASSOCIATION WHERE name = ?', [trim($associationName)]);

        if (!empty($otherAssociation))
            return $validationFailed('Association name already exists.');

        DB::beginTransaction();

        try {
            DB::update(
                'UPDATE ASSOCIATION SET name = ? WHERE id = ?',
                [trim($associationName), $associationID]
            );

            DB::commit();
            return $this->redirectToViewWithMessage($associationID, 'Your association was edited successfully!', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $validationFailed('There was an error trying to edit your association, try again later.');
        }
    }

    public function deleteView(int $associationID)
    {
        $association = $this->getAssociation($associationID);

        if (empty($association)) {
            return $this->redirectToListWithMessage('The association you are trying to delete was not found.', false);
        }

        return view('association.delete', [
            'association' => $association,
        ]);
    }

    public function deleteAction(int $associationID): RedirectResponse {
        $association = $this->getAssociation($associationID);

        if (empty($association)) {
            return $this->redirectToListWithMessage('The association you are trying to delete was not found.', false);
        }

        try {
            DB::delete('DELETE FROM ASSOCIATION WHERE id = ?', [$associationID]);
            return $this->redirectToListWithMessage('Your association was successfully deleted.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($associationID,'We failed to delete the association, try again later.');
        }
    }

    public function removeMember($associationID, int $memberID) {
        $association = $this->getAssociation($associationID);

        if (empty($association)) {
            return $this->redirectToListWithMessage('The association you are trying to delete a member from was not found.', false);
        }

        $memberAssociation = $this->getMemberAssociation($memberID);

        if (empty($memberAssociation)) {
            return $this->redirectToViewWithMessage($associationID, 'That member does not seem to exist.', false);
        }

        if ($associationID != $memberAssociation) {
            return $this->redirectToViewWithMessage($associationID, 'That member is not part of your association.', false);
        }

        try {
            $groupsTaken = DB::update('UPDATE MGROUP SET owner = ? WHERE owner = ?', [Auth::id(), $memberID]);
            DB::delete('DELETE FROM MEMBER WHERE id = ? AND associationID = ?', [$memberID, $associationID]);
            return $this->redirectToViewWithMessage($associationID, 'That member was successfully deleted.' . ($groupsTaken > 0 ? " You have taken ownership of $groupsTaken group(s) that they owned." : ''), true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($associationID,'We failed to delete that member, try again later.', false);
        }
    }
}
