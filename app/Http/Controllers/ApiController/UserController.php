<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\FellowsModel;
use App\Models\OrderModel;
use App\Models\UserModels\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use stdClass;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $res;
     function __construct()
     {
        $this->res = new stdClass();
     }
    public function user_types()
    {
        try {
            $UserType = UserType::all();
            return response()->json(['message' => 'User Type Record', 'Usertype' => $UserType]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }




    /**
     * Show the form for creating a new resource.
     */
    public function update_usertypes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usertypeid' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $user = User::findOrFail(Auth::id()); // Assuming your userId is coming from parameter id

            $user->account_type = $request->usertypeid;
            $user->save();

            return response()->json(['message' => 'UserType updated successfully'], 200);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Update profile info
     */
    public function update_profile(Request $request )
    {
        try {
            $data = $this->validate($request, [
                'fullname' => 'required',
                'username' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'bio' => '',
            ]);

            $AuthId = Auth::id();
            $user = User::findOrFail($AuthId); // Find user by ID parameter

            // Update user attributes with validated data
            $user->fullname = $data['fullname'];
            $user->username = $data['username'];
            $user->email = $data['email'];
            $user->phone = $data['phone'];
            $user->bio = $data['bio'];

            // Save the changes to the database
            $user->save();

            $this->res->user = $user;
            $this->res->message = "Data Updated Successfully";


        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return response()->json($this->res);
        }
    }

    // update image profile

    public function updateProfileImage(Request $request, )
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif' // Adjust validation rules as needed
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Find user data associated with the provided user ID
            $AuthId = Auth::id();
            $user = User::findOrFail($AuthId);

            $coverImage = $request->file('image');

            $imageName = '/upload_images/profile_' . time() . '.' . $coverImage->getClientOriginalExtension();


            $coverImage->move(public_path('upload_images'), $imageName);

            $user->update([
                'image' =>  $imageName 
            ]);

            return response()->json(['message' => 'Profile image updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function searchUser(Request $request)
    {
        if ($request->input('search')) {
            // Search for users based on the provided search string
            return User::where('fullname', 'like', '%' . $request->input('search') . '%')
                ->orderBy('fullname') // Order by the 'fullname' column
                ->orderBy('username') // Then by the 'username' column
                ->orderBy('email')    // Finally by the 'email' column
                ->get();
        } else {
            // If no search input is provided, return an empty array or any default response
            return [];
        }
    }



    /**
     * Update the specified resource in storage.
     */

     public function fellows(Request $request)
     {
         try {
             $data = $this->validate($request, [
                 'following_user_id' => 'required|exists:users,id',
                 'follower_user_id' => '',
             ]);

             $followingUserId = $request->input('following_user_id');
             $followerUserId = Auth::id();

             // Check if following_user_id and follower_user_id are the same
             if ($followingUserId === $followerUserId) {
                 // Delete the record if it exists
                 FellowsModel::where('following_user_id', $followingUserId)
                             ->where('follower_user_id', $followerUserId)
                             ->delete();

                 return response()->json(['message' => 'Record deleted successfully']);
             }

             // Check if a fellow record already exists with the same following_user_id and follower_user_id
             $existingFellow = FellowsModel::where('following_user_id', $followingUserId)
                                             ->where('follower_user_id', $followerUserId)
                                             ->get()
                                             ->first();

             if ($existingFellow) {
                 $existingFellow->delete();
                 return response()->json(['message' => 'Record deleted successfully']);
             }

             // Create a new Fellow instance and populate it with the validated data
             $fellow = new FellowsModel();
             $fellow->following_user_id = $followingUserId;
             $fellow->follower_user_id = $followerUserId;

             // Save the new fellow to the database
             $fellow->save();

             return response()->json([
                 'message' => 'Fellow stored successfully',
                 'fellow' => $fellow
             ]);

         } catch (Exception $ex) {
             return response()->json(['error' => $ex->getMessage()], 500);
         }
     }


    public function follower_details()
    {
        try {
            $followerDetails = FellowsModel::with(['getUserForFollower'])
            ->where('following_user_id' , Auth::id())
            ->latest()
            ->paginate(10);
            return response()->json($followerDetails);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }



    public function following_details()
    {
     try{
         $followerDetails = FellowsModel::with(['getUserForFollowing'])
         ->where('follower_user_id', Auth::id())
         ->latest()
         ->paginate(10);
         return response()->json($followerDetails);


      } catch(Exception $ex){
         return response()->json(['error'=>$ex->getMessage()]);
     }
     }


    /**
     * Remove the specified resource from storage.
     */
    // public function desginerDashboard()
    // {
    //     $order = OrderModel::;
    // }
}
