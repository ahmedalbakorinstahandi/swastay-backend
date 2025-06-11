<?php

namespace App\Http\Controllers;

use App\Http\Permissions\BookingPermission;
use App\Http\Requests\Booking\AddTransactionRequest;
use App\Http\Requests\Booking\CreateRequest;
use App\Http\Requests\Booking\UpdateRequest;
use App\Http\Resources\BookingResource;
use App\Http\Services\BookingService;
use App\Services\ResponseService;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $data = request()->all();
        $data = $this->bookingService->index($data);

        return ResponseService::response([
            'success' => true,
            'info'    => $data['bookings_status_count'],
            'data'    => $data['bookings'],
            'resource' => BookingResource::class,
            'meta'    => true,
            'status'  => 200,
        ]);
    }

    public function show($id)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canShow($booking);

        return ResponseService::response([
            'success' => true,
            'data'    => $booking,
            'resource' => BookingResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $data = BookingPermission::create($data);

        $booking = $this->bookingService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.booking.create',
            'data'    => $booking,
            'resource' => BookingResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $booking = $this->bookingService->show($id);

        BookingPermission::canUpdate($booking);

        $booking = $this->bookingService->update($booking, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.booking.update',
            'data'    => $booking,
            'resource' => BookingResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canDelete($booking);

        $this->bookingService->destroy($booking);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.booking.delete',
            'status'  => 200,
        ]);
    }

    // add transaction
    public function addTransaction($id, AddTransactionRequest $request)
    {
        $data = $request->validated();

        $booking = $this->bookingService->show($id);

        BookingPermission::canUpdate($booking);

        $booking = $this->bookingService->addTransaction($booking, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.booking.addTransaction',
            'data'    => $booking,
            'resource' => BookingResource::class,
            'status'  => 200,
        ]);
    }
}
