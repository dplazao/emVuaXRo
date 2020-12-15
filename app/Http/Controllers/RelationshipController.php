<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class RelationshipController
 * Handles view/create/delete relationships between members
 * @package App\Http\Controllers
 * @author dplazao 40132793
 */
class RelationshipController extends Controller
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

    private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('relationship.list')
            ->with('message', $message)
            ->with('success', $success);
    }

    private function redirectToCreateWithMessage($input, string $message, bool $success = null): RedirectResponse {
        return redirect()
            ->route('relationship.create')
            ->withInput($input)
            ->with('message', $message)
            ->with('success', $success);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list()
    {
        $userID = Auth::id();
        $relationships = DB::select('
            SELECT M.name as firstName, M.id as firstID, MR.type, M2.name as secondName, M2.id as secondID FROM MEMBERRELATIONSHIP MR
                JOIN MEMBER M on MR.memberID = M.id
                JOIN MEMBER M2 on MR.withMemberID = M2.id
                WHERE memberID = ? OR withMemberID = ?;
        ', [$userID, $userID]);
        return view('relationship.list', [
            'relationships' => $relationships,
            'userID' => $userID
        ]);
    }

    public function createAction(Request $request) {
        $userID = Auth::id();

        $relationshipType = $request->get('relationshipType');
        $withMemberID = $request->get('withMemberID');

        $validationFailed = function ($message) use (&$request) {
            return $this->redirectToCreateWithMessage($request->input(), $message, false);
        };

        $otherMember = DB::selectOne(
            'SELECT id FROM MEMBER WHERE id = ?',
            [$withMemberID]
        );

        if (empty($otherMember))
            return $validationFailed('That other member does not exist.');

        $relationship = DB::select(
            'SELECT * FROM MEMBERRELATIONSHIP WHERE memberID = ? AND withMemberID = ?',
            [$userID, $withMemberID]
        );

        $relationship2 = DB::select(
            'SELECT * FROM MEMBERRELATIONSHIP WHERE memberID = ? AND withMemberID = ?',
            [$withMemberID, $userID]
        );

        if (!empty($relationship) || !empty($relationship2))
            return $validationFailed('That relationship already exists.');

        $relationshipType = strtolower($relationshipType);

        if ($relationshipType !== 'friend' && $relationshipType !== 'family' && $relationshipType !== 'colleague')
            return $validationFailed('Invalid relationship type: "friend", "family", or "colleague" expected.');

        DB::insert(
            'INSERT INTO MEMBERRELATIONSHIP (memberID, type, withMemberID) VALUES (?, ?, ?)',
            [$userID, $relationshipType, $withMemberID]
        );

        return $this->redirectToListWithMessage('Relationship created.', true);
    }

    public function delete($memberID, $withMemberID) {
        $userID = Auth::id();

        if ($memberID != $userID && $withMemberID != $userID)
            return $this->redirectToListWithMessage('You can only manage relationships that you are a part of.', false);

        $relationship = DB::select(
            'SELECT * FROM MEMBERRELATIONSHIP WHERE memberID = ? AND withMemberID = ?',
            [$memberID, $withMemberID]
        );

        if (empty($relationship))
            return $this->redirectToListWithMessage('That relationship does not exist.', false);

        DB::delete(
            'DELETE FROM MEMBERRELATIONSHIP WHERE memberID = ? AND withMemberID = ?',
            [$memberID, $withMemberID]
        );

        return $this->redirectToListWithMessage('Relationship deleted.', true);
    }
}
