<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CampusController extends Controller
{

    public function index (){
        $campuses = Campus::all();
        return response([
            'campuses' => $campuses,
        ], 200);
    }
    public function edit(Campus $campus)
    {
        return response([
            'user' => $campus,
        ], 200);
    }


    public function destroy(Campus $campus)
    {
        $campus->delete();
        return response([
            'message'=> "Campus deleted!"
        ], 200);
    }
    public function update(Campus $campus, Request $request)
    {
        $attributes = $request->validate([
            'name' =>       ['required', 'string', 'max:255',  Rule::unique('campuses')->ignore($campus->id)],
            'city' =>       ['required', 'string', 'max:255'],
        ]);



        $campus->update([
            'name'=>$attributes['name'],
            'slug'=>Str::lower(Str::of($attributes['name'])->replace('&', 'and')->value),
            'updated_at'=>now(),
            'city'=>$attributes['city'],
    ]);

    return response([
        'message'=> "Campus updated!"
    ], 200);

}


    public function store(){
        $attributes = request()->validate([
            'name' =>       ['required', 'string', 'max:255',  Rule::unique('campuses')],
            'city' =>       ['required', 'string', 'max:255'],
        ]);

      $campus =  Campus::create([
        'name'=>$attributes['name'],
        'slug'=>Str::lower(Str::of($attributes['name'])->replace('&', 'and')->value),
        'city'=>$attributes['city'],
        ]);

        return response([
            'message'=> "Campus added!",
            'campus'=>$campus
        ], 200);
    }
}
