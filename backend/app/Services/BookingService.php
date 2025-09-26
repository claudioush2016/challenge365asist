<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Models\Booking;
use App\Events\BookingStatusChanged;
use Illuminate\Http\Request;

class BookingService
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    public function listBookings(Request $request)
    {
        $query = Booking::with('passengers');

        // Filtro por estado
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro por fecha (opcional)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        return $query->get();
    }
    /**
     * Crea una nueva reserva y sus pasajeros asociados.
     */
    public function createBooking(array $data)
    {
        $booking = $this->bookingRepository->create([
            'flight_number' => $data['flight_number'],
            'departure_time' => $data['departure_time'],
            'status' => 'PENDING' // Estado inicial
        ]);


        if (isset($data['passengers']) && is_array($data['passengers'])) {
            $booking->passengers()->createMany($data['passengers']);
        }

        event(new BookingStatusChanged($booking));

        return $booking;
    }

    /**
     * Obtiene una reserva por su ID.
     */
    public function getBooking(int $id)
    {
        return $this->bookingRepository->find($id)->load('passengers');
    }

    /**
     * Actualiza el estado de una reserva y dispara un evento.
     */
    public function updateBookingStatus(int $id, string $status)
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new \Exception('Booking not found.');
        }

        // Actualizar el estado
        $booking->status = $status;
        $booking->save();

        // Cargar los pasajeros antes de disparar el evento
        $booking->load('passengers');

        // Disparar el evento con el modelo de reserva completo
        event(new BookingStatusChanged($booking));

        return $booking;
    }


    /**
     * Cancela una reserva y dispara un evento.
     */
    public function cancelBooking(int $id)
    {
        $result = $this->bookingRepository->update($id, ['status' => 'cancelled']);
        if ($result) {
            $booking = $this->bookingRepository->find($id);
            if ($booking) {
                event(new BookingStatusChanged($booking));
            }
        }
        return $result;
    }

    function getPassenger(int $id){
        $booking = $this->bookingRepository->findPassenger($id);
        return $booking;
    }

}
