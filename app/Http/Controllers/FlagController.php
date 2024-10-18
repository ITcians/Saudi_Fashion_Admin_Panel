<?php

namespace App\Http\Controllers;

use App\Models\FlagPostModel;
use Illuminate\Http\Request;
use App\Models\PostModel;
use App\Models\User;
class FlagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'post_id' => 'required',
                'flagged_by_user_id' => 'required',
                'reason' => 'required',
            ]);


            // Save to database
            $flag = new FlagPostModel();
            $flag->post_id = $request->input('post_id');
            $flag->flagged_by_user_id = $request->input('flagged_by_user_id');
            $flag->reason = $request->input('reason');

            $flag->save();

            return back()->with('success','Flag added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['danger' => $th->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = PostModel::where('id', $id)->first(); // Retrieve the post record using first()
        return view('posts.flag', compact('post')); // Pass 'post' as an associative array to compact
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
