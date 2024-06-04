<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(){
        $payments=Payment::with('user')->latest()->get();
        return response(['payments'=>$payments],200);
    }
    public function paymentStore(PaymentRequest $request)
    {
        $request->validated();

       auth()->user()->payments()->create([
            'amount' => $request->amount
       ]);

       return response(['message' => 'Payment created successfully'], 201);
    }
}
