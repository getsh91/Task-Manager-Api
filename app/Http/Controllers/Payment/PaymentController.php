<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use App\Models\Payment;
use Chapa\Chapa\Facades\Chapa as Chapa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class PaymentController extends Controller
{
    protected $reference;

    public function __construct()
    {
        $this->reference = Chapa::generateReference();
    }


    public function index(){

        $user = Auth::user();
        $dailyPayments = Payment::where('user_id', $user->id)->whereDate('created_at', now())->get();
        $weeklyPayments = Payment::where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $monthlyPayments = Payment::where('user_id', $user->id)->whereMonth('created_at', now()->month)->get();

        return response(['payments'=>
            [
                'dailyPayments' => $dailyPayments,
                'weeklyPayments' => $weeklyPayments,
                'monthlyPayments' => $monthlyPayments
            ]
    ],200);
    }
    public function paymentStore(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $reference = $this->reference;


        $data = [
        
            'amount' => $request->input('amount'),
            'email' => $user->email,
            'tx_ref' => $reference,
            'currency' => "ETB",
            'callback_url' => route('callback', [$reference, 'user_id' => $user->id]),
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            "customization" => [
                "title" => 'ELPA APP',
                "description" => "Pay Online"
            ]
        ];
   
        $payment = Chapa::initializePayment($data);
       
        if ($payment['status'] !== 'success') {
            return response()->json([
                'message' => 'Something went really bad'
            ], 500);
        }
        return response()->json($payment, 200);
    }

    public function callback($reference, Request $request)
    {
        $data = Chapa::verifyTransaction($reference);
        $user_id = $request->input('user_id');

        if ($data['status'] == 'success') {
            $payment = Payment::create([
                'user_id' => $user_id,
                'amount' => $data['data']['amount'],
            ]);

            Log::info("Payment successful");
        } else {
            Log::info("Payment failed");
        }
    }
}
