<?php

namespace App\Http\Controllers;

use App\Models\EventsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EventsModel::select('*');

            return DataTables::of($data)
                ->editColumn('created_by', function ($row) {
                    return User::find($row->created_by)->username;
                })
                ->addColumn('event_status', function ($row) {
                    $statusText = ($row->event_status == 200) ? 'Enable' : 'Disable';
                    $btnClass = ($row->event_status == 200) ? 'success' : 'danger';
                    return '<a href="/admin/event/update-status/' . $row->id . '" class="btn btn-sm btn-' . $btnClass . '">' . $statusText . '</a>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = "/admin/event/edit/$row->id";
                    $deleteUrl = "/admin/event/destroy/$row->id"; // Removed the space before $row->id
                    $viewUrl = "/admin/event/view/$row->id";
                    $editButton = "<a href='" . $editUrl . "' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('events.delete',['id'=>$row->id,'url'=>$deleteUrl]);
                    $viewButton = "<a href='" . $viewUrl . "' class='btn btn-success btn-sm'><i class='fa fa-eye'></i></a>";

                    return $editButton . "  " . $viewButton . "  "  . $deleteButton . $modal; // Concatenate both buttons with a line break
                })
                ->rawColumns(['created_by', 'event_status', 'action'])
                ->make(true);
        }

        return view('events.index');
    }



    // change the event_status of Event

    public function updateevent_status($id) {
        $event = EventsModel::find($id);
        if ($event) {
            if ($event->event_status == 200) {
                $event->event_status = 403;
                $event->save();
            }
        }
        return back();
    }


    //  Store function of event
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'event_name' => 'required',
                'event_date' => 'required',
                'event_description' => 'required',
                'event_hour' => 'required',
                'cover_image' => 'required', // Adjust file types and size as per your requirements
                'created_by' => 'required',
            ]);

            // Store cover_image
            if ($request->hasFile('cover_image')) {
                $cover_image = $request->file('cover_image');
                $cover_imageName = time().'.'.$cover_image->getClientOriginalExtension();
                $path = public_path('/upload_cover_image');
                $cover_image->move($path, $cover_imageName);
            } else {
                throw new \Exception("cover_image not provided.");
            }


            // Save to database
            $Event = new EventsModel();
            $Event->event_name = $request->input('event_name');
            $Event->event_date = $request->input('event_date');
            $Event->event_description = $request->input('event_description');
            $Event->event_hour = $request->input('event_hour');
            $Event->cover_image = $cover_imageName; // Assuming 'cover_image' is the column name in your Event table
            $Event->created_by = $request->input('created_by');
            $Event->save();

            return back()->with('success','Event added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['danger' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function view(string $id)
    {
        $event = EventsModel::where('id', $id)->first(); // Retrieve the post record using first()
        $users = User::where('account_status',200)->get();
        return view('events.view', compact('event','users')); // Pass 'post' as an associative array to compact
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = EventsModel::where('id', $id)->get()->first(); 
        $users = User::where('account_status',200)->get();
        return view('events.edit', compact('event','users')); // Pass 'event' as an associative array to compact
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'event_name' => 'required',
                'event_date' => 'required',
                'event_description' => 'required',
                'event_hour' => 'required',
                'cover_image' => 'required', // Adjust file types and size as per your requirements
                'created_by' => 'required',
            ]);

            // Find the event by ID
            $event = EventsModel::findOrFail($id);

          // Update cover_image if provided
            if ($request->hasFile('cover_image')) {
                $cover_image = $request->file('cover_image');
                $cover_imageName = time().'.'.$cover_image->getClientOriginalExtension();
                $path = public_path('/upload_cover_image');
                $cover_image->move($path, $cover_imageName);

                // Delete old cover_image if it exists
                if ($event->cover_image && file_exists(public_path('upload_cover_image/' . $event->cover_image))) {
                    unlink(public_path('upload_cover_image/' . $event->cover_image));
                }

                $event->cover_image = $cover_imageName;
            }

            // Update event details
            $event->event_name = $request->input('event_name');
            $event->event_date = $request->input('event_date');
            $event->event_description = $request->input('event_description');
            $event->event_hour = $request->input('event_hour');
            $event->created_by = $request->input('created_by');

            $event->save();

            return back()->with('success', 'event updated successfully');

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
            $Event = EventsModel::findOrFail($id);
            $Event->delete();
            return back()->with('success', 'Event delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete Event: '.$e->getMessage());
        }
    }

}
