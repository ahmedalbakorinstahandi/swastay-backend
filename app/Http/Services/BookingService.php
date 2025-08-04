<?php


namespace App\Http\Services;

use App\Http\Notifications\BookingNotification;
use App\Http\Permissions\BookingPermission;
use App\Models\Booking;
use App\Models\BookingPrice;
use App\Models\Listing;
use App\Models\Setting;
use App\Models\User;
use App\Services\DigiBankarPaymentService;
use App\Services\FilterService;
use App\Services\MessageService;
use Illuminate\Support\Carbon;

class BookingService
{
    public function index($filters = [])
    {
        $baseQuery = Booking::query()->with(['host', 'guest', 'listing', 'prices', 'review.user']);

        $baseQuery = BookingPermission::filterIndex($baseQuery);

        $statsQuery = clone $baseQuery;

        $statsQuery = FilterService::applyFilters(
            $statsQuery,
            $filters,
            ['message', 'host_notes', 'admin_notes'],
            ['price', 'commission', 'service_fees', 'adults_count', 'children_count', 'infants_count', 'pets_count'],
            ['start_date', 'end_date'],
            [],
            [],
            false,
        );

        $allBookings = $statsQuery->get();

        $bookings_status_count = [
            'all_count' => $allBookings->count(),
            'pending_count' => $allBookings->where('status', 'pending')->count(),
            'accepted_count' => $allBookings->where('status', 'accepted')->count(),
            'confirmed_count' => $allBookings->where('status', 'confirmed')->count(),
            'completed_count' => $allBookings->where('status', 'completed')->count(),
            'cancelled_count' => $allBookings->where('status', 'cancelled')->count(),
            'rejected_count' => $allBookings->where('status', 'rejected')->count(),
        ];

        $displayQuery = FilterService::applyFilters(
            $baseQuery,
            $filters,
            ['message', 'host_notes', 'admin_notes'],
            ['price', 'commission', 'service_fees', 'adults_count', 'children_count', 'infants_count', 'pets_count'],
            ['start_date', 'end_date'],
            ['status', 'payment_method', 'currency'],
            ['status', 'payment_method'],
            false,
        );

        return [
            'bookings' => $displayQuery->paginate($filters['limit'] ?? 20),
            'bookings_status_count' => $bookings_status_count,
        ];
    }

    public function show($id)
    {
        $booking = Booking::where('id', $id)->first();

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }

        $booking->load(['host', 'guest', 'listing', 'transactions', 'prices', 'review.user']);

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
        $data['price'] = 0;
        $data['currency'] = $listing->currency;






        $data['commission'] = $listing->commission;

        $serviceFeeSetting = Setting::where('key', 'service_fee')->first();
        $data['service_fees'] = $serviceFeeSetting ? $serviceFeeSetting->value : null;


        $booking = Booking::create($data);

        BookingNotification::created($booking);

        $booking_prices = [];

        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date'])->subDay();

        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $price = $listing->getFinalPriceAttribute($date);
            $booking_prices[] = [
                'price' => $price,
                'type' => $date->isWeekend() ? 'weekend' : 'normal',
                'date' => $date,
                'booking_id' => $booking->id,
            ];
        }

        $booking_prices = BookingPrice::insert($booking_prices);


        $booking->load(['host', 'guest', 'listing', 'transactions', 'prices', 'review.user']);

        return $booking;
    }





    public function update(Booking $booking, array $data)
    {
        $lastStatus = $booking->status;

        $booking->update($data);


        if ($lastStatus != $booking->status) {
            // "pending", "accepted", "confirmed", "completed", "cancelled", "rejected"
            if ($booking->status == 'accepted') {
                BookingNotification::accepted($booking);
            } elseif ($booking->status == 'confirmed') {
                BookingNotification::confirmed($booking);
            } elseif ($booking->status == 'completed') {
                BookingNotification::completed($booking);
            } elseif ($booking->status == 'cancelled') {
                BookingNotification::cancelled($booking);
            } elseif ($booking->status == 'rejected') {
                BookingNotification::rejected($booking);
            }
        }
        $booking->load(['host', 'guest', 'listing', 'transactions', 'prices', 'review.user']);

        return $booking;
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
    }

    public function addTransaction(Booking $booking, array $data)
    {


        $user = User::auth();

        $transaction = $booking->transactions()->create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'description' => [
                'ar' => 'دفع رصيد للحجز ' . $booking->id,
                'en' => 'Pay for booking ' . $booking->id,
            ],
            'method' => $data['method'],
            'attached' => $data['attached'] ?? null,
            'status' => 'pending',
            'type' => 'booking_payment',
            'direction' => 'in',
            'transactionable_id' => $booking->id,
            'transactionable_type' => Booking::class,
        ]);


        if ($data['method'] == 'crypto') {
            $paymentService = new DigiBankarPaymentService();

            $response = $paymentService->createRequest([
                'orderId' => $transaction->id,
                'total' => 0.01,
                'currency' => $booking->currency,
                'customerEmail' => $user->email,
                'validityPeriod' => 3600,
                'orderItems' => [
                    [
                        'id' => $transaction->id,
                        'name' => __('messages.digibankar.payment_for_booking', ['id' => $booking->id, 'title' => $booking->listing->title[app()->getLocale()]]),
                        'description' => __('messages.digibankar.payment_for_booking_description', ['id' => $booking->id, 'title' => $booking->listing->title[app()->getLocale()]]),
                        'price' => 0.01,
                        'quantity' => 1,
                    ]
                ],
            ]);


            $paymentLink = $response['paymentLink'] ?? null;
        }

        if ($data['method'] != 'crypto') {
            BookingNotification::addTransaction($transaction);
        }

        $booking->load(['host', 'guest', 'listing', 'transactions', 'prices', 'review.user']);

        return [
            'booking' => $booking,
            'payment_link' => $paymentLink ?? null,
        ];
    }
}
