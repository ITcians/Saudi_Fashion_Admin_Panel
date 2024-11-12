<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\OfferPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
class OfferPromotionController extends Controller
{

    public function index(){
        return OfferPromotion::where('created_by' ,  Auth::id())->with(['product'])->latest()->paginate(10);
    }

   
    public function create()
    {
        //
    }

 
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->account_type == 1) {
            return response()->json(['error' => 'Unauthorized. Only users with account_type Desginer can update posts.'], 403);
        }
        try {
            $offerpromotion = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'percentage' => 'required',
                'product_id' => 'required',
                'status' => 'required',
            ]);

            if ($offerpromotion->fails()) {
                return response()->json(['error' => $offerpromotion->errors()], 422);
            }

            $Data = [
                'title' => $request->title,
                'description' => $request->description,
                'percentage' => $request->percentage,
                'product_id' => $request->product_id,
                'created_by' => Auth::id(),
                'status' => $request->status,
            ];


            $promotion = OfferPromotion::create($Data);

            return response()->json([
                'message' => 'Offer Promotion Submitted Successfully',
                'promotion' => $promotion
            ]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
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
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'percentage' => 'required', 
                'product_id' => 'required', 
                'status' => 'required', 
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            $offerPromotion = OfferPromotion::findOrFail($id);
    
            $offerPromotion->update($request->all());
    
            return response()->json([
                'message' => 'Offer Promotion Updated Successfully',
                'promotion' => $offerPromotion
            ]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
