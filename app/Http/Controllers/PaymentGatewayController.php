<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayModel;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentGatewayModel::select('*');
            return Datatables::of($data)
            ->addColumn('action',function ($row) {
                $editUrl = "/admin/paymentgateway/edit/$row->id";
                $deleteUrl = "/admin/paymentgateway/destroy/$row->id";
                $editButton = "<a href='".$editUrl."' class='btn btn-primary btn-sm'><i class='fa fa-edit'></i></a> ";
                $deleteButton = "<a data-bs-toggle='modal' data-bs-target='#deleteRecordModal".$row->id."' href='#' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a>";
                $modal = view('paymentgateway.delete',['id'=>$row->id,'url'=>$deleteUrl]);
                return $editButton.$deleteButton.$modal;
            })
            ->editColumn('logo', function($row) {
                return '
                <div class="image-container">
                <img src="'.$row->logo.'" alt="logo">
                </div>
                ';
            })
            ->addColumn('status',function ($row) {
                $statusText = ($row->status == 200) ? 'Active' : 'InActive';
                $badgeClass = ($row->status == 200) ? 'success' : 'danger';
                return '<td>
                       <span class="badge bg-'.$badgeClass.'">'.$statusText.'</span>
                </td>';
            })
            ->rawColumns(['logo','status','action'])
            ->make(true);
        }
        return view('paymentgateway.index');
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
            $data = $this->validate($request,[
                'gateway_name' => 'required',
                'logo' => 'required|image' ,
                'status' => 'required',
            ]);

            // Store Logo

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time().'.'.$logo->getClientOriginalExtension();
                $path = public_path('/images');
                $logo->move($path,$logoName);
            }else {
                return back()->with('error','logo not provided');
            }

            $paymentGateway = new PaymentGatewayModel;

            $paymentGateway->gateway_name = $request->gateway_name;
            $paymentGateway->logo = '/images/'.$logoName;
            $paymentGateway->status = $request->status;

            $paymentGateway->save();

            return back()->with('success', 'Payment Gateway store Successfuly');

        } catch (\Throwable $th) {
            return back()->with('error' , $th->getMessage());
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
        $paymentgateway = PaymentGatewayModel::findorfail($id);

        return view('paymentgateway.edit',compact('paymentgateway'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $this->validate($request, [
                'gateway_name' => 'required',
                'logo' => 'nullable|image',
                'status' => 'required',
            ]);
    
            $paymentGateway = PaymentGatewayModel::findOrFail($id);
    
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $path = public_path('/images');
                $logo->move($path, $logoName);
                $logoName = '/images/' . $logoName; 
            } else {
                $logoName = $paymentGateway->logo;
            }
    
            $paymentGateway->gateway_name = $request->gateway_name;
            $paymentGateway->logo = $logoName; 
            $paymentGateway->status = $request->status;
    
            $paymentGateway->save();
    
            return back()->with('success', 'Payment Gateway updated');
            
        } catch (Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $paymentGateway = PaymentGatewayModel::findorfail($id);
            $paymentGateway->delete();
            return back()->with('success' , 'Payment Gateway Deleted');
        } catch (\Throwable $th) {
            return back()->with('danger', 'Failed to deleted successfully');
        }
    }
}
