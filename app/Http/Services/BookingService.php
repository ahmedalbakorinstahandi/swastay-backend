<?php


namespace App\Http\Services;

use App\Http\Permissions\BookingPermission;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Setting;
use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;

class BookingService
{
    public function index($filters = [])
    {

        $query = Booking::query()->with(['host', 'guest', 'listing']);

        $query = BookingPermission::filterIndex($query);

        $query = FilterService::applyFilters(
            $query,
            $filters,
            ['message', 'host_notes', 'admin_notes'],
            ['price', 'commission', 'service_fees', 'adults_count', 'children_count', 'infants_count', 'pets_count'],
            ['start_date', 'end_date'],
            ['status', 'payment_method', 'currency'],
            ['status', 'payment_method'],
            false,
        );


        $bookings = $query->get();

        $bookings_status_count = [
            'all_count' => $bookings->count(),
            'pending_count' => $bookings->where('status', 'pending')->count(),
            'accepted_count' => $bookings->where('status', 'accepted')->count(),
            'confirmed_count' => $bookings->where('status', 'confirmed')->count(),
            'completed_count' => $bookings->where('status', 'completed')->count(),
            'cancelled_count' => $bookings->where('status', 'cancelled')->count(),
            'rejected_count' => $bookings->where('status', 'rejected')->count(),
        ];

        return [
            'bookings' => $query->paginate($data['limit'] ?? 20),
            'bookings_status_count' => $bookings_status_count,
        ];
    }

    public function show($id)
    {
        $booking = Booking::where('id', $id)->first();

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }

        $booking->load(['host', 'guest', 'listing', 'transactions']);

        return $booking;
    }

    public function create(array $data)
    {

        $listing = Listing::find($data['listing_id']);

        $data['host_id'] = $listing->host_id;

        $rule = $listing->rule;

        if ($rule) {
            if ($rule->check_in_time) {
                $data['check_in'] = $rule->check_in_time;
            }
            if ($rule->check_out_time) {
                $data['check_out'] = $rule->check_out_time;
            }
        }

        $data['status'] = 'pending';
        $data['price'] = $listing->price;
        $data['currency'] = $listing->currency;


        $data['commission'] = $listing->commission;

        $serviceFeeSetting = Setting::where('key', 'service_fee')->first();
        $data['service_fees'] = $serviceFeeSetting ? $serviceFeeSetting->value : null;


        $booking = Booking::create($data);

        $booking->load(['host', 'guest', 'listing', 'transactions']);

        return $booking;
    }

    public function update(Booking $booking, array $data)
    {
        $booking->update($data);

        $booking->load(['host', 'guest', 'listing', 'transactions']);

        return $booking;
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
    }

    public function addTransaction(Booking $booking, array $data){


        $user = User::auth();

        $booking->transactions()->create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'description' => [
                'ar' => 'دفع رصيد للحجز ' . $booking->id,
                'en' => 'Pay for booking ' . $booking->id,
            ],
            'method' => $data['method'],
            'attached' => $data['attached'],
            'status' => 'pending',
            'type' => 'booking_payment',
            'direction' => 'in',
            'transactionable_id' => $booking->id,
            'transactionable_type' => Booking::class,
        ]);

        $booking->load(['host', 'guest', 'listing', 'transactions']);

        return $booking;
    }
}
