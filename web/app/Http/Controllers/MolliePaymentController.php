<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Mollie\Laravel\Facades\Mollie;

class MolliePaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|max:100|min:10',
            ]);


        // Get the amount from the request
        $amount = $request->amount;

        $order = Order::create([
            'amount' => $amount,
        ]);

        // Create a payment
        $payment = Mollie::api()->payments->create([
            "amount" => [
                "currency" => "EUR",
                "value" => number_format($amount, 2, '.', ''), // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            "description" => "Order #{$order->id}",
            "redirectUrl" => route('payment.success', ['payment_id' => $order->id]),
            "webhookUrl" => route('webhook.mollie'),
            "metadata" => [
                "order_id" => $order->id,
            ],
        ]);

        // replace _payment_id_ with the payment id
        $payment->redirectUrl = str_replace('_payment_id_', $payment->id, $payment->redirectUrl);

        // Redirect to the payment screen
        return Inertia::location($payment->getCheckoutUrl(), 303);
    }

    public function paymentSuccess(Request $request)
    {
        // Get the payment id from the request
        $paymentId = $request->payment_id;

        // Get the payment
        $payment = Mollie::api()->payments->get($paymentId);

        // Get the payment status
        $status = $payment->status;

        // Return the payment status
        return Inertia::render('Payment/Success', [
            'status' => $status,
        ]);
    }

    public function handleWebhookMollie(Request $request)
    {
        // Get the payment ID
        $paymentId = $request->input('id');

        // Check if the payment ID is provided
        if (!$paymentId) {
            // Handle error: Payment ID not provided
            return response('Payment ID missing', 400);
        }

        // Get the payment
        $payment = Mollie::api()->payments->get($paymentId);

        // Update the order status
        $order = Order::find($payment->metadata->order_id);
        // Update the order status
        $order->status = $payment->status;
        // Save the order
        $order->save();

        return response(null, 200);
    }
}
