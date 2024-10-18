<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddressModel;
use App\Models\OrderModel;
use Faker\Provider\ar_EG\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerId = Auth::id();

        $addresses = CustomerAddressModel::where('customer_id',$customerId)->get();

        return response()->json( $addresses);

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
            $data = $this->validate($request, [
               'address' => 'required',
               'address_category'=>'required',
            ]);

            $customerId = Auth::id();


            $address = CustomerAddressModel::create([
              'address' => $request->address,
              'address_category' => $request->address_category,
              'customer_id' => $customerId,
           ]);

           return response()->json(['message' => 'Address has been created successfully' , 'address' => $address]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
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
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validate($request, [
                'address' => 'required',
            ]);

            $customerId = Auth::id();

            $address = CustomerAddressModel::findOrFail($id);

            if ($address->customer_id != $customerId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Update the address
            $address->update([
                'address' => $request->address,
            ]);

            return response()->json(['message' => 'Address has been updated successfully' , 'address' => $address]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $customerId = Auth::id();

            $address = CustomerAddressModel::findOrFail($id);

            if ($address->customer_id != $customerId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Delete the address
            $address->delete();

            return response()->json(['message' => 'Address has been deleted successfully']);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


}
