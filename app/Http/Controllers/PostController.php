<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreateForm() {
        return view('create-post');
    }

    public function storeNewPost(Request $request) {
       $incomingFields = $request->validate([
        'title' => 'required',
        'body' => 'required'
       ]);

       $incomingFields['title'] = strip_tags($incomingFields['title']); // stripping out html/javascript tags
       $incomingFields['body'] = strip_tags($incomingFields['body']);
       $incomingFields['user_id'] = auth()->id();

       $newPost = Post::create($incomingFields);

       return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');
    }

    public function viewSinglePost(Post $post) { //Type hinting. It will look the appropriate post in the db based on the incoming value
        $post['body'] = strip_tags(Str::markdown($post->body), '<p><ul><ol><li><strong><em><h3>'); //overriding the body value with markdown
        return view('single-post', ['post' => $post]);
    }

    public function delete(Post $post) {
        
        $post->delete();

        return redirect('/profile/' . auth()->user()->username)->with('success', 'Post succesfully deleted.');
    }

    public function showEditForm(Post $post) {
        return view('edit-post', ['post' => $post]);
    }

   
    public function actuallyUpdate(Post $post, Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']); // stripping out html/javascript tags
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);

        return back()->with('success', 'Post successfully updated.'); //Takes user back to the edit post form
    }
}
