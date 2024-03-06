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
        $order->mollie_payment_id = $payment->id;
        $order->save();

        // Redirect to the payment screen
        return Inertia::location($payment->getCheckoutUrl(), 303);
    }

    public function paymentSuccess(Request $request)
    {
        // Get the payment id from the request
        $order_id = $request->payment_id;

        // Get the order with the mollie payment id
        $order = Order::where('mollie_payment_id', $order_id)->first();

        // Check if the order exists
        if (!$order) {
            // Handle error: Order not found
            return Inertia::render('Payment/Success', [
                'status' => 'error',
            ]);
        }

        $payment_id = $order->mollie_payment_id;

        // Get the payment
        $payment = Mollie::api()->payments->get($payment_id);

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
        $payment_id = $request->input('id');

        // Check if the payment ID is provided
        if (!$payment_id) {
            // Handle error: Payment ID not provided
            return response('Payment ID missing', 400);
        }

        // Get the payment
        $payment = Mollie::api()->payments->get($payment_id);

        // Update the order status
        $order = Order::find($payment->metadata->order_id);
        // Update the order status
        $order->status = $payment->status;
        // Save the order
        $order->save();

        return response(null, 200);
    }
}
