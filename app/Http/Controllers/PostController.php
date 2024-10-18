<?php

namespace App\Http\Controllers;

use App\Models\FlagPostModel;
use App\Models\PostCommentModel;
use App\Models\PostModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PostModel::select('*');

            return DataTables::of($data)
            ->editColumn('created_by', function ($row) {
                return User::find($row->created_by)->username;
            })
                ->addColumn('status', function($row) {
                    $statusText = ($row->status == 200) ? 'Enable' : 'Disable';
                    $btnClass = ($row->status == 200) ? 'success' : 'danger';
                    return '<td>
                        <a href="/admin/post/update-status/'.$row->id.'" class="btn btn-sm btn-'.$btnClass.'">
                            '.$statusText.'
                        </a>
                    </td>'
                    ;

                })
                ->addColumn('action', function($row){
                    $editUrl = "/admin/post/edit/$row->id";
                    $deleteUrl = "/admin/post/destroy/$row->id";
                    $flagUrl = "/admin/post/flag/edit/$row->id";
                    $viewUrl = "/admin/post/view/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a href='".$deleteUrl."' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $flagButton = "<a href='".$flagUrl."' class='btn btn-success btn-sm'><i class='fa fa-flag'></i></a>";
                    $viewButton = "<a href='".$viewUrl."' class='btn btn-primary btn-sm'><i class='fa fa-eye'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('posts.delete',['id'=>$row->id,'url'=>$deleteUrl]);

                    return $editButton . "  " . $flagButton . "  " . $viewButton . "  "  . $deleteButton . $modal;
                })
                ->rawColumns(['created_by', 'event_status', 'action'])
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('posts.index');
    }


    // Change the status of Post
    public function updateStatus($id) {
        $post = PostModel::find($id);
        if ($post) {
            if ($post->status == 200) {
                $post->status = 403;
                $post->save();
            }
        }
        return back();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'post' => 'required',
                'cover' => 'required',// Adjust file types and size as per your requirements
                'allow_comments' => 'required',
                'is_drafted' => 'required',
                'visibiliy' => 'required',
                'created_by' => 'required',
            ]);

            // Store cover
            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = time().'.'.$cover->getClientOriginalExtension();
                $path = public_path('/upload_image');
                $cover->move($path, $coverName);
            } else {
                throw new \Exception("cover not provided.");
            }

            // Save to database
            $post = new PostModel();
            $post->post = $request->input('post');
            $post->cover = $coverName; // Assuming 'cover' is the column name in your posts table
            $post->allow_comments = $request->input('allow_comments');
            $post->visibiliy = $request->input('visibiliy');
            $post->is_drafted = $request->input('is_drafted');
            $post->created_by = $request->input('created_by');


            $post->save();

            return back()->with('success','Post added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['danger' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function view(string $id)
    {
        $comments = PostCommentModel::where('post_id',$id)->paginate(10);
        $post = PostModel::where('id', $id)->first(); // Retrieve the post record using first()
        $users = User::where('account_status',200)->get();
        $flags = FlagPostModel::where('post_id',$id)->paginate(10);
        return view('posts.view', compact('post','users', 'flags','comments')); // Pass 'post' as an associative array to compact
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = PostModel::where('id', $id)->first(); // Retrieve the post record using first()
        $users = User::where('account_status',200)->get();
        return view('posts.edit', compact('post','users')); // Pass 'post' as an associative array to compact
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'post' => 'required',
                'cover' => 'required',// Adjust file types and size as per your requirements
                'allow_comments' => 'required',
                'is_drafted' => 'required',
                'created_by' => 'required',
            ]);

            // Find the user by ID
            $post = PostModel::findOrFail($id);

          // Update cover if provided
            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = time().'.'.$cover->getClientOriginalExtension();
                $path = public_path('/upload_image');
                $cover->move($path, $coverName);

                // Delete old cover if it exists
                if ($post->cover && file_exists(public_path('upload_image/' . $post->cover))) {
                    unlink(public_path('upload_image/' . $post->cover));
                }

                $post->cover = $coverName;
            }

            // Update post details
            $post->post = $request->input('post');
            $post->allow_comments = $request->input('allow_comments');
            $post->visibiliy = $request->input('visibiliy');
            $post->is_drafted = $request->input('is_drafted');
            $post->created_by = $request->input('created_by');

            $post->save();

            return back()->with('success', 'post updated successfully');

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
            $Post = PostModel::findOrFail($id);
            $Post->delete();
            return back()->with('success', 'Post delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete Post: '.$e->getMessage());
        }
    }

}
