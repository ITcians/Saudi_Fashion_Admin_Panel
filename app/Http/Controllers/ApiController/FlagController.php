<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\FlagCommmentModel;
use App\Models\FlagPostModel;
use Exception;
use Illuminate\Http\Request;

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
    public function postFlagged(Request $request)
    {
        try {
            $this->validate($request, [
                'post_id' => 'required',
                'flagged_by_user_id' => 'required',
                'reason' => 'required',
            ]);


            $flag = new FlagPostModel();
            $flag->post_id = $request->input('post_id');
            $flag->flagged_by_user_id = $request->input('flagged_by_user_id');
            $flag->reason = $request->input('reason');

            $flag->save();

            return response()->json(['message' => 'Flag Post added Successfully' , 'FlagPost' => $flag]);

        } catch (Exception $ex) {
            return response()->json(['danger' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function commentFlagged(Request $request)
    {
        try {
            $this->validate($request, [
                'post_id' => 'nullable',
                'comment_id' => 'nullable',
                'flagged_by_user_id' => 'required',
                'reason' => 'required',
            ]);


            $flag = new FlagCommmentModel();
            $flag->post_id = $request->input('post_id');
            $flag->comment_id = $request->input('comment_id');
            $flag->flagged_by_user_id = $request->input('flagged_by_user_id');
            $flag->reason = $request->input('reason');

            $flag->save();

            return response()->json(['message' => 'Flag Comment added Successfully' , 'FlagComment' => $flag]);

        } catch (Exception $ex) {
            return response()->json(['danger' => $ex->getMessage()], 500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
