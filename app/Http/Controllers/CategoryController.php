<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
      public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CategoryModel::select('*');

            return DataTables::of($data)
                ->addColumn('action', function($row){
                    $editUrl = "/admin/category/edit/$row->id";
                    $deleteUrl = "/admin/category/destroy/$row->id";
                    // $viewUrl = "/admin/category/view/$row->id";
                    $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    // $deleteButton = "<a href='".$deleteUrl."' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    // $viewButton = "<a href='".$viewUrl."' class='btn btn-success btn-sm'><i class='fa fa-eye'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; // Changed the class to btn-danger for delete button
                    $modal = view('category.delete',['id'=>$row->id,'url'=>$deleteUrl]);


                    return $editButton  . "  "  . $deleteButton . $modal  ; // Concatenate both buttons with a line break
                })

                ->editColumn('icon', function($row) {
                    return '
                    <div class="image-container">
                    <img src="'.$row->icon.'" alt="Icon">
                    </div>
                    ';
                })
                ->rawColumns(['icon','action'])
                ->make(true);
        }

        return view('category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    //  Store function of category
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'category' => 'required',
                'description' => 'required',
                'icon' => 'required', // Adjust file types and size as per your requirements
            ]);

            // Store icon
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $iconName = time().'.'.$icon->getClientOriginalExtension();
                $path = public_path('/images');
                $icon->move($path, $iconName);
            } else {
                throw new \Exception("icon not provided.");
            }


            // Save to database
            $category = new CategoryModel();
            $category->category = $request->input('category');
            $category->description = $request->input('description');
            $category->icon = $iconName; // Assuming 'icon' is the column name in your category table
            $category->save();

            return back()->with('success','category added Successfully..');

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
        $category = CategoryModel::findorfail($id); // Retrieve the category record using first()
        $users = User::where('account_status',200)->get();
        return view('category.edit', compact('category','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'category' => 'required',
                'description' => 'required',
                'icon' => 'required', // Adjust file types and size as per your requirements
            ]);

            // Find the user by ID
            $category = CategoryModel::findOrFail($id);

                  // Store icon
                  if ($request->hasFile('icon')) {
                    $icon = $request->file('icon');
                    $iconName = time().'.'.$icon->getClientOriginalExtension();
                    $path = public_path('/images');
                    $icon->move($path, $iconName);
                    $iconName = '/images/' . $iconName;
                } else {
                    throw new \Exception("icon not provided.");
                }

            // Update category details
            $category->category = $request->input('category');
            $category->description = $request->input('description');
            $category->icon = $iconName;
            $category->save();

            return back()->with('success', 'category updated successfully');

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
            $category = CategoryModel::findOrFail($id);
            $category->delete();
            return back()->with('success', 'Category delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete Category: '.$e->getMessage());
        }
    }
}
