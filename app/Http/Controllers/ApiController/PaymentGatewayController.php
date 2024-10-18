<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\PaymentGatewayModel;
use App\Models\SettingModel;
use App\Models\TapModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentGateway = PaymentGatewayModel::where('status' , 200)->get();
        return response()->json($paymentGateway);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        // dd($request);
        $input = $request->all();

        $productId = $request->product_id;
        $User = Auth::user();

        $default_currency = SettingModel::where('key', 'default_currency')->first();

        // $data['amount'] = floatval($input['amount']);
        $data['amount'] = 300;
        $data['currency'] = $default_currency->value;
        $data['customer']['first_name'] = $User->first_name;
        $data['customer']['email'] = $User->email;
        $data['customer']['phone']['country_code'] = $User->country_code;
        $data['customer']['phone']['number'] = $User->phone;
        $data['source']['id'] = 'src_card';

        $productIds = [1,2,3,4]; // Assuming this is an array // testing
        $productIdList = implode(',', $productIds); // Convert array to a comma-separated string
        
        $data['redirect']['url'] = 'http://192.168.100.8:8000/api/callback/' . urlencode($productIdList);
        

        $response = Curl::to('https://api.tap.company/v2/charges')
                    ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                    ->withData($data)
                    ->asJson()
                    ->post();

                    return response()->json([
                        'payment_gateway_url' => $response->transaction->url,
                    ]);
                    
        // dd($response);
    }

  
    public function callback(Request $request,string $productIds)
    {
        // return response()->json($productIds);
        $response = Curl::to('https://api.tap.company/v2/charges/'.$_GET['tap_id'])
                    ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                    ->asJson()
                    ->get();

       if ($response->status == 'CAPTURED') {
            $TapModel = new TapModel();
            $TapModel->name = $response->customer->first_name;
            $TapModel->email = $response->customer->email;
            $TapModel->tran_id = $response->id;
            $TapModel->amount = $response->amount;
            $TapModel->save();
       }


    //    dd($response);
    return view('invoice',compact($response));
    // return 'Successfully Payment ADDED!';
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
