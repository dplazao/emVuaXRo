<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Handles create/view/delete on posts
 * @package App\Http\Controllers
 * @author Ronick Uch 40093861
 */
class PostController extends Controller
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
    
    private function getPost(int $postID)
    {
        return DB::selectOne('SELECT * FROM POST WHERE ID = ?', [$postID]);
    }
    
    private function getAllPost(): array {
        return DB::select('SELECT P.id, M.name as memberName, postName, memberID, 
        P.associationID, classification, privacy 
        FROM POST P    
        JOIN MEMBER M on memberID = M.id
        ORDER BY id DESC');
    }
    
    private function redirectToCreateWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
        ->route('post.create')
        ->with('message', $message)
        ->with('success', $success);
    }
    
    private function redirectToViewWithMessage(int $postID, string $message, bool $success = null): RedirectResponse {
        return redirect()
        ->action([PostController::class, 'view'], ['postID' => $postID])
        ->with('message', $message)
        ->with('success', $success);
    }
    
    private function redirectToListWithMessage(string $message, bool $success = null): RedirectResponse {
        return redirect()
        ->route('post.list')
        ->with('message', $message)
        ->with('success', $success);
    }
    
    public function list()
    {
        $posts = $this->getAllPost();
        return view('post.list', ['posts' => $posts]);
    }
    
    public function view(int $postID)
    {
        $post = $this->getPost($postID);
        $userID = Auth::id();
        $user = [
            'isOwner' => $userID === $post->memberID
        ];
        
        return view('post.view', [
            'post' => $post,
            'user' => $user
        ]);
    }
    
    public function create(Request $request): RedirectResponse
    {
        $memberID = Auth::id();
        $postName = $request->get('postName');
        $postText = $request->get('postText');
        $classification = $request->get('classification');
        $privacy = $request->get('privacy');
        $path = null;
        
        if ($request->hasFile('image')){
            $path = $request->file('image')->storePublicly('images');
        }
        
        if (empty($memberID)) {
            return $this->redirectToCreateWithMessage('You are not logged in.', false);
        } else if (empty($postName)) {
            return $this->redirectToCreateWithMessage('Post name is required.', false);
        } else if (strlen($postName) > 255) {
            return $this->redirectToCreateWithMessage('Post name must be shorter than 255 characters.', false);
        } else if (empty($postText)) {
            return $this->redirectToCreateWithMessage('Posting text is required.', false);
        } else if (strlen($postText) > 5000) {
            return $this->redirectToCreateWithMessage('Posting text must be shorter than 5000 characters.', false);
        } else if (empty($classification)) {
            return $this->redirectToCreateWithMessage('Posting classification option must be selected.', false);
        } else if (empty($privacy)) {
            return $this->redirectToCreateWithMessage('Posting privacy option must be selected.', false);
        } else if (empty($path)) {
            return $this->redirectToCreateWithMessage('A picture must be uploaded.', false);
        }
        
        $postID = 0;
        
        DB::beginTransaction();
        
        try {
            // Create a post
            DB::insert(
                'INSERT INTO POST (postName, memberID, postText, postPicture, classification, privacy) VALUES (?, ?, ?, ?, ?, ?)',
                [trim($postName), $memberID, trim($postText), $path, $classification, $privacy]
                );
            
            $postID = DB::connection()->getPdo()->lastInsertId();
            
            DB::commit();
            return $this->redirectToViewWithMessage($postID, 'Your post was created!', true);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->redirectToCreateWithMessage('There was an error trying to create your post, try again later.', false);
        }
    }
    
    public function deleteView(int $postID)
    {
        $post = $this->getPost($postID);
        
        if (empty($post)) {
            return $this->redirectToListWithMessage('The post you are trying to delete was not found.', false);
        } else if (Auth::id() !== $post->memberID) {
            return $this->redirectToViewWithMessage($postID, 'You can only delete posts you own.', false);
        }
        
        return view('post.delete', [
            'post' => $post,
        ]);
    }
    
    public function deleteAction(int $postID): RedirectResponse {
        $post = $this->getPost($postID);
        
        if (empty($post)) {
            return $this->redirectToListWithMessage('The post you are trying to delete was not found.', false);
        } else if (Auth::id() !== $post->memberID) {
            return $this->redirectToViewWithMessage($postID, 'You can only delete posts you own.', false);
        }
        
        try {
            DB::delete('DELETE FROM POST WHERE id = ?', [$postID]);
            return $this->redirectToListWithMessage('Your post was successfully deleted.', true);
        } catch (Exception $e) {
            return $this->redirectToViewWithMessage($postID,'We failed to delete the post, try again later.');
        }
    }
    
    public function storeComment(Request $request)
    {
        $request->validate([
            'body'=>'required',
        ]);
        
        $input = $request->all();
        $input['userID'] = auth()->user()->id;
        
        Comment::create($input);
        
        return back();
    }
    
}
