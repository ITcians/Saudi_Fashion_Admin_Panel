<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\ProductSizeModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class ProductSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductSizeModel::select('*');

            return DataTables::of($data)
            ->editColumn('product_id',function($row){
                return ProductModel::find($row->product_id)->title;
            })
                ->addColumn('action', function($row){
                    $editUrl = "/admin/productsize/edit/$row->id";
                    $deleteUrl = "/admin/productsize/destroy/$row->id"; // Removed the space before $row->id
                    // $viewUrl = "/admin/productsize/view/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('events.delete',['id'=>$row->id,'url'=>$deleteUrl]);

                    return $editButton  . "  "  . $deleteButton . $modal ; // Concatenate both buttons with a line break
                })
                ->rawColumns(['product_id','action'])
                ->make(true);
        }

        return view('productsize.index');
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
                'size' => 'required',
                'product_id' => 'required',
            ]);



            // Save to database
            $ProductSize = new ProductSizeModel();
            $ProductSize->size = $request->input('size');
            $ProductSize->product_id = $request->input('product_id');
            $ProductSize->save();

            return back()->with('success','ProductSize added Successfully..');

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
        $productsize = ProductSizeModel::findorfail($id);

        $products = ProductModel::all();

        return view('productsize.edit',compact('productsize','products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $this->validate($request,[
                'size'=> 'required',
                'product_id' => 'required'
            ]);


            $productsize = ProductSizeModel::findorfail($id);

            $productsize->size = $request->size;
            $productsize->product_id = $request->product_id;

            $productsize->save();

            return back()->with('success' , 'Product Size update successfully');
        } catch (\Throwable $th) {
            return back()->with('error' , $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ProductSize = ProductSizeModel::findOrFail($id);
            $ProductSize->delete();
            return back()->with('success', 'ProductSize delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete Category: '.$e->getMessage());
        }
    }
}
