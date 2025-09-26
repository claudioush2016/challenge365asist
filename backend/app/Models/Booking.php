<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Booking",
 * title="Reserva",
 * description="Modelo de una reserva de vuelo",
 * @OA\Property(
 * property="id",
 * type="integer",
 * description="ID de la reserva",
 * readOnly=true,
 * example=1
 * ),
 * @OA\Property(
 * property="flight_number",
 * type="string",
 * description="Número de vuelo",
 * example="VUELO-42-CDMX"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * description="Estado de la reserva",
 * enum={"PENDING", "CONFIRMED", "CANCELLED", "CHECKED_IN"},
 * example="PENDING"
 * ),
 * @OA\Property(
 * property="departure_time",
 * type="string",
 * format="date-time",
 * description="Fecha y hora de salida del vuelo",
 * example="2025-10-25 10:00:00"
 * ),
 * @OA\Property(
 * property="passengers",
 * type="array",
 * description="Pasajeros asociados a la reserva",
 * @OA\Items(ref="#/components/schemas/Passenger")
 * )
 * )
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'status',
        'departure_time'
    ];

    /**
     * Obtiene los pasajeros asociados a la reserva.
     */
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
