<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //
    public function store(){
        $attributes = request()->validate([
            'name' =>       ['required', 'string', 'max:255',  Rule::unique('categories')],
        ]);

        $category = Category::create([
            'name'=>$attributes['name'],
            'slug'=>Str::lower(Str::of($attributes['name'])->replace('&', 'and')->value),
        ]);

        return response([
            'message'=> "Category added!",
            'category'=>$category
        ], 200);
    }

    public function index(){
        $categories = Category::all()->load('events');
        return response([
            'categories'=>$categories
        ], 200);
    }
}
