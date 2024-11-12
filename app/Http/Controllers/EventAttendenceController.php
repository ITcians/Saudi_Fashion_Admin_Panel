<?php

namespace App\Http\Controllers;

use App\Models\EventAttendenceModel;
use App\Models\EventsModel;
use App\Models\User;
use Database\Seeders\EventAttendence;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class EventAttendenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EventAttendenceModel::select('*');

            return DataTables::of($data)
            ->editColumn('user_id',function($row){
                return User::find($row->user_id)->username;
            })
            ->editColumn('event_id',function($row){
                return EventsModel::find($row->event_id)->event_name;
            })
                ->addColumn('action', function($row){
                    $editUrl = "/admin/eventattendence/edit/$row->id";
                    $deleteUrl = "/admin/eventattendence/destroy/$row->id"; // Removed the space before $row->id
                    // $viewUrl = "/admin/eventattendence/view/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('eventattendence.delete',['id'=>$row->id,'url'=>$deleteUrl]);

                    return $editButton . "  "  . $deleteButton . $modal; // Concatenate both buttons with a line break
                })->rawColumns(['user_id','action'])
                ->rawColumns(['event_id','action'])

                ->make(true);
        }

        return view('eventattendence.index');
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
                'user_id' => 'required',
                'status' => 'required',
                'event_id' => 'required',
            ]);



            // Save to database
            $EventAttendence = new EventAttendenceModel();
            $EventAttendence->user_id = $request->input('user_id');
            $EventAttendence->status = $request->input('status');
            $EventAttendence->event_id = $request->input('event_id');
            $EventAttendence->save();

            return back()->with('success','Event Attendence added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['danger' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $users = User::all();

        $eventattendence = EventAttendenceModel::findorfail($id);

        $events = EventsModel::all();

        return view('eventattendence.edit', compact('users', 'eventattendence' , 'events'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::all();

        $eventattendence = EventAttendenceModel::findorfail($id);

        $events = EventsModel::all();

        return view('eventattendence.edit', compact('users', 'eventattendence' , 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $this->validate($request ,[
                'user_id',
                'status',
                'event_id',
            ]);

            // Find the event by ID
            $eventattendence = EventAttendenceModel::findOrFail($id);

            $eventattendence->user_id = $request->user_id;
            $eventattendence->status = $request->status;
            $eventattendence->event_id = $request->event_id;
            $eventattendence->save();

            return back()->with('success' , 'Event Attendence updated successfuly');

        } catch (\Throwable $th) {
            return back()->with('error' , $th->getMessage() );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $EventAttendence = EventAttendenceModel::findOrFail($id);
            $EventAttendence->delete();
            return back()->with('success', 'EventAttendence delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete SubCategory: '.$e->getMessage());
        }
    }
}
