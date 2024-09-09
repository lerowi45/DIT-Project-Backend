<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //get all comments

    public function index(Event $event){
        return response([
            'comments' => $event->comments()->with('user:id,username,avatar')->get()
        ], 200);
    }

    //create a comment
    public function store(Event $event){
        request()->validate([
            'body'=> ['required', 'max:500']
        ]);

        $event->comments()->create([

            'body'=>request('body'),

            'user_id'=>request()->user()->id
        ]);

        return response([
            'message' => 'Comment created'
        ], 200);
    }

    //update a comment
    public function update(Comment $comment){
        request()->validate([
            'body'=> ['required', 'max:500']
        ]);

        $comment->update([
            'body'=>request('body'),
        ]);

        return response([
            'message' => 'Comment updated!'
        ], 200);

    }

    //delete comment
    public function destroy(Comment $comment){
        $comment->delete();
        return response([
            'message' => 'Comment deleted'
        ], 200);
    }
}
