<?php

namespace App\Listeners;


use App\Events\BookingStatusChanged;
use App\Models\Booking;
use App\Models\Notification;

class LogBookingNotification
{
    public function handle(BookingStatusChanged $event)
    {
        $booking = $event->booking;

        Notification::create([
            'type' => 'booking.status_updated',
            'notifiable_type' => Booking::class,
            'notifiable_id' => $booking->id,
            'data' => json_encode([ // <-- El cambio está aquí
                'id' => $booking->id,
                'flight_number' => $booking->flight_number,
                'status' => $booking->status,
                'passengers' => $booking->passengers,
            ]),
            // Los timestamps 'created_at' y 'updated_at' se manejan automáticamente
        ]);
    }
}
