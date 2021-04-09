<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return stories
        if ($request->user()) {
            return $request->user()->stories()->orderBy('id', 'desc')->get();
        }

        return Story::orderBy('id', 'desc')->limit(10)->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate request
        $validator = Validator::make($request->all(), [
            'title' => 'required | string | min:3 | max:100',
            'description' => 'required | string | min:10 | max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return Story::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(Story $story)
    {
        return $story;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Story $story)
    {
        //check authentication
        if (!$request->user()->tokenCan('server:update')) {
            return response()->json(['error' => 'Not Authorized!!'], 400);
        }

        //validate request
        $validator = Validator::make($request->all(), [
            'title' => 'nullable | string | min:3 | max:100',
            'description' => 'nullable | string | min:10 | max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $updated = $story->update($request->all());
        if (!$updated) {
            return response()->json(['error' => 'Internal Server Error!!'], 500);
        }

        return response()->json('Successfully Updated!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Story $story)
    {
        //check authentication
        if (!$request->user()->tokenCan('server:update')) {
            return response()->json(['error' => 'Not Authorized!!'], 400);
        }

        return $story->delete();
    }

    public function recentstories()
    {
        return Story::orderBy('id', 'desc')->limit(3)->get();
    }
}
