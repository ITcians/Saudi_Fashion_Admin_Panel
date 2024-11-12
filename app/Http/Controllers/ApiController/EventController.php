<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\EventAttendenceModel;
use App\Models\EventsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->input('fetch-mine') == 1) {
            //get my events
            return $this->getMyEvents();
        }
        if ($request->input('search')) {
            return EventsModel::where('event_name', 'like', '%' . $request->search . '%')
                ->orWhere('event_description', 'like', '%' . $request->search . '%')
                ->where('event_status', 1)
                ->with(['attendies'])
                ->paginate(10);
        }
        //Latest events

        return EventsModel::where('event_status', 200)
            ->with(['attendies'])
            ->latest()
            ->paginate(10);
    }

    function getMyEvents()
    {
        return EventsModel::where(['created_by' => Auth::id()])
            ->with(['attendies'])
            ->paginate(10);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function joinEvent(Request $request)
    {
        try {
            $data = $this->validate($request, [
                'event_id' => 'required|exists:events,id',
                'status' => 'required',
            ]);

            $eventId = $request->input('event_id');
            $status = $request->input('status');
            $userId = Auth::id();

            // Check if the user is already joined the event
            $existingJoin = EventAttendenceModel::where('user_id', $userId)
                                      ->where('event_id', $eventId)
                                      ->first();

            if ($existingJoin) {
                $existingJoin->status = $status;
                $existingJoin->save();

                return response()->json(['message' => 'User '.$status.' the event']);
            }

            // Create a new attendance record
            $attendance = new EventAttendenceModel();
            $attendance->user_id = $userId;
            $attendance->event_id = $eventId;
            $attendance->status = $status;
            $attendance->save();

            return response()->json([
                'message' => 'User '.$status.' the event successfully',
            ]);

        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function deleteJoin($id)
    {
        try {
            $userId = Auth::id();

            $event = EventAttendenceModel::findOrFail($id);

            if ($event->user_id !== $userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $event->delete();

            return response()->json(['message' => 'User deleted the event join']);
        } catch (Exception $ex) {
            return response()->json(['error' => 'Event attendance not found'], 404);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if the user account type is 1
        if (!$user->account_type == 1) {
            return response()->json(['error' => 'Unauthorized. Only users with account_type Desginer can update posts.'], 403);
        }
        try {
            $validator = Validator::make($request->all(), [
                'event_name' => 'required',
                'event_date' => 'required',
                'event_hour' => 'required',
                'event_description' => 'required',
                'cover_image' => 'required|file' // Adjust validation rules as needed
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $AuthId = Auth::id();
            // Get the cover image path from the request
            $coverImage = $request->file('cover_image');

            // Generate a unique file name
            $imageName = time() . '.png' ;

            // Move the uploaded file to a public directory
            $coverImage->move(public_path('upload_images'), $imageName);
            $eventData = [
                'event_name' => $request->event_name,
                'event_date' => $request->event_date,
                'event_hour' => $request->event_hour,
                'event_description' => $request->event_description,
                'cover_image' => "/upload_images/" . $imageName,
                'created_by' => $AuthId,
            ];

            $event = EventsModel::create($eventData);

            return response()->json([
                'message' => 'Event Submitted Successfully',
                'event' => $event
            ]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function updateCoverImage(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif' // Adjust validation rules as needed
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Find event data associated with the provided event ID
            $event = EventsModel::findOrFail($id);

            // Get the cover image path from the request
            $coverImage = $request->file('cover_image');

            // Generate a unique file name
            $imageName = time() . '.' . $coverImage->extension();

            // Move the uploaded file to a public directory
            $coverImage->move(public_path('upload_images'), $imageName);

            // Update event data with the new cover image path
            $event->update([
                'cover_image' => '/upload_images/' . $imageName // Update the field name accordingly
            ]);

            return response()->json(['message' => 'Event cover image updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
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
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Check if the user account type is 1
            if ($user->account_type == 1) {
                $event = EventsModel::findorfail($id);

                if (!$event) {
                    return response()->json(['error' => 'Event not found'], 404);
                }

                $validator = Validator::make($request->all(), [
                    'event_name' => 'required',
                    'event_date' => 'required',
                    'event_hour' => 'required',
                    'event_description' => 'required',
                    'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024' // Adjust validation rules as needed
                ]);

                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 422);
                }

                // Update event data
                $event->event_name = $request->event_name;
                $event->event_date = $request->event_date;
                $event->event_hour = $request->event_hour;
                $event->event_description = $request->event_description;

                // Check if cover image is provided
                if ($request->hasFile('cover_image')) {
                    // Get the cover image from the request
                    $coverImage = $request->file('cover_image');

                    // Generate a unique file name
                    $imageName = time() . '.' . $coverImage->getClientOriginalExtension();

                    // Move the uploaded file to a public directory
                    $coverImage->move(public_path('upload_images'), $imageName);
                    $event->cover_image = "upload_images/" . $imageName;
                }

                $event->save();

                return response()->json([
                    'message' => 'Event updated successfully',
                    'event' => $event
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the event by ID
            $event = EventsModel::findOrFail($id);

            // Check if the authenticated user's account_type is 1 (assuming account_type is a field in the users table)
            $user = Auth::user();
            if ($user->account_type != 1) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Delete the event
            $event->delete();

            return response()->json(['message' => 'Event deleted successfully']);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

}
