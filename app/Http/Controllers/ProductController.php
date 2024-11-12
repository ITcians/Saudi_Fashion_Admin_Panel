<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Models\CategoryModel;
use App\Models\ColorModel;
use App\Models\ProductSizeModel;
use Faker\Core\Color;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductModel::select('*');

            return DataTables::of($data)
            ->editColumn('created_by',function($row){
                return User::find($row->created_by)->username;
            })
            ->editColumn('category_id',function($row){
                return CategoryModel::find($row->category_id)->category;
            })
            ->editColumn('sub_category_id',function($row){
                return SubCategoryModel::find($row->sub_category_id)->sub_category;
            })
            ->addColumn('status', function($row) {
                $statusText = ($row->status == 200) ? 'Enable' : 'Disable';
                $btnClass = ($row->status == 200) ? 'success' : 'danger';
                return '<td>
                    <a href="/admin/post/update-status/'.$row->id.'" class="btn btn-sm btn-'.$btnClass.'">
                        '.$statusText.'
                    </a>
                </td>';


            })
                ->addColumn('action', function($row){
                    // $editUrl = "/admin/product/edit/$row->id";
                    $deleteUrl = "/admin/product/destroy/$row->id"; // Removed the space before $row->id
                    $viewUrl = "/admin/product/view/$row->id";
                    // $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a>";
                    $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>"; 
                    $viewButton = "<a href='".$viewUrl."' class='btn btn-success btn-sm'><i class='fa fa-eye'></i></a>";
                    $modal = view('product.delete', ['id' => $row->id, 'url' => $deleteUrl]);
    
                    return $viewButton . "  " . $deleteButton . $modal;
                })->rawColumns(['created_by','action'])
                ->rawColumns(['category_id','action'])
                ->rawColumns(['sub_category_id','action'])
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function updateaccount_status($id) {
    //     $product = ProductModel::find($id);
    //     if ($product) {
    //         if ($product->account_status == 200) {
    //             $product->account_status = 300;
    //         }else{
    //             $product->account_status = 200;

    //         }
    //         $product->save();
    //     }
    //     return back();
    // }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required',
                'description' => 'required',
                'care_advice' => 'required',
                'material' => 'required',
                'price' => 'required',
                'quantity' => 'required',
                'created_by' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'required',
            ]);


            // Save to database
            $product = new ProductModel();
            $product->title = $request->input('title');
            $product->description = $request->input('description');
            $product->care_advice = $request->input('care_advice');
            $product->material = $request->input('material');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->created_by = $request->input('created_by');
            $product->category_id = $request->input('category_id');
            $product->sub_category_id = $request->input('sub_category_id');
            $product->save();

            return back()->with('success','Product added Successfully..');

        } catch (\Throwable $th) {
            return response()->json(['danger' => $th->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function view(string $id)
    {
        // Fetching a single product based on ID
        $product = ProductModel::findorfail($id);

        // Fetching users with account_status 200 (assuming this is what you want)
        $users = User::where('account_status', 200)->get();

        // Fetching colors for the product with the given ID
        $colors = ColorModel::where('product_id', $id)->get();

        // Fetching product sizes (assuming you have some condition here)
        $sizes = ProductSizeModel::where('product_id', $id)->get();

        // Passing the retrieved data to the view
        return view('product.view', compact('product', 'users', 'colors', 'sizes'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch the product by its ID
        $products = ProductModel::findOrFail($id);

        // Fetch users with account status 200
        $users = User::where('account_status', 200)->get();

        // Fetch colors related to the product
        $colors = ColorModel::where('product_id', $id)->get();

        // Fetch sizes related to the product
        $sizes = ProductSizeModel::where('product_id', $id)->get();

        // Fetch all categories
        $categories = CategoryModel::all();

        // Fetch all subcategories
        $subcategories = SubCategoryModel::all();

        return view('product.edit', compact('products', 'users', 'colors', 'sizes', 'categories', 'subcategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $Product = ProductModel::findOrFail($id);
            $Product->delete();
            return back()->with('success', 'Product delete successfully');
        } catch (\Exception $e) {
            return back()->with('danger', 'Failed to delete SubCategory: '.$e->getMessage());
        }
    }
}
