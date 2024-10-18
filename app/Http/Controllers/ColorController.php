<?php

namespace App\Http\Controllers;

use App\Models\ColorModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ColorModel::select('*');
            
            return DataTables::of($data)
                ->editColumn('product_id', function($row) {
                    return ProductModel::find($row->product_id)->title;
                })
                ->addColumn('action', function($row) {
                    $editUrl = "/admin/color/edit/$row->id";
                    $deleteUrl = "/admin/color/destroy/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>";
                    $modal = view('color.delete', ['id' => $row->id, 'url' => $deleteUrl]);
    
                    return $editButton . "  " . $deleteButton . $modal;
                })
                ->rawColumns(['product_id', 'action'])
                ->make(true);
        }
    
        return view('color.index');
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
                'color_name' => 'required',
                'color_code' => 'required',
                'product_id' => 'required', // Adjust file types and size as per your requirements
            ]);


            // Save to database
            $Color = new ColorModel();
            $Color->color_name = $request->input('color_name');
            $Color->color_code = $request->input('color_code');
            $Color->product_id = $request->input('product_id');
            $Color->save();

            return back()->with('success','Color added Successfully..');

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
         // Fetch the subcategory you want to edit
        $color = ColorModel::findOrFail($id);

        $product = ProductModel::all();

        return view('color.edit',compact('color','product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'color_name' => 'required',
                'color_code' => 'required',
                'product_id' => 'required',
            ]);

            // Find the user by ID
            $color = ColorModel::findOrFail($id);

            // Update subcatego$color details
            $color->color_name = $request->input('color_name');
            $color->color_code = $request->input('color_code');
            $color->product_id = $request->input('product_id');
            $color->save();

            return back()->with('success', 'Color updated successfully');

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
            $Color = ColorModel::findOrFail($id);
            $Color->delete();
            return back()->with('success', 'Color delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete Color: '.$e->getMessage());
        }
    }
}
