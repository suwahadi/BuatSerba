<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderPrintAwbController extends Controller
{
    public function __invoke(Order $order): View
    {
        return view('filament.orders.print-awb', [
            'record' => $order,
        ]);
    }
}
