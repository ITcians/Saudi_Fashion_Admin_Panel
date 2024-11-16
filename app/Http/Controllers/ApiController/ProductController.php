<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\ColorModel;
use App\Models\Comment;
use App\Models\Info;
use App\Models\OrderModel;
use App\Models\ProductFrequency;
use App\Models\ProductMediaModel;
use App\Models\ProductModel;
use App\Models\ProductSizeModel;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator as ValidationValidator;
use stdClass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $res;

    function __construct()
    {
        $this->res = new stdClass;
    }
    /**
     * Display a listing of the resource.
     */
    public function getProduct(Request $request)
    {
        try {
            $this->validate($request, [
                'category_id' => 'nullable|string',
                'sub_category_id' => 'nullable|string',
                'search' => 'nullable|string', // Add validation for the search parameter
            ]);
    
            // Build the query based on provided parameters
            $query = ProductModel::with([
                'media',
                'category',
                'sub_category',
                'sizes',
                'colors',
            ])->latest();
    
            // Apply filters if present
            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }
    
            if ($request->sub_category_id) {
                $query->where('sub_category_id', $request->sub_category_id);
            }
    
            // Apply search if present
            if ($request->search) {
                $query->where('title', 'like', '%' . $request->search . '%'); // Assuming 'name' is the product name field
            }
    
            // Fetch the products
            $products = $query->paginate(15);
    
            return response()->json($products);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    
    public function productDetails(string $id)
    {
        $product = ProductModel::where('id',$id)->with([
            'media',
            'category',
            'sub_category',
            'sizes',
            'colors',
        ])->get();
        return response()->json($product);
    }

    function myProducts(Request $request){
        if ($request->input('search')) {
            return ProductModel::where('created_by', Auth::id())
            ->latest()
            ->orWhere('title','like','%'.$request->search.'%')
            ->with([
                'media',
                'category',
                'sub_category',
                'sizes',
                'colors',
            ])->paginate(10);
        }
        return ProductModel::where('created_by', Auth::id())
            ->latest()
            ->with([
                'media',
                'category',
                'sub_category',
                'sizes',
                'colors',
            ])->paginate(10);
    }


    // function uploadImages(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'images' => "required|Array",
    //             'product_id' => 'required|exists:products,id'
    //         ]);

    //         if ($validator->fails()) {
    //             $this->res->errors = $validator->errors();
    //             return;
    //         }

    //         $product = ProductModel::where([
    //             'id' => $request->product_id,
    //             'created_by' => Auth::id()
    //         ])->first();
    //         if ($product) {
    //             // Upload media.
    //             $images = array();
    //             $images = json_decode($request->images);


    //             foreach ($images as $base64Image) {
    //                 $imageData = base64_decode($base64Image);
    //                 $imageName = 'profile_' . Str::random(10) . '.png';
    //                 $imagePath = '/upload_images/' . $imageName;

    //                 File::put(public_path($imagePath), $imageData);

    //                 File::put(public_path($imagePath), $imageData);

    //             foreach ($request->images as $img) {

    //                 $media = ProductMediaModel::create([
    //                     'type' => "image",
    //                     'media' => $imagePath,
    //                     'product_id' => $request->product_id,
    //                 ]);

    //                 array_push($images, $media);
    //             }

    //                 array_push($images, $media);
    //             }
    //             $this->res->uploaded_media = $images;
    //             $this->res->message = 'Media files uploaded against the product successfully!';
    //             $product->update(['status' => 1]);
    //         } else {
    //             $this->res->error = 'Unauthorized action!';
    //         }
    //     } catch (Exception $ex) {
    //         $this->res->error = $ex->getMessage();
    //     } finally {
    //         return $this->res;
    //     }
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function product_price_category_subcategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'price' => 'required',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'product_id' => "required|exists:products,id"
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            // Fetch the authenticated user
            $user = auth()->user();

            // Fetch the product by ID
            $product = ProductModel::findOrFail($request->product_id);

            // Check if the authenticated user created the product
            if ($user->id !== $product->created_by) {
                return response()->json(['error' => 'You are not authorized to update this product.'], 403);
            }

            
            Info::create(['message' => 'price',$request->price]);
            // Update product data
            $product->price = $request->price;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id ?? 0;
            $product->save();

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the product.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // Store product
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if the user account type is 1
        if ($user->account_type != 1) {
            return response()->json(['error' => 'Unauthorized. Only users with account_type Desginer can update posts.'], 403);
        }
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'quantity' => 'required',
                'description' => 'required',
                'care_advice' => 'required',
                'material' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $authId = Auth::id();

            $productData = [
                'title' => $request->title,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'care_advice' => $request->care_advice,
                'material' => $request->material,
                'category_id' => 0,
                'sub_category_id' => 0,
                'status' => 0, // Adjust status as needed
                'created_by' => $authId
            ];

            $product = ProductModel::create($productData);

       

            return response()->json([
                'message' => 'Data Submitted Successfully',
                'product' => $product
            ]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

            public function uploadBase64ImagesProduct(Request $request)
            {
                try {
                    $uploadDir = public_path('upload_images/');

                    // Create the directory if it does not exist
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $base64Images = $request->input('images');

                    // \Log::info('Uploading images: ', ['images' => $base64Images]);
                    
                    foreach (json_decode($base64Images) as $data) {
                        // Strip the header if it exists
                        if (strpos($data, 'base64,') !== false) {
                            $data = explode('base64,', $data)[1];
                        }
                        $data = base64_decode($data); // Decode the Base64 string
                        
                        // Determine the file extension from the type
                        $filename = uniqid() . '.jpg';
                        $path = $uploadDir . $filename;

                        // Save the image to the server
                        if (file_put_contents($path, $data) === false) {
                            throw new Exception("Failed to write image to $path");
                        }

                        // Save the file path in the database
                        ProductMediaModel::create([
                            'media' => '/upload_images/' . $filename,
                            'type' => 'image',
                            'product_id' => $request->product_id,
                        ]);
                    }

                    return response()->json(['success' => true]);
                } catch (Exception $ex) {
                    \Log::error('Image upload error: ' . $ex->getMessage());
                    return response()->json(['success' => false, 'error' => $ex->getMessage()], 500);
                }
            }

       

    // Store the color

    public function ColorSizes(Request $request, string $productId)
    {
        // return $request->all();
        // return $request->sizes;
        try {
            $validator = Validator::make($request->all(), [
                'colors' => 'required|array',
                'sizes' => 'required',
            ]);
            //return $validator;


            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors(),
            'type'=>var_dump('sizes')], 422);
            }
            // return $request->all();


            //check if product is created by this user or not.
            if (!ProductModel::where(['id' => $productId, 'created_by' => Auth::id()])->get()->first()) {
                //unauthorized
                return response()->json(['error' => 'Unauthorized action!'], 403);
            }

            foreach ($request->colors as $color) {
                ColorModel::create([
                    'color_name' => $color['color_name'],
                    'color_code' => $color['color_code'],
                    'product_id' => $productId,
                ]);
            }

            foreach (json_decode($request->sizes) as $size) {
                ProductSizeModel::create([
                    'size' => $size,
                    'product_id' => $productId,
                ]);
            }



            return response()->json(['message' => 'Color and sizes data updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }




    //  Yahan sa Update ke function start ho rahe han




    public function update(Request $request, $id)
    {
        try {
            $authId = Auth::id();

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'quantity' => 'required|numeric|min:0',
                'description' => 'required',
                'care_advice' => 'required',
                'material' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Find the existing product by its ID
            $product = ProductModel::findOrFail($id);

            // Check if the authenticated user is the creator of the product
            if ($product->created_by != $authId) {
                return response()->json(['error' => 'You do not have permission to update this product'], 403);
            }

            // Update the product with the new data
            $product->update([
                'title' => $request->title,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'care_advice' => $request->care_advice, 
                'material' => $request->material,
            ]);

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function updateColorAndSizes(Request $request, string $productId)
{
    try {
        $validator = Validator::make($request->all(), [
            'colors' => 'required|array',
            'sizes' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if the product exists and is created by the authenticated user
        $product = ProductModel::where(['id' => $productId, 'created_by' => Auth::id()])->first();
        if (!$product) {
            return response()->json(['error' => 'Unauthorized action or Product not found!'], 403);
        }

        // Update colors
        foreach ($request->colors as $color) {
            ColorModel::updateOrCreate(
                ['product_id' => $productId, 'color_name' => $color['color_name']],
                ['color_code' => $color['color_code']]
            );
        }

        // Update sizes
        ProductSizeModel::where('product_id', $productId)->delete(); // Remove existing sizes
        foreach ($request->sizes as $size) {
            ProductSizeModel::create([
                'size' => $size,
                'product_id' => $productId,
            ]);
        }

        return response()->json(['message' => 'Color and sizes data updated successfully']);
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}


public function updateProductPriceAndCategory(Request $request, string $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'price' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Fetch the authenticated user
        $user = $request->user();

        // Fetch the product by ID
        $product = ProductModel::findOrFail($id);

        // Check if the authenticated user created the product
        if ($user->id !== $product->created_by) {
            return response()->json(['error' => 'You are not authorized to update this product.'], 403);
        }

        // Update product data
        $product->update([
            'price' => $request->price,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => 'An error occurred while updating the product.'], 500);
    }
}

    public function productAccordingToDesginerId(string $id)
    {
        $product = ProductModel::where('created_by',$id)->with([
            'media',
            'category',
            'sub_category',
            'sizes',
            'colors',
        ])->latest()->paginate(15);
        return response()->json($product);
    }


    public function productTapFrequency(Request $request)
    {
        try {
            $this->validate($request,[
                'product_id' => 'required',
                'created_by_id' => 'required'
            ]);

            ProductFrequency::create([
                'product_id' =>$request->product_id,
                'user_id' => Auth::id(),
                'created_by_id' =>$request->created_by_id,
            ]);

            $this->res->message = 'Data Added Successfully!';
        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return $this->res;
        }
    }

    public function getProductFrequency()
    {
        $fre = ProductFrequency::where('created_by_id',Auth::id())->get()->count();
        return response()->json(['view_count'=>$fre]);
    }

    public function statusForDesginer($invoiceId) 
    {
        $order = OrderModel::where('desginer_id',Auth::id())->where('invoice_id',$invoiceId)->first();
        return response()->json(['status' => $order->status]);
    }

}


