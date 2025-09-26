<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 * schema="UpdateBookingStatusRequest",
 * required={"status"},
 * @OA\Property(
 * property="status",
 * type="string",
 * example="CONFIRMED",
 * description="El nuevo estado de la reserva",
 * enum={"CONFIRMED", "CANCELLED", "CHECKED_IN"}
 * )
 * )
 */
class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => ['required', 'string', Rule::in(['CONFIRMED', 'CANCELLED', 'CHECKED_IN'])],
        ];
    }

    /**
     * Maneja una validación fallida en una petición de API.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
