
<?php namespace App\Http\Controllers;use Exception;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;use Illuminate\Support\Facades\Auth;use Illuminate\Support\Facades\DB;use Illuminate\Support\Facades\Hash;/*** Handles create/edit/view/join groups* @package App\Http\Controllers* @author dplazao*/class UserController extends Controller{/*** Create a new user instance.** @return void*/public function __construct(){$this->middleware('auth');
}
//@author Annes Cherid
public function listAllUsers(int $userID=null)
{
$users = $this->getAllUsers();
return view('users.list', ['users' => $users]);
}
private function getAllUsers(int $memberID = null): array {
return DB::select('SELECT id, privilege,status, name, email
FROM MEMBER;');
}
public function createUser(Request $request){
$memberID = Auth::id();
$userName = $request->get('userName');
$privilege = "user";
$status = "active";
$password = $request->get('password');
$email = $request->get('email');
$address = $request->get('address');
$internalEmailAddress = $email;
// Create the user
DB::insert('INSERT INTO MEMBER(email, name, address, privilege, status, password,internalEmailAddress) VALUES (?, ?, ?, ?, ?, ?,?)',
[$email, $userName, $address, $privilege, $status, Hash::make($password),$internalEmailAddress]);
return view('users.view');
}
private function getUser(int $userID)
{
return DB::selectOne('SELECT * FROM USERS WHERE ID = ?', [$userID]);
}
private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
return redirect()
->route('users.view')
->with('message', $message)
->with('success', $success);
}
private function redirectToViewWithMessage(int $ID, string $message, bool $success = null): RedirectResponse {
return redirect()
->action([UserController::class, 'view'], ['ID' => $ID])
->with('message', $message)
->with('success', $success);
}
private function redirectToCreateWithMessage(string $message, bool $success = null): RedirectResponse {
return redirect()
->route('users.view')
->with('message', $message)
->with('success', $success);
}
private function redirectToEditWithMessage(int $ID, string $message, bool $success = null): RedirectResponse {
return redirect()
->route('user.view', $ID)
->with('message', $message)
->with('success', $success);
}
public function deleteUser(Request $request){
$memberID = Auth::id();
$userName = $request->get('userName');
$id = $request->get('ID');
DB::delete('DELETE FROM MEMBER WHERE id = ? AND name = ?', [$id,$userName]);
return view('users.view');
}
public function editUser(Request $request){
$memberID = Auth::id();
$id = $request->get('ID');
$userName = $request->get('userName');
$password = $request->get('password');
$email = $request->get('email');
$address = $request->get('address');
DB::update('UPDATE MEMBER SET name = ?, password = ?, email=?, address=? WHERE id = ?', [$userName,Hash::make($password),$email,$address,$id]);
return view('users.view');
}
}
