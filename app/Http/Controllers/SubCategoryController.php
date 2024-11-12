<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SubCategoryModel::select('*');

            return DataTables::of($data)
            ->editColumn('category_id',function($row){
                return CategoryModel::find($row->category_id)->category;
            })
                ->addColumn('action', function($row){
                    $editUrl = "/admin/subcategory/edit/$row->id";
                    $deleteUrl = "/admin/subcategory/destroy/$row->id"; // Removed the space before $row->id
                    // $viewUrl = "/admin/subcategory/view/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    // $viewButton = "<a href='".$viewUrl."' class='btn btn-success btn-sm'><i class='fa fa-eye'></i></a>";
                    $modal = view('subcategory.delete',['id'=>$row->id,'url'=>$deleteUrl]);

                    return $editButton . "  " . $deleteButton . "  " . $modal  ; // Concatenate both buttons with a line break
                })->rawColumns(['category_id','action'])

                ->make(true);
        }

        return view('subcategory.index');
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
                'sub_category' => 'required',
                'category_id' => 'required',
            ]);



            // Save to database
            $subcategory = new SubCategoryModel();
            $subcategory->sub_category = $request->input('sub_category');
            $subcategory->category_id = $request->input('category_id');
            $subcategory->save();

            return back()->with('success','subcategory added Successfully..');

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
        $subcategory = SubCategoryModel::findOrFail($id);
        // Fetch all categories
        $categories = CategoryModel::all();

        return view('subcategory.edit',compact('subcategory','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'sub_category' => 'required',
                'category_id' => 'required',
            ]);

            // Find the user by ID
            $subcategory = SubCategoryModel::findOrFail($id);

            // Update subcatego$subcategory details
            $subcategory->sub_category = $request->input('sub_category');
            $subcategory->category_id = $request->input('category_id');
            $subcategory->save();

            return back()->with('success', 'Sub Category updated successfully');

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
            $subcategory = SubCategoryModel::findOrFail($id);
            $subcategory->delete();
            return back()->with('success', 'SubCategory delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete SubCategory: '.$e->getMessage());
        }
    }
}
