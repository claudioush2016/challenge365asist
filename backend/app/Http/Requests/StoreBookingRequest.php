<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 * schema="StoreBookingRequest",
 * required={"flight_number", "departure_time", "passengers"},
 * @OA\Property(
 * property="flight_number",
 * type="string",
 * example="VUELO-42-CDMX",
 * description="El número del vuelo"
 * ),
 * @OA\Property(
 * property="departure_time",
 * type="string",
 * format="date-time",
 * example="2025-10-25 10:00:00",
 * description="La fecha y hora de salida del vuelo"
 * ),
 * @OA\Property(
 * property="passengers",
 * type="array",
 * minItems=1,
 * @OA\Items(
 * @OA\Property(
 * property="name",
 * type="string",
 * example="Claudio"
 * ),
 * @OA\Property(
 * property="last_name",
 * type="string",
 * example="Cabrera"
 * ),
 * @OA\Property(
 * property="passport_number",
 * type="string",
 * example="1111121"
 * )
 * ),
 * description="Lista de pasajeros para la reserva"
 * )
 * )
 */
class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'flight_number' => ['required', 'string', 'max:255'],
            'departure_time' => ['required', 'date'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.name' => ['required', 'string', 'max:255'],
            'passengers.*.last_name' => ['required', 'string', 'max:255'],
            'passengers.*.passport_number' => ['required', 'string', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
