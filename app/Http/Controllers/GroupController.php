<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Handles create/edit/view/join groups
 * @package App\Http\Controllers
 * @author dplazao
 */
class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getGroup(int $groupID)
    {
        return DB::selectOne('SELECT * FROM MGROUP WHERE ID = ?', [$groupID]);
    }

    private function getAllGroupsWithCounts(int $memberID = null): array {
        if (empty($memberID)) {
            return DB::select('SELECT G.id, name, information, COUNT(*) as memberCount
            FROM MGROUP G JOIN GROUPMEMBER GM on G.id = GM.groupID
            GROUP BY G.id, name, information;');
        } else {
            // get special list for logged-in users
            // show which groups they own/are in
            // first show groups they own, then that they're in, then by member count
            return DB::select('
                SELECT id, name, information, G.id IN (
                    SELECT id FROM MGROUP G
                        JOIN GROUPMEMBER GM on G.id = GM.groupID
                        WHERE GM.accepted AND GM.memberID = ?
                    ) as isInGroup, G.id IN (
                    SELECT id FROM MGROUP G
                        JOIN GROUPMEMBER GM on G.id = GM.groupID
                        WHERE NOT GM.accepted AND GM.memberID = ?
                    ) as hasJoinRequest, (
                    SELECT COUNT(*) as memberCount FROM MGROUP G2
                        JOIN GROUPMEMBER GM on G2.id = GM.groupID
                        WHERE G2.id = G.id AND GM.accepted
                        GROUP BY G2.id
                    ) AS memberCount, ? = G.owner as isOwner
                    FROM MGROUP G
                    ORDER BY isOwner DESC, hasJoinRequest DESC, isInGroup DESC, memberCount DESC, id',
                [$memberID, $memberID, $memberID]);
        }
    }

    private function getMembersOfGroup(int $groupID): array {
        return DB::select('
                SELECT M.id, M.name, internalEmailAddress, M.id = G.owner as isOwner, GM.accepted FROM MEMBER M
                    JOIN GROUPMEMBER GM on M.id = GM.memberID
                    JOIN MGROUP G on GM.groupID = G.id
                    WHERE GM.groupID = ?
                    ORDER BY isOwner DESC, M.id
                ', [$groupID]);
    }

    private function getGroupMember(int $groupID, int $memberID)
    {
        return DB::selectOne('
                SELECT M.id, M.name, internalEmailAddress, M.id = G.owner as isOwner, GM.accepted FROM MEMBER M
                    JOIN GROUPMEMBER GM on M.id = GM.memberID
                    JOIN MGROUP G on GM.groupID = G.id
                    WHERE GM.groupID = ? AND M.id = ?
                ', [$groupID, $memberID]);
    }

    public function list()
    {
        $groups = $this->getAllGroupsWithCounts(Auth::id());
        return view('group.list', ['groups' => $groups]);
    }

    public function view(int $groupID)
    {
        $group = $this->getGroup($groupID);
        $members = [];
        $user = [];

        if (!empty($group)) {
            $members = $this->getMembersOfGroup($groupID);
            $userID = Auth::id();
            $memberIndex = array_search($userID, array_column($members, 'id'));
            $user = [
                'id' => $userID,
                'isOwner' => $userID === $group->owner,
                'isInGroup' => $memberIndex,
                'isAccepted' => $memberIndex === false ? false : $members[$memberIndex]->accepted,
            ];
        }

        return view('group.view', [
            'group' => $group,
            'members' => $members,
            'user' => $user,
        ]);
    }

    private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('group.list')
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToViewWithMessage(int $groupID, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->action([GroupController::class, 'view'], ['groupID' => $groupID])
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('group.create')
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToEditWithMessage(int $groupID, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('group.editView', $groupID)
            ->with('message', $message)
            ->with('success', $success);
    }

    public function create(Request $request): RedirectResponse {
        $memberID = Auth::id();
        $groupName = $request->get('groupName');
        $groupInfo = $request->get('groupInformation');

        if (empty($memberID)) {
            return $this->redirectToCreateWithMessage('You are not logged in.', false);
        } else if (empty($groupName)) {
            return $this->redirectToCreateWithMessage('Group name is required.', false);
        } else if (strlen($groupName) > 255) {
            return $this->redirectToCreateWithMessage('Group name must be shorter than 255 characters.', false);
        } else if (empty($groupInfo)) {
            return $this->redirectToCreateWithMessage('Group information is required.', false);
        } else if (strlen($groupInfo) > 5000) {
            return $this->redirectToCreateWithMessage('Group information must be shorter than 5000 characters.', false);
        }

        $groupID = 0;

        DB::beginTransaction();

        try {
            // Create the group and insert the user into it
            DB::insert(
                'INSERT INTO MGROUP (name, owner, information) VALUES (?, ?, ?)',
                [trim($groupName), $memberID, trim($groupInfo)]
            );

            $groupID = DB::connection()->getPdo()->lastInsertId();

            DB::insert(
                'INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (?, ?, TRUE)',
                [$memberID, $groupID]
            );

            DB::commit();
            return $this->redirectToViewWithMessage($groupID, 'Your group was created! Invite some friends!', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->redirectToCreateWithMessage('There was an error trying to create your group, try again later.', false);
        }
    }

    public function editView(int $groupID)
    {
        $group = $this->getGroup($groupID);

        if (empty($group)) {
            return redirect('groups.list');
        }

        $members = $this->getMembersOfGroup($groupID);
        $userID = Auth::id();
        $memberIndex = array_search($userID, array_column($members, 'id'));
        $user = [
            'id' => $userID,
            'isOwner' => $userID === $group->owner,
            'isInGroup' => $memberIndex,
            'isAccepted' => $memberIndex === false ? false : $members[$memberIndex]->accepted,
        ];

        if ($user['isOwner'] !== true) {
            return $this->redirectToViewWithMessage($groupID, 'You can only edit groups you own.', false);
        }

        return view('group.edit', [
            'group' => $group,
            'members' => $members,
            'user' => $user,
        ]);
    }

    public function editAction(Request $request, int $groupID): RedirectResponse {
        $memberID = Auth::id();
        $groupName = $request->get('groupName');
        $groupInfo = $request->get('groupInformation');
        $groupData = $this->getGroup($groupID);

        if (empty($groupData)) {
            return $this->redirectToViewWithMessage($groupID, 'That group does not exist', false);
        } else if ($groupData->owner !== $memberID) {
            return $this->redirectToViewWithMessage($groupID, 'You need to be the owner to edit a group.', false);
        } else if (empty($memberID)) {
            return $this->redirectToEditWithMessage($groupID, 'You are not logged in.', false);
        } else if (empty($groupName)) {
            return $this->redirectToEditWithMessage($groupID, 'Group name is required.', false);
        } else if (strlen($groupName) > 255) {
            return $this->redirectToEditWithMessage($groupID, 'Group name must be shorter than 255 characters.', false);
        } else if (empty($groupInfo)) {
            return $this->redirectToEditWithMessage($groupID, 'Group information is required.', false);
        } else if (strlen($groupInfo) > 5000) {
            return $this->redirectToEditWithMessage($groupID, 'Group information must be shorter than 5000 characters.', false);
        }

        DB::beginTransaction();

        try {
            DB::update(
                'UPDATE MGROUP SET name = ?, information = ? WHERE id = ?',
                [$groupName, $groupInfo, $groupID]
            );

            DB::commit();
            return $this->redirectToViewWithMessage($groupID, 'Your group was edited successfully!', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->redirectToEditWithMessage($groupID, 'There was an error trying to edit your group, try again later.', false);
        }
    }

    public function deleteView(int $groupID)
    {
        $group = $this->getGroup($groupID);

        if (empty($group)) {
            return $this->redirectToListWithMessage('The group you are trying to delete was not found.', false);
        } else if (Auth::id() !== $group->owner) {
            return $this->redirectToViewWithMessage($groupID, 'You can only delete groups you own.', false);
        }

        return view('group.delete', [
            'group' => $group,
        ]);
    }

    public function deleteAction(int $groupID): RedirectResponse {
        $group = $this->getGroup($groupID);

        if (empty($group)) {
            return $this->redirectToListWithMessage('The group you are trying to delete was not found.', false);
        } else if (Auth::id() !== $group->owner) {
            return $this->redirectToViewWithMessage($groupID, 'You can only delete groups you own.', false);
        }

        try {
            DB::delete('DELETE FROM MGROUP WHERE id = ?', [$groupID]);
            return $this->redirectToListWithMessage('Your group was successfully deleted.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($groupID,'We failed to delete the group, try again later.');
        }
    }

    public function transferOwnershipView(int $groupID, int $memberID)
    {
        $group = $this->getGroup($groupID);

        if (empty($group)) {
            return $this->redirectToListWithMessage('The group you are trying to transfer to was not found.', false);
        } else if (Auth::id() !== $group->owner) {
            return $this->redirectToViewWithMessage($groupID, 'You can only transfer groups you own.', false);
        }

        $member = $this->getGroupMember($groupID, $memberID);

        if (empty($member)) {
            return $this->redirectToViewWithMessage($groupID, 'The member you are trying to transfer to is not part of the group.', false);
        } else if (!$member->accepted) {
            return $this->redirectToViewWithMessage($groupID, 'You need to accept the member into the group before you can transfer ownership.', false);
        } else if (Auth::id() === $member->id) {
            return $this->redirectToViewWithMessage($groupID, 'You cannot transfer groups to yourself.', false);
        }

        return view('group.transferOwnership', [
            'group' => $group,
            'member' => $member,
        ]);
    }

    public function transferOwnershipAction(int $groupID, int $memberID): RedirectResponse {
        $group = $this->getGroup($groupID);

        if (empty($group)) {
            return $this->redirectToListWithMessage('The group you are trying to delete was not found.', false);
        } else if (Auth::id() !== $group->owner) {
            return $this->redirectToViewWithMessage($groupID, 'You can only delete groups you own.', false);
        }

        try {
            DB::UPDATE('UPDATE MGROUP SET owner = ? WHERE id = ?', [$memberID, $groupID]);
            return $this->redirectToViewWithMessage($groupID,'Your group was successfully transferred.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($groupID,'We failed to transfer the group, try again later.');
        }
    }

    public function join(int $groupID): RedirectResponse {
        $group = DB::selectOne('SELECT * FROM MGROUP WHERE ID = ?', [$groupID]);

        if (empty($group)) {
            return $this->redirectToViewWithMessage($groupID, 'There was no group found matching that ID.', false);
        }

        $member = $this->getGroupMember($groupID, Auth::id());

        if (!empty($member)) {
            if (!$member->accepted) {
                return $this->redirectToViewWithMessage($groupID, 'You have already sent a join request to this group.');
            }
            return $this->redirectToViewWithMessage($groupID, 'You are already in this group.');
        }

        DB::insert(
            'INSERT INTO GROUPMEMBER (memberID, groupID, accepted) VALUES (?, ?, FALSE)',
            [Auth::id(), $groupID]
        );

        return $this->redirectToViewWithMessage($groupID, 'You have sent this group a join request.', true);
    }

    public function leave(int $groupID): RedirectResponse {
        $group = DB::selectOne('SELECT * FROM MGROUP WHERE ID = ?', [$groupID]);

        if (empty($group)) {
            return $this->redirectToViewWithMessage($groupID, 'There was no group found matching that ID.', false);
        }

        $member = $this->getGroupMember($groupID, Auth::id());

        if (empty($member)) {
            return $this->redirectToViewWithMessage($groupID, 'You are not part of this group.', false);
        }

        DB::delete(
            'DELETE FROM GROUPMEMBER WHERE memberID = ? AND groupID = ?',
            [Auth::id(), $groupID]
        );

        return $this->redirectToViewWithMessage($groupID, 'You have left this group, or cancelled your request to join.', true);
    }

    public function acceptMember(int $groupID, int $memberID): RedirectResponse {
        $group = DB::selectOne('SELECT * FROM MGROUP WHERE ID = ?', [$groupID]);

        if (empty($group)) {
            return $this->redirectToListWithMessage('There was no group found matching that ID.', false);
        }

        $requester = $this->getGroupMember($groupID, Auth::id());

        if (empty($requester)) {
            return $this->redirectToViewWithMessage($groupID, 'You are not part of this group.', false);
        } else if ($requester->isOwner === false) {
            return $this->redirectToViewWithMessage($groupID, 'You are not the owner of this group', false);
        }

        $target = $this->getGroupMember($groupID, $memberID);

        if (empty($target)) {
            return $this->redirectToViewWithMessage($groupID, 'That member has not requested to join.', false);
        } else if ($target->accepted) {
            return $this->redirectToViewWithMessage($groupID, 'That member was already accepted', false);
        }

        DB::update(
            'UPDATE GROUPMEMBER SET accepted = true WHERE memberID = ? AND groupID = ?',
            [$memberID, $groupID]
        );

        return $this->redirectToViewWithMessage($groupID, 'You have successfully accepted that member into your group.', true);
    }

    public function removeMember(int $groupID, int $memberID): RedirectResponse {
        $group = DB::selectOne('SELECT * FROM MGROUP WHERE ID = ?', [$groupID]);

        if (empty($group)) {
            return $this->redirectToListWithMessage('There was no group found matching that ID.', false);
        }

        $requester = $this->getGroupMember($groupID, Auth::id());

        if (empty($requester)) {
            return $this->redirectToViewWithMessage($groupID, 'You are not part of this group.', false);
        } else if ($requester->isOwner === false) {
            return $this->redirectToViewWithMessage($groupID, 'You are not the owner of this group', false);
        }

        $target = $this->getGroupMember($groupID, $memberID);

        if (empty($target)) {
            return $this->redirectToViewWithMessage($groupID, 'That member is not part of the group.', false);
        }

        DB::delete(
            'DELETE FROM GROUPMEMBER WHERE memberID = ? AND groupID = ?',
            [$memberID, $groupID]
        );

        return $this->redirectToViewWithMessage($groupID, 'You have successfully removed that member from your group.', true);
    }
}
