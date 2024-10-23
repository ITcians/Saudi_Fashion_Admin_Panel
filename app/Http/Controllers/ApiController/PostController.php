<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\FellowsModel;
use App\Models\HashtagModel;
use App\Models\PostMediaModel;
use App\Models\PostModel;
use App\Models\PostReaction;
use App\Models\PostSaves;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
class PostController extends Controller
{

    protected $res;
    function __construct() 
    {
        $this->res = new stdClass();
    }


    function reaction_count($id){
       return [
       'PostReactionCount' => PostReaction::where('post_id',$id)
       ->get()
       ->count()
       ];
    }



    function storeHashtag(Request $request){
        try{
            $this->validate($request,[
                'hashtag'=>"required"
            ]);
            HashtagModel::create([
                'hashtag'=>$request->hashtag,

            ]);
        }catch(\Exception $ex){
            return response()->json(['error'=>$ex->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostModel::select("*")
        ->with(['createdBy'])
        ->paginate(10);
    }

    public function getFollowingPost()
    {
        $followerDetails = FellowsModel::where('follower_user_id', Auth::id())
             ->get()
             ->pluck('following_user_id');
        
        $posts = PostModel::whereIn('created_by', $followerDetails)
            ->with(['createdBy'])
            ->latest()
            ->paginate(10);
        
        return response()->json($posts);
    }
    
    public function TrendingPost(Request $request) {
        $posts = PostReaction::select('post_id')
                             ->groupBy('post_id')
                             ->get()
                             ->mapWithKeys(function ($item) {
                                 return [$item->post_id => PostReaction::where('post_id', $item->post_id)->count()];
                             });

        //the posts are sorted in descending order based on the number of reactions and then only the post_id keys are get.
        $highestPostID = $posts->sortDesc()->keys();
      
        if ($request->input('search')) {
            $posts = PostModel::whereIn('created_by', $highestPostID)
            ->with(['createdBy'])
            ->latest()
            ->orWhere('post','like','%'.$request->search.'%')
            ->paginate(10);
        } else {
            $posts = PostModel::whereIn('created_by', $highestPostID)
            ->with(['createdBy'])
            ->latest()
            ->paginate(10);
        }
    
        return $posts;
        // return [
        //     'post_id' => $highestPostID,
        //     'post' => $posts
        // ];
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function searchHashtag(Request $request)
    {
        if ($request->input('search')) {
            //search
            return HashtagModel::where('hashtag', 'like', '%' . $request->search . '%')
                ->orderBy('hashtag')
                ->get();
        }
    }

    public function postReaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'post_id'  => 'required',
                'user_id'  => 'required',
                'reaction' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            $data = [
                'post_id'  => $request->input('post_id'),
                'user_id'  => $request->input('user_id'),
                'reaction' => $request->input('reaction'),
            ];
    
            $postReaction = PostReaction::create($data);
    
            return response()->json([
                'message' => 'Post Reaction added successfully',
                'PostReaction' => $postReaction,
            ]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          // Check if the authenticated user's account_type is 1
          if (auth()->user()->account_type == 2) {
            return response()->json(['error' => 'Unauthorized. Only users with account_type Desginer can update posts.'], 403);
        }
        try {
            $validator = Validator::make($request->all(), [
                'post' => 'required',
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif',
          //      'cover' => 'required', // Assuming cover is an image upload field
                'allow_comments' => 'required|numeric',
                'visibiliy' => 'required|numeric', // Fixed typo: visibiliy to visibili
                'hashtag' => '',
                'videos.*' => 'file|mimes:mp4,avi,mov,wmv|max:10240', // Adjusted video validation rules
                'mention' => ''
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $profilecoverName = '';
            if ($request->hasFile('cover')) {
                $profilecover = $request->file('cover');
                $profilecoverName = '/upload_images/' . time() . '.' . $profilecover->getClientOriginalExtension();
                $profilecover->move(public_path('upload_images'), $profilecoverName);
            }

            $postData = [
                'post' => $request->post,
                'cover' => $profilecoverName,
                'allow_comments' => $request->allow_comments,
                'visibiliy' => $request->visibiliy, // Fixed typo: visibiliy to visibiliy
                'created_by' => Auth::id(),
            ];

            $post = PostModel::create($postData);

            $createdHashtags = '';

            $createdHashtags = [];
            if ($request->has('hashtag')) {
                $hashtagsString = $request->hashtag;

                // Split the input string by space and remove '#' character from each hashtag
                $hashtags = array_map(function($hashtag) {
                    return trim(trim($hashtag), '#');
                }, explode(' ', $hashtagsString));

                // Remove empty hashtags
                $hashtags = array_filter($hashtags);

                // Check for duplicate hashtags
                $duplicateHashtags = array_diff_assoc($hashtags, array_unique($hashtags));

                if (!empty($duplicateHashtags)) {
                    return response()->json(['error' => 'Duplicate hashtags found: ' . implode(', ', $duplicateHashtags)], 400);
                }

                // Save each hashtag in a separate row in the HashtagModel table
                foreach ($hashtags as $hashtag) {
                    $hashtagModel = new HashtagModel();
                    $hashtagModel->hashtag = $hashtag;
                    $hashtagModel->post_id = $post->id;
                    $hashtagModel->save();
                }

                // Concatenate hashtags into a single string separated by commas
                $createdHashtags = implode(', ', $hashtags);

                // Save all hashtags in array form
                $createdHashtags = $hashtags;

                // Save the array of hashtags in the database as JSON
                $hashtagModel = new HashtagModel();
                $hashtagModel->hashtag = json_encode($createdHashtags);
                $hashtagModel->post_id = $post->id;
                $hashtagModel->save();
            }

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $videoFile) {
                    // Check if the file is actually uploaded
                    if ($videoFile->isValid()) {
                        $videoName = 'video_' . time() . '_' . $videoFile->getClientOriginalName();
                        $videoFile->move(public_path('upload_videos'), $videoName);

                        $postData = [
                            'media' => $videoName,
                            'type' => 'video',
                            'post_id' => $post->id,
                        ];

                        PostMediaModel::create($postData);
                    }
                }
            }

            return response()->json(['message' => 'Post created successfully', 'hashtags' => $createdHashtags], 200);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Check if the authenticated user's account_type is 1
            if (auth()->user()->account_type !== 1) {
                return response()->json(['error' => 'Unauthorized. Only users with account_type Desginer can update posts.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'post' => 'required',
                'cover' => '',
                'allow_comments' => 'required|numeric',
                'visibiliy' => 'required|numeric', // Fixed typo: visibiliy to visibiliy
                'hashtag' => '',
                'videos.*' => 'file|mimes:mp4,avi,mov,wmv|max:10240', // Adjusted video validation rules
                'mention' => ''
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $post = PostModel::findOrFail($id);

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = 'post_' . time() . '.' . $cover->getClientOriginalExtension();
                $cover->move(public_path('upload_images'), $coverName);
                $post->cover = $coverName;
            }

            $post->post = $request->post;
            $post->allow_comments = $request->allow_comments;
            $post->visibiliy = $request->visibiliy; // Fixed typo: visibiliy to visibiliy
            $post->save();

            $createdHashtags = [];
            if ($request->has('hashtag')) {
                $hashtagsString = $request->hashtag;
                // Split the input string by space and remove '#' character from each hashtag
                $hashtags = array_map(function($hashtag) {
                    return trim(trim($hashtag), '#');
                }, explode(' ', $hashtagsString));

                // Remove empty hashtags
                $hashtags = array_filter($hashtags);

                // Check for duplicate hashtags
                $duplicateHashtags = array_diff_assoc($hashtags, array_unique($hashtags));

                if (!empty($duplicateHashtags)) {
                    return response()->json(['error' => 'Duplicate hashtags found: ' . implode(', ', $duplicateHashtags)], 400);
                }

                // Save all hashtags in array form
                $createdHashtags = $hashtags;

                // Save the array of hashtags in the database as JSON
                $hashtagModel = HashtagModel::where('post_id', $id)->first();
                if (!$hashtagModel) {
                    $hashtagModel = new HashtagModel();
                }
                $hashtagModel->hashtag = json_encode($createdHashtags);
                $hashtagModel->post_id = $post->id;
                $hashtagModel->save();
            }

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $videoFile) {
                    // Check if the file is actually uploaded
                    if ($videoFile->isValid()) {
                        $videoName = 'video_' . time() . '_' . $videoFile->getClientOriginalName();
                        $videoFile->move(public_path('upload_videos'), $videoName);

                        $postData = [
                            'media' => $videoName,
                            'type' => 'video',
                            'post_id' => $post->id,
                        ];

                        PostMediaModel::create($postData);
                    }
                }
            }

            return response()->json(['message' => 'Post updated successfully', 'hashtags' => $createdHashtags], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->user();

            // Check if the account_status is 1
            if ($user->account_type == 1) {
                $post = PostModel::findOrFail($id);

                // Delete associated hashtags
                HashtagModel::where('post_id', $id)->delete();

                // Delete associated media files
                PostMediaModel::where('post_id', $id)->delete();

                // Delete the post
                $post->delete();

                return response()->json(['message' => 'Post deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'User account is not a desginer'], 403);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function postAccordingToDesginerId(string $id)
    {
        return PostModel::where('created_by',$id)->select("*")
        ->paginate(10);
    }

    public function storeComment(Request $request){
        try {
            $this->validate($request,[
                'comment' => 'required',
                'post_id' => 'required',
            ]);

            Comment::create([
                'user_id' => Auth::id(),
                'post_id' => $request->post_id,
                'comment' => $request->comment,
            ]);

            $this->res->message = 'Comment added Successfully!';
        
        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally { 
            return $this->res;
        }
    }

    public function getComment(string $postId)
    {
        $comment = Comment::where('post_id',$postId)->with('User')->latest()->paginate(10);
        return response()->json($comment);
    }

    public function storePostSaves(Request $request)
    {
        try {
            $this->validate($request,[
                'post_id' => 'required',
            ]);

            PostSaves::create([
                'post_id' => $request->post_id,
                'user_id'=>Auth::id(),
            ]);

            $this->res->message = 'Post Saves Successfully!';
        } catch (   Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return $this->res;
        }
    }

}
