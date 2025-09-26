<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;

class SimulateBookingEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skylink:simulate-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulates random booking status changes every 5 seconds.';

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        parent::__construct();
        $this->bookingService = $bookingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting booking event simulation. Press Ctrl+C to stop.');

        $statuses = ['CONFIRMED', 'CANCELLED', 'CHECKED_IN'];

        while (true) {
            $booking = Booking::inRandomOrder()->with('passengers')->first();

            if ($booking) {
                // Selecciona un estado aleatorio, asegurándose de que no sea el mismo que el actual
                $newStatus = $statuses[array_rand($statuses)];
                while ($newStatus === $booking->status) {
                    $newStatus = $statuses[array_rand($statuses)];
                }

                $this->info("Updating booking #{$booking->id} to status: {$newStatus}");
                $this->bookingService->updateBookingStatus($booking->id, $newStatus);
            } else {
                $this->warn('No bookings found. Please create at least one booking to start the simulation.');
            }

            sleep(5); // Espera 5 segundos antes de la próxima actualización
        }
    }
}
