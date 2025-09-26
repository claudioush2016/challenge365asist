<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use Predis\Client as RedisClient; // Importa la clase del cliente de Redis

class SendBookingUpdateToRedis
{
    public function handle(BookingStatusChanged $event)
    {
        $data = [
            'id' => $event->booking->id,
            'flight_number' => $event->booking->flight_number,
            'status' => $event->booking->status,
            'passengers' => $event->booking->passengers,
        ];

        try {
            $redis = new RedisClient([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            $redis->publish('events:bookings', json_encode($data));

        } catch (\Exception $e) {
            \Log::error('Error al publicar en Redis desde el listener: ' . $e->getMessage());
        }
    }
}
