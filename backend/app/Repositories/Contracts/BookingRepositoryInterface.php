<?php
namespace App\Repositories\Contracts;

use App\Models\Booking;

interface BookingRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
}
