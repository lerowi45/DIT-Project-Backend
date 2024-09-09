<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class EventController extends Controller
{
    //get all events

    public function index()
    {
        return response([
            'events' => Event::orderBy('created_at', 'desc')
                ->with('creator:id,username,avatar')
                ->with('comments')
                ->withCount('comments', 'likes')
                ->with('likes', function($like){
                    return $like->where('user_id', auth()->user()->id)
                    ->select('id', 'user_id', 'event_id');
                })->filter(request([

                    'search', 'category', 'campus', 'author'

                ]))->get()
        ], 200);
    }

    //get single event
    public function show(Event $event)
    {
        return response([
            'event' => $event->loadCount('likes')->load(['creator:id,username,avatar', 'comments'])
        ], 200);
    }

    //create an event
    public function store(Request $request)
    {
        // Define validation rules in an array
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'published' => ['required', 'boolean'],
            'location' => ['required', 'string'],
            'datetime' => ['required'],
            'keywords' => ['required', 'string', 'max:255'],
            'capacity' => ['integer', 'min:1'],
            'thumbnail' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Example: Image validation
            'category_id' => ['required', 'int', 'exists:categories,id'], // Validate that category_id exists in the categories table
        ];

        // Define custom error messages (optional)
        $customMessages = [
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be in JPEG, PNG, JPG, or GIF format.',
            'thumbnail.max' => 'The thumbnail size should not exceed 2MB.',
        ];

        // Validate the request data using the validate method
        $attributes = $request->validate($rules, $customMessages);

        $thumbnail = $this->saveImage($request->thumbnail, 'events');
        $attributes['thumbnail'] = $thumbnail;
        // If validation passes, $validatedData contains the validated input data
        // Create the event and store it in the database using $validatedData

        $event = auth()->user()->events()->create($attributes);
        echo 'event created';

        //catch error in notifcations

        $this->sendNotification(auth()->user(), 'Your event has been created successfully!');


        return response([
            'message' => 'Event created',
            'event' => $event
        ], 200);
    }

    //update event
    public function update(Event $event, Request $request)
    {
        if(!$event)
        {
            return response([
                'message'=> 'Event not found'
            ], 403);
        }

        if($event->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }
        // Defining validation rules
        $rules = [
            'title' =>       ['string', 'max:255'],
            'description' => ['string'],
            'published' =>   ['boolean'],
            'location' =>    ['string'],
            'datetime' =>    ['date'],
            'keywords' =>    ['string', 'max:255'],
            'capacity' =>    ['integer', 'min:1'],
            'thumbnail' =>   ['image', 'mimes:jpeg,png,jpg,gif',  'max:2048'], // Example: Image validation
            'user_id' =>     ['exists:users,id'], // Validate that creator_id exists in the users table
            'category_id' => ['exists:categories,id'], // Validate that category_id exists in the categories table
        ];

        // Define custom error messages (optional)
        $customMessages = [
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be in JPEG, PNG, JPG, or GIF format.',
            'thumbnail.max' => 'The thumbnail size should not exceed 2MB.',
        ];

        // Validate the request data using the validate method
        $attributes = $request->validate($rules, $customMessages);

        // If validation passes, $validatedData contains the validated input data
        // Create the event and store it in the database using $validatedData

        $event = auth()->user()->events()->update($attributes);

        return response([
            'message' => 'Event updated',
            'event' => $event
        ], 200);
    }

    //delete evvent
    public function destroy(Event $event){

        if(!$event)
        {
            return response([
                'message'=> 'Event not found'
            ], 403);
        }

        if($event->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $event->delete();

        return response([
            'message'=> "Event deleted!"
        ], 200);
    }
}

