<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="API de Reservas de Vuelos",
 * description="API para gestionar reservas y pasajeros en tiempo real.",
 * @OA\Contact(
 * email="claudio.cabrera@example.com"
 * )
 * )
 *
 * @OA\Tag(
 * name="Reservas",
 * description="Operaciones de gestión de reservas"
 * )
 *
 * @OA\Tag(
 * name="Pasajeros",
 * description="Operaciones de gestión de pasajeros"
 * )
 */
class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @OA\Post(
     * path="/api/reservations",
     * summary="Crea una nueva reserva",
     * tags={"Reservas"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(ref="#/components/schemas/StoreBookingRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Reserva creada exitosamente.",
     * @OA\JsonContent(ref="#/components/schemas/Booking")
     * )
     * )
     */
    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->createBooking($request->validated());
        return response()->json($booking, 201);
    }

    /**
     * @OA\Put(
     * path="/api/reservations/{id}/status",
     * summary="Actualiza el estado de una reserva",
     * tags={"Reservas"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID de la reserva a actualizar",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", enum={"CONFIRMED", "CANCELLED", "CHECKED_IN"}, example="CONFIRMED")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Estado de reserva actualizado correctamente."
     * ),
     * @OA\Response(
     * response=404,
     * description="Reserva no encontrada."
     * )
     * )
     */
    public function updateStatus(UpdateBookingStatusRequest $request, $id)
    {
        $result = $this->bookingService->updateBookingStatus($id, $request->validated('status'));
        if (!$result) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }
        return response()->json(['message' => 'Estado de reserva actualizado correctamente']);
    }

    /**
     * @OA\Get(
     * path="/api/reservations",
     * summary="Lista todas las reservas",
     * tags={"Reservas"},
     * @OA\Response(
     * response=200,
     * description="Lista de reservas obtenida exitosamente.",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Booking")
     * )
     * )
     * )
     */
    public function index(Request $req)
    {
        $bookings = $this->bookingService->listBookings($req);
        return response()->json($bookings);
    }

    /**
     * @OA\Get(
     * path="/api/reservations/{id}",
     * summary="Muestra el detalle de una reserva",
     * tags={"Reservas"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID de la reserva a mostrar",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Detalles de la reserva obtenidos exitosamente.",
     * @OA\JsonContent(ref="#/components/schemas/Booking")
     * ),
     * @OA\Response(
     * response=404,
     * description="Reserva no encontrada."
     * )
     * )
     */
    public function show(string $id)
    {
        $booking = $this->bookingService->getBooking((int) $id);
        if (!$booking) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }
        return response()->json($booking);
    }

    /**
     * @OA\Get(
     * path="/api/passengers/{id}",
     * summary="Muestra el detalle de un pasajero",
     * tags={"Pasajeros"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID del pasajero a mostrar",
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Detalles del pasajero obtenidos exitosamente.",
     * @OA\JsonContent(ref="#/components/schemas/Passenger")
     * ),
     * @OA\Response(
     * response=404,
     * description="Pasajero no encontrado."
     * )
     * )
     */
    public function showPassenger($id)
    {
        $passenger = $this->bookingService->getPassenger($id);
        if (!$passenger) {
            return response()->json(['message' => 'Pasajero no encontrado'], 404);
        }
        return response()->json($passenger);
    }
}
