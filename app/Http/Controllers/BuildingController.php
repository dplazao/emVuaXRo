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
class BuildingController extends Controller
{
    // Allows for `/building/view/@me` to view your own association
    private $MY_BUILDING = '@me';

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

    public function getMemberBuildingID($memberID) {
        $building = DB::selectOne('SELECT B.id FROM BUILDING B
            JOIN CONDO C on B.id = C.buildingID
            WHERE C.ownerID = ?;', [$memberID]);
        return empty($building) ? null : $building->id;
    }

    public function canModifyBuilding($user, $buildingID) {
        if ($buildingID === $this->MY_BUILDING) $buildingID = $this->getMemberBuildingID($user->id);
        // sysadmin can modify any
        if ($user->privilege === 'sysadmin') return true;
        // non-admins can't modify any
        if ($user->privilege !== 'admin') return false;

        $building = DB::selectOne(
            'SELECT B.id, B.associationID, B.spaceFee FROM BUILDING B
                    JOIN ASSOCIATION A on B.associationID = A.id
                    JOIN ASSOCIATIONOWNER AO on A.id = AO.associationID
                    WHERE AO.memberID = ? AND B.id = ?'
            , [$user->id, $buildingID]
        );

        // Building exists & member is marked as an admin for the related association
        return !empty($building);
    }

    public function canViewBuilding($user, $buildingID) {
        if ($buildingID === $this->MY_BUILDING) $buildingID = $this->getMemberBuildingID($user->id);
        // is the user the sysadmin or admin of the building
        if ($this->canModifyBuilding($user, $buildingID)) return true;

        $memberInBuilding = DB::selectOne(
            'SELECT * FROM BUILDING B
                    JOIN CONDO C on B.id = C.buildingID
                    WHERE C.ownerID = ? AND B.id = ?;',
            [$user->id, $buildingID]
        );

        // Building exists and user is a part of it
        return !empty($memberInBuilding);
    }

    public function canTransferCondo($user, $buildingID, $condoID) {
        if ($this->canModifyBuilding($user, $buildingID)) return true;

        $condo = DB::selectOne('SELECT id FROM CONDO WHERE id = ? AND ownerID = ?', [$condoID, $user->id]);

        // Condo exists and you own it
        return !empty($condo);
    }

    // DB functions

    private function getBuilding(int $buildingID)
    {
        return DB::selectOne('SELECT * FROM BUILDING WHERE id = ?', [$buildingID]);
    }

    private function getAllBuildings(): array {
        return DB::select('
            SELECT B.id, name, associationID, spaceFee, coalesce(counts.condoCount, 0) as condoCount FROM BUILDING B
                LEFT JOIN (
                    SELECT B2.id as count_id, COUNT(*) as condoCount FROM BUILDING B2
                        JOIN CONDO C on B2.id = C.buildingID
                        GROUP BY B2.id
                ) as counts
                ON B.id = count_id
        ');
    }

    private function getAllBuildingsForMember($memberID): array {
        return DB::select('
            SELECT B.id, name, associationID, spaceFee, coalesce(counts.condoCount, 0) as condoCount FROM BUILDING B
                LEFT JOIN (
                    SELECT B2.id as count_id, COUNT(*) as condoCount FROM BUILDING B2
                    JOIN CONDO C on B2.id = C.buildingID
                    GROUP BY B2.id
                ) as counts
                ON B.id = count_id
                WHERE associationID = (
                    SELECT A.id FROM ASSOCIATION A
                        JOIN MEMBER M on A.id = M.associationID
                        WHERE M.id = ?
                )
        ', [$memberID]);
    }

    private function getCondosOfBuilding(int $buildingID): array {
        return DB::select('
                SELECT C.id, buildingID, ownerID, parkingSpaces, storageSpace FROM BUILDING B
                    JOIN CONDO C on B.id = C.buildingID
                    WHERE B.id = ?
                ', [$buildingID]);
    }

    // Routes/Actions

    public function list()
    {
        $buildings = Auth::user()->privilege === 'sysadmin'
            ? $this->getAllBuildings()
            : $this->getAllBuildingsForMember(Auth::id());
        return view('building.list', ['buildings' => $buildings]);
    }

    public function view($buildingID)
    {
        if ($buildingID === $this->MY_BUILDING) $buildingID = $this->getMemberBuildingID(Auth::id());
        $building = $this->getBuilding($buildingID);
        $condos = [];
        $user = [];

        if (!empty($building)) {
            $condos = $this->getCondosOfBuilding($buildingID);
            $user = [
                'id' => Auth::id(),
                'canModifyBuilding' => $this->canModifyBuilding(Auth::user(), $buildingID),
                'isSysadmin' => Auth::user()->privilege === 'sysadmin',
            ];
        }

        return view('building.view', [
            'building' => $building,
            'condos' => $condos,
            'user' => $user,
        ]);
    }

    private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('building.list')
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToViewWithMessage($buildingID, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->action([BuildingController::class, 'view'], ['buildingID' => $buildingID])
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateWithMessage(string $message, $input, bool $success = null): RedirectResponse {
        return redirect()
            ->route('building.create')
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToEditWithMessage(int $buildingID, $input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('building.editView', $buildingID)
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateCondoWithMessage(int $buildingID, $input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('building.createCondoView', $buildingID)
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToEditCondoWithMessage(int $buildingID, $condoID, $input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('building.editCondoView', ['buildingID' => $buildingID, 'condoID' => $condoID])
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    public function createCondoView(int $buildingID) {
        $building = $this->getBuilding($buildingID);

        if (empty($building)) return abort(404);
        return view('building.createCondo', [ 'building' => $building, 'userIsSysadmin' => Auth::user()->privilege === 'sysadmin' ]);
    }

    public function createCondoAction(Request $request) {
        $user = Auth::user();

        $parkingSpaces = $request->get('parkingSpaces');
        $storageSpace = $request->get('storageSpace');
        $condoOwner = $request->get('condoOwner');
        $buildingID = $request->get('buildingID');

        $building = $this->getBuilding($buildingID);

        $validationFailed = function ($message) use (&$buildingID, &$request) {
            return $this->redirectToCreateCondoWithMessage($buildingID, $request->input(), $message, false);
        };

        if (empty($building))
            return $this->redirectToListWithMessage('That building was not found.', false);

        if (!$this->canModifyBuilding($user, $building->id))
            return $this->redirectToViewWithMessage('@me','You do not have permission to modify that building.', false);

        if (empty($condoOwner)) {
            // Leave the condo owner empty
            $condoOwner = null;
        } else {
            $ownerAssociation = DB::selectOne('SELECT id FROM MEMBER WHERE id = ? AND associationID = ?', [$condoOwner, $building->associationID]);
            if (empty($ownerAssociation)) {
                return $validationFailed('That condo owner is either a member that does not exist or is not part of the same association as the building.');
            }
        }

        if  (!is_numeric($parkingSpaces)) {
            return $validationFailed('Parking spaces must be a number');
        } else {
            $parkingSpaces = floatval($parkingSpaces);

            if (intval($parkingSpaces) != $parkingSpaces || 0 > $parkingSpaces || $parkingSpaces > 255)
                return $validationFailed('Parking spaces must be an integer between 0 and 255');

            $parkingSpaces = intval($parkingSpaces);
        }

        if  (!is_numeric($storageSpace)) {
            return $validationFailed('Storage space must be a number');
        } else {
            $storageSpace = floatval($storageSpace);

            if (intval($storageSpace) != $storageSpace || 0 > $storageSpace || $storageSpace > 255)
                return $validationFailed('Storage space must be an integer between 0 and 255');

            $storageSpace = intval($storageSpace);
        }

        DB::beginTransaction();

        try {
            // Create the group and insert the user into it
            DB::insert(
                'INSERT INTO CONDO (buildingID, ownerID, parkingSpaces, storageSpace)
                    VALUES (?, ?, ?, ?)',
                [$buildingID, $condoOwner, $parkingSpaces, $storageSpace]
            );

            DB::commit();
            return $this->redirectToViewWithMessage($buildingID, "Condo was created!", true);
        } catch (Exception $e) {
            DB::rollBack();
            return $validationFailed('There was an error trying to create that condo, try again later.');
        }
    }

    public function editCondoView(int $buildingID, int $condoID)
    {
        $building = $this->getBuilding($buildingID);

        if (empty($building))
            return $this->redirectToListWithMessage('That building does not exist.', false);;

        $canModifyBuilding = $this->canModifyBuilding(Auth::user(), $buildingID);

        $condo = DB::selectOne('SELECT * FROM CONDO WHERE id = ?', [$condoID]);

        if (empty($condo))
            return $this->redirectToViewWithMessage($buildingID, 'That condo does not exist.', false);

        return view('building.editCondo', [
            'condo' => $condo,
            'canModifyBuilding' => $canModifyBuilding
        ]);
    }

    public function editCondoAction(Request $request, int $buildingID, int $condoID): RedirectResponse {
        $user = Auth::user();

        $parkingSpaces = $request->get('parkingSpaces');
        $storageSpace = $request->get('storageSpace');
        $condoOwner = $request->get('condoOwner');

        $building = $this->getBuilding($buildingID);

        $validationFailed = function ($message) use (&$buildingID, &$condoID, &$request) {
            return $this->redirectToEditCondoWithMessage($buildingID, $condoID, $request->input(), $message, false);
        };

        if (empty($building))
            return $this->redirectToListWithMessage('That building was not found.', false);

        $condo = DB::selectOne('SELECT * FROM CONDO WHERE id = ?', [$condoID]);

        if (empty($condo))
            return $this->redirectToViewWithMessage($buildingID, 'That condo does not exist.', false);

        if (!$this->canTransferCondo($user, $building->id, $condoID))
            return $this->redirectToViewWithMessage('@me','You do not have permission to modify that condo.', false);

        if (empty($condoOwner)) {
            // Leave the condo owner empty
            $condoOwner = null;
        } else {
            $ownerAssociation = DB::selectOne('SELECT id FROM MEMBER WHERE id = ? AND associationID = ?', [$condoOwner, $building->associationID]);
            if (empty($ownerAssociation)) {
                return $validationFailed('That condo owner is either a member that does not exist or is not part of the same association as the building.');
            }
        }

        if  (!is_numeric($parkingSpaces)) {
            return $validationFailed('Parking spaces must be a number');
        } else {
            $parkingSpaces = floatval($parkingSpaces);

            if (intval($parkingSpaces) != $parkingSpaces || 0 > $parkingSpaces || $parkingSpaces > 255)
                return $validationFailed('Parking spaces must be an integer between 0 and 255');

            $parkingSpaces = intval($parkingSpaces);
        }

        if  (!is_numeric($storageSpace)) {
            return $validationFailed('Storage space must be a number');
        } else {
            $storageSpace = floatval($storageSpace);

            if (intval($storageSpace) != $storageSpace || 0 > $storageSpace || $storageSpace > 255)
                return $validationFailed('Storage space must be an integer between 0 and 255');

            $storageSpace = intval($storageSpace);
        }

        DB::beginTransaction();

        try {
            // Create the group and insert the user into it

            if ($this->canModifyBuilding($user, $buildingID)) {
                DB::insert(
                    'UPDATE CONDO SET ownerID = ?, parkingSpaces = ?, storageSpace = ? WHERE id = ?',
                    [$condoOwner, $parkingSpaces, $storageSpace, $condoID]
                );
            } else {
                DB::insert(
                    'UPDATE CONDO SET ownerID = ? WHERE id = ?',
                    [$condoOwner, $condoID]
                );
            }

            DB::commit();
            return $this->redirectToViewWithMessage($buildingID, "Condo was updated!", true);
        } catch (Exception $e) {
            DB::rollBack();
            return $validationFailed('There was an error trying to edit that condo, try again later.');
        }
    }

    public function removeCondo($buildingID, int $condoID) {
        $association = $this->getBuilding($buildingID);

        if (empty($association)) {
            return $this->redirectToListWithMessage('The building you are trying to delete a condo from was not found.', false);
        }

        $condoExists = DB::selectOne('SELECT * FROM CONDO WHERE id = ? AND buildingID = ?', [$condoID, $buildingID]);

        if (empty($condoExists)) {
            return $this->redirectToViewWithMessage($buildingID, 'That condo is not part of that building.', false);
        }

        try {
            DB::delete('DELETE FROM CONDO WHERE id = ? AND buildingID = ?', [$condoID, $buildingID]);
            return $this->redirectToViewWithMessage($buildingID, 'That condo was successfully deleted.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($buildingID,'We failed to delete that condo, try again later.', false);
        }
    }

    public function createAction(Request $request): RedirectResponse {
        $associationID = $request->get('associationID');
        $buildingName = $request->get('buildingName');
        $spaceFee = $request->get('spaceFee');

        $validationFailed = function ($message) use (&$request) {
            return $this->redirectToCreateWithMessage($message, $request->input(), false);
        };

        if (empty($associationID))
            return $validationFailed('Association ID is missing.');

        $associationExists = DB::selectOne('SELECT id FROM ASSOCIATION WHERE id = ?', [$associationID]);

        if (empty($associationExists))
            return $validationFailed('Association ID provided does not exist.');

        if (empty($buildingName))
            return $validationFailed('Building name must exist.');

        if (strlen($buildingName) > 255)
            return $validationFailed('Building name is too long.');

        if (empty($spaceFee))
            return $validationFailed('Building space fee cannot be empty.');

        if  (!is_numeric($spaceFee)) {
            return $validationFailed('Building space fee must be a number');
        } else {
            $spaceFee = floatval($spaceFee);

            if (0 > $spaceFee || $spaceFee > 255)
                return $validationFailed('Space fee must be number between 0 and 255');

            $spaceFee = intval($spaceFee);
        }

        DB::beginTransaction();

        try {
            DB::insert(
                'INSERT INTO BUILDING (name, associationID, spaceFee) VALUES (?, ?, ?)',
                [$buildingName, $associationID, $spaceFee]
            );

            $buildingID = DB::connection()->getPdo()->lastInsertId();

            DB::commit();
            return $this->redirectToViewWithMessage($buildingID, 'Your association was created. Create some users.', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->redirectToCreateWithMessage('There was an error trying to create your building, try again later.', $request->input(), false);
        }
    }

    public function editView(int $buildingID)
    {
        $building = $this->getBuilding($buildingID);

        if (empty($building))
            return $this->redirectToListWithMessage('That building does not exist.', false);;

        if (!$this->canModifyBuilding(Auth::user(), $buildingID))
            return $this->redirectToListWithMessage('You cannot modify that building.', false);

        return view('building.edit', [
            'building' => $building,
        ]);
    }

    public function editAction(Request $request, int $buildingID): RedirectResponse {
        $user = Auth::user();
        $buildingName = $request->get('buildingName');
        $spaceFee = $request->get('spaceFee');

        $buildingExists = DB::selectOne('SELECT id FROM BUILDING WHERE id = ?', [$buildingID]);

        if (empty($buildingExists))
            return $this->redirectToListWithMessage('That building does not exist', false);

        $validationFailed = function ($message) use (&$buildingID, &$request) {
            return $this->redirectToEditWithMessage($buildingID, $request->input(), $message, false);
        };

        if (empty($buildingName))
            return $validationFailed('Building name must exist.');

        if (strlen($buildingName) > 255)
            return $validationFailed('Building name is too long.');

        if (empty($spaceFee))
            return $validationFailed('Building space fee cannot be empty.');

        if  (!is_numeric($spaceFee)) {
            return $validationFailed('Building space fee must be a number');
        } else {
            $spaceFee = floatval($spaceFee);

            if (0 > $spaceFee || $spaceFee > 255)
                return $validationFailed('Space fee must be a number between 0 and 255');

            $spaceFee = intval($spaceFee);
        }

        DB::beginTransaction();

        try {
            DB::update(
                'UPDATE BUILDING SET name = ?, spaceFee = ? WHERE id = ?',
                [$buildingName, $spaceFee, $buildingID]
            );

            DB::commit();
            return $this->redirectToViewWithMessage($buildingID, 'Your building was edited successfully!', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $validationFailed('There was an error trying to edit your building, try again later.');
        }
    }

    public function deleteView(int $buildingID)
    {
        $building = $this->getBuilding($buildingID);

        if (empty($building)) {
            return $this->redirectToListWithMessage('The building you are trying to delete was not found.', false);
        }

        return view('building.delete', [
            'building' => $building,
        ]);
    }

    public function deleteAction(int $buildingID): RedirectResponse {
        $building = $this->getBuilding($buildingID);

        if (empty($building)) {
            return $this->redirectToListWithMessage('The building you are trying to delete was not found.', false);
        }

        try {
            DB::delete('DELETE FROM BUILDING WHERE id = ?', [$buildingID]);
            return $this->redirectToListWithMessage('Your building was successfully deleted.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($buildingID,'We failed to delete the building, try again later.');
        }
    }
}
