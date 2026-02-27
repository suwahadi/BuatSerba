<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderPrintInvoiceController extends Controller
{
    public function __invoke(Order $order): View
    {
        $payment = \App\Models\Payment::query()->where('order_id', $order->id)->first();

        return view('filament.orders.print-invoice', [
            'record' => $order,
            'payment' => $payment,
        ]);
    }
}
