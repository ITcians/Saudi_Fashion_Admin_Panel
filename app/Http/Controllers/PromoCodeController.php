<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PromoCode::select('*');
            return DataTables::of($data)
            ->addColumn('action',function ($row) {
                $deleteUrl = "/admin/promocode/destroy/$row->id";
                $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>";
                $modal = view('promocode.delete', ['id' => $row->id, 'url' => $deleteUrl]);
                return  $deleteButton . $modal;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('promocode.index');
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
        $this->validate($request,[
            'promo_code' => 'required',
            'percentage'=>'required',
        ]);

        $promocode = new PromoCode();
        $promocode->promo_code = $request->promo_code;
        $promocode->percentage = $request->percentage;
        $promocode->save();

        return back()->with('success','Promo Code Added Succesfully!');
    
    } catch (Exception $ex) {
        return back()->with('danger',$ex->getMessage());
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
    public function getPromo(Request $request)
    {
        try {
            $this->validate($request,[
                'promo_code' => 'required',
            ]);
            $dis = PromoCode::where('promo_code',$request->promo_code)->first()->get();
            if ($dis->isNotEmpty()) {
                return response()->json($dis);
            } else {
                return response()->json(['message' =>'Your Promo Code is Incorrect!']);
            }
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $promocode = PromoCode::findOrFail($id);
            $promocode->delete();
            return back()->with('success', 'Promo Code delete successfully');
        } catch (Exception $ex) {
            return back()->with('danger', 'Failed to delete Color: '.$ex->getMessage());
        }
    }
}
