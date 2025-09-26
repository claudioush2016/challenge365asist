<?php
namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Models\Passenger;
use App\Repositories\Contracts\BookingRepositoryInterface;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function create(array $data)
    {
        return Booking::create($data);
    }

    public function find(int $id)
    {
        return Booking::find($id);
    }

    public function update(int $id, array $data)
    {
        $booking = $this->find($id);

        if ($booking) {
            return $booking->update($data);
        }
        return false;
    }

    public function delete(int $id)
    {
        $booking = $this->find($id);
        if ($booking) {
            return $booking->delete();
        }
        return false;
    }
    public function findPassenger(int $id){
        $passager = Passenger::find($id);
        return $passager;
    }
}
