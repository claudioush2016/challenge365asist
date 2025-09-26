<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Passenger",
 * title="Pasajero",
 * description="Modelo de un pasajero en una reserva",
 * @OA\Property(
 * property="id",
 * type="integer",
 * description="ID del pasajero",
 * readOnly=true,
 * example=18
 * ),
 * @OA\Property(
 * property="booking_id",
 * type="integer",
 * description="ID de la reserva a la que pertenece el pasajero",
 * example=17
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * description="Nombre del pasajero",
 * example="Claudio"
 * ),
 * @OA\Property(
 * property="last_name",
 * type="string",
 * description="Apellido del pasajero",
 * example="Cabrera"
 * ),
 * @OA\Property(
 * property="passport_number",
 * type="string",
 * description="Número de pasaporte del pasajero",
 * example="1111121"
 * )
 * )
 */
class Passenger extends Model
{
    public $table = 'passengers';
    protected $fillable = ['booking_id','name','last_name','passport_number'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
