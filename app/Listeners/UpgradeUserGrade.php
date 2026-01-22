<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpgradeUserGrade implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if (!$user) {
            return;
        }

        $totalPaid = $user->orders()
            ->where('payment_status', 'paid')
            ->sum('total');

        $grade = 'basic';
        if ($totalPaid >= 100000001) {
            $grade = 'platinum';
        } elseif ($totalPaid >= 10000001) {
            $grade = 'gold';
        } elseif ($totalPaid >= 1000001) {
            $grade = 'silver';
        }

        if ($user->grade !== $grade) {
            $user->update(['grade' => $grade]);
        }
    }
}
