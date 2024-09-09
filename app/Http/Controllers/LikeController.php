<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    //
    public function likeOrunlike(Event $event)
    {
        $like = $event->likes()->where('user_id', auth()->user()->id)->first();


    //if not yet liked, like

    if(!$like)
    {
        auth()->user()->likes()->create([
            'event_id'=> $event->id
        ]);

        return response([
            'message' => 'Liked'
        ], 200);
    }else{
        $like->delete();
        return response([
            'message' => 'Desliked'
        ], 200);
    }

}
}
