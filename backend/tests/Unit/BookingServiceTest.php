<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Booking;
use App\Services\BookingService;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\BookingStatusChanged;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    // Se encargará de limpiar los mocks.
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_update_a_booking_status()
    {
        // 1. Arrange: Prepara el entorno
        $booking = Booking::factory()->create(['status' => 'PENDING']);
        $newStatus = 'CONFIRMED';

        // Mock del repositorio: Simula que la base de datos se actualiza correctamente
        $mockRepo = Mockery::mock(BookingRepositoryInterface::class);
        $mockRepo->shouldReceive('find')
            ->with($booking->id)
            ->andReturn($booking);
        $mockRepo->shouldReceive('update')
            ->with($booking->id, ['status' => $newStatus])
            ->andReturn(true);

        // 2. Act: Ejecuta el método que queremos probar
        $bookingService = new BookingService($mockRepo);
        $result = $bookingService->updateBookingStatus($booking->id, $newStatus);

        // La actualización de la DB se hace en el mismo método
        $result->refresh();

        // 3. Assert: Verifica el resultado
        $this->assertEquals($newStatus, $result->status);
        $this->assertNotEquals('PENDING', $result->status);
    }

    /** @test */
    public function it_can_create_a_booking_with_passengers()
    {
        // Arrange: Prepara los datos de la reserva y los pasajeros.
        $data = [
            'flight_number' => 'VUELO-42-CDMX',
            'departure_time' => '2025-10-25 10:00:00',
            'passengers' => [
                ['name' => 'Claudio', 'last_name' => 'Cabrera', 'passport_number' => '12345678'],
                ['name' => 'Nicolas', 'last_name' => 'Fernandez', 'passport_number' => '87654321'],
            ],
        ];

        // Simula que el repositorio y el modelo de Eloquent funcionan correctamente.
        $mockBooking = Mockery::mock(Booking::class);
        $mockBooking->shouldReceive('load')->with('passengers')->andReturnSelf();

        $mockBooking->shouldReceive('passengers->createMany')->once();
        // Mockea el comportamiento del modelo
        $mockBooking->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mockBooking->shouldReceive('getAttribute')->with('flight_number')->andReturn($data['flight_number']);
        $mockBooking->shouldReceive('getAttribute')->with('departure_time')->andReturn($data['departure_time']);
        $mockBooking->shouldReceive('getAttribute')->with('status')->andReturn('PENDING'); // Nuevo: Mockea el status

        // Mockea el repositorio para que devuelva el objeto 'mockBooking'.
        $mockRepo = Mockery::mock(BookingRepositoryInterface::class);
        $mockRepo->shouldReceive('create')->once()->andReturn($mockBooking);

        // Act: Ejecuta el método a probar y dispara el evento.
        Event::fake(); // Falso el sistema de eventos.
        $bookingService = new BookingService($mockRepo);
        $booking = $bookingService->createBooking($data);

        // Assert: Verifica los resultados y que el evento se disparó.
        $this->assertEquals('PENDING', $booking->status);
        $this->assertInstanceOf(Booking::class, $booking);
        Event::assertDispatched(BookingStatusChanged::class);
    }

    /** @test */
    public function it_can_retrieve_a_booking_by_id()
    {
        // Arrange: Crea una reserva en la base de datos de prueba.
        $booking = Booking::factory()->create();

        // Mockea el repositorio para que devuelva la reserva.
        $mockRepo = Mockery::mock(BookingRepositoryInterface::class);
        $mockRepo->shouldReceive('find')->once()->with($booking->id)->andReturn($booking);
        $mockRepo->shouldReceive('load')->andReturnSelf();

        // Act: Llama al método del servicio.
        $bookingService = new BookingService($mockRepo);
        $foundBooking = $bookingService->getBooking($booking->id);

        // Assert: Verifica que la reserva se encontró y que es la correcta.
        $this->assertNotNull($foundBooking);
        $this->assertEquals($booking->id, $foundBooking->id);
    }

    /** @test */
    public function it_can_cancel_a_booking()
    {
        // Arrange: Crea una reserva en la base de datos de prueba.
        $booking = Booking::factory()->create(['status' => 'PENDING']);

        // Falso el sistema de eventos.
        Event::fake();

        // Mockea el repositorio para que simule la actualización.
        $mockRepo = Mockery::mock(BookingRepositoryInterface::class);
        $mockRepo->shouldReceive('update')->once()->andReturn(true);
        $mockRepo->shouldReceive('find')->once()->andReturn($booking);

        // Act: Llama al método del servicio.
        $bookingService = new BookingService($mockRepo);
        $result = $bookingService->cancelBooking($booking->id);

        // Assert: Verifica que el método retornó true y que el evento se disparó.
        $this->assertTrue($result);
        Event::assertDispatched(BookingStatusChanged::class);
    }
}
