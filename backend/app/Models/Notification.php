<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Notification",
 * title="Notificación",
 * description="Modelo de una notificación de evento",
 * @OA\Property(
 * property="id",
 * type="string",
 * format="uuid",
 * description="ID de la notificación",
 * readOnly=true,
 * example="f8f8f8f8-f8f8-4f8f-8f8f-8f8f8f8f8f8f"
 * ),
 * @OA\Property(
 * property="type",
 * type="string",
 * description="Tipo de notificación",
 * example="App\Notifications\BookingStatusChanged"
 * ),
 * @OA\Property(
 * property="notifiable_type",
 * type="string",
 * description="Tipo de modelo al que se notifica",
 * example="App\Models\Booking"
 * ),
 * @OA\Property(
 * property="notifiable_id",
 * type="integer",
 * description="ID del modelo al que se notifica",
 * example=1
 * ),
 * @OA\Property(
 * property="data",
 * type="object",
 * description="Datos de la notificación",
 * example={"id": 1, "status": "CONFIRMED"}
 * ),
 * @OA\Property(
 * property="read_at",
 * type="string",
 * format="date-time",
 * nullable=true,
 * description="Fecha y hora de lectura de la notificación",
 * example="2025-10-25 10:00:00"
 * )
 * )
 */
class Notification extends Model
{
    public $table = "notifications";
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];


    public function notifiable()
    {
        return $this->morphTo();
    }
}
