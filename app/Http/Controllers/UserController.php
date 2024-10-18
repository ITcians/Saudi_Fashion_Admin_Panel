<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Yajra\DataTables\Services\DataTable;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\alert;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');

            return DataTables::of($data)
                ->addColumn('account_status', function($row) {
                    $statusText = ($row->account_status == 200) ? 'Enable' : 'Disable';
                    $btnClass = ($row->account_status == 200) ? 'success' : 'danger';
                    return '<td>
                        <a href="/admin/update-status/'.$row->id.'" class="btn btn-sm btn-'.$btnClass.'">
                            '.$statusText.'
                        </a>
                    </td>';


                })
                ->addColumn('action', function($row){
                    $editUrl = "/admin/user/edit/$row->id";
                    $deleteUrl = "/admin/user/destroy/$row->id"; // Removed the space before $row->id
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('users.delete',['id'=>$row->id,'url'=>$deleteUrl]);
                    return $editButton . "  " . $deleteButton . $modal; // Concatenate both buttons with a line break
                })
                ->rawColumns(['account_status', 'action'])
                ->make(true);
        }

        return view('users.index');
    }


    // change the account_status of user

    public function updateaccount_status($id) {
        $user = User::find($id);
        if ($user) {
            if ($user->account_status == 200) {
                $user->account_status = 403;
            }else{
                $user->account_status = 200;

            }
            $user->save();
        }
        return back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'fullname' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required',
            'image' => 'required', // Adjust file types and size as per your requirements
        ]);
        try {

            // Store image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $path = public_path('/upload_image');
                $image->move($path, $imageName);
            } else {
                throw new \Exception("Image not provided.");
            }
                 // Hash password
                 $hashedPassword = Hash::make($request->input('password'));


            // Save to database
            $user = new User();
            $user->fullname = $request->input('fullname');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->password = $hashedPassword;
            $user->image = $imageName; // Assuming 'image' is the column name in your users table
            $user->save();

            return back()->with('success','User added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
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
        $user = User::where('id', $id)->first(); // Retrieve the user record using first()
        return view('users.edit', compact('user')); // Pass 'user' as an associative array to compact
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'fullname' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'password' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file types and size as per your requirements
            ]);

            // Find the user by ID
            $user = User::findOrFail($id);

          // Update image if provided
if ($request->hasFile('image')) {
    $image = $request->file('image');
    $imageName = time().'.'.$image->getClientOriginalExtension();
    $path = public_path('/upload_image');
    $image->move($path, $imageName);

    // Delete old image if it exists
    if ($user->image && file_exists(public_path('upload_image/' . $user->image))) {
        unlink(public_path('upload_image/' . $user->image));
    }

    $user->image = $imageName;
}

            // Update user details
            $user->fullname = $request->input('fullname');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->password = Hash::make($request->input('password')); // Hash the password before saving

            $user->save();

            return back()->with('success', 'User updated successfully');

        } catch (\Throwable $th) {
            return back()->with('danger', $th->getMessage());
            // Optionally, you can also return back with input and errors
            // return back()->withInput()->withErrors(['error'=> $th->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return back()->with('success', 'User delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete user: '.$e->getMessage());
        }
    }



}
