<?php


namespace App\Http\Services;

use App\Http\Notifications\BookingNotification;
use App\Http\Permissions\BookingPermission;
use App\Models\Booking;
use App\Models\BookingPrice;
use App\Models\Listing;
use App\Models\Setting;
use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;
use Illuminate\Support\Carbon;

class BookingService
{
    public function index($filters = [])
    {
        $query = Booking::query()->with(['host', 'guest', 'listing', 'prices', 'review.user']);

        $searchFields = [
            'message',
            'host_notes', 
            'admin_notes',
            'listing.title',
            'host.first_name',
            'host.last_name',
            'guest.first_name',
            'guest.last_name',
            'id',
        ];
        $numericFields = ['price', 'commission', 'service_fees', 'adults_count', 'children_count', 'infants_count', 'pets_count'];
        $dateFields = ['start_date', 'end_date', 'created_at'];
        $exactMatchFields = [
            'id',
            'host_id',
            'guest_id',
            'listing_id',
            'status',
            'payment_method',
            'currency',
        ];
        $inFields = [];

        $query = BookingPermission::filterIndex($query);

        $filteredQuery = FilterService::applyFilters(
            $query,
            $filters,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields,
            false,
        );

        $countQuery = (clone $filteredQuery);
        $countQuery->getQuery()->orders = null;

        $statusCounts = $countQuery
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Count all bookings (without status filter)
        $allCountQuery = (clone $query);
        $allCountQuery->getQuery()->orders = null;
        $allCount = $allCountQuery->count();

        $statusCounts['ALL'] = $allCount;

        $finalQuery = FilterService::applyFilters(
            $filteredQuery,
            $filters,
            [],
            [],
            [],
            ['status'],
            ['status'],
            false,
        );

        return [
            'bookings' => $finalQuery->latest()->paginate($filters['limit'] ?? 20),
            'bookings_status_count' => [
                'all_count' => $statusCounts['ALL'] ?? 0,
                'pending_count' => $statusCounts['pending'] ?? 0,
                'accepted_count' => $statusCounts['accepted'] ?? 0,
                'confirmed_count' => $statusCounts['confirmed'] ?? 0,
                'completed_count' => $statusCounts['completed'] ?? 0,
                'cancelled_count' => $statusCounts['cancelled'] ?? 0,
                'rejected_count' => $statusCounts['rejected'] ?? 0,
            ],
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
            }elseif ($booking->status == 'confirmed') {
                BookingNotification::confirmed($booking);
            }elseif ($booking->status == 'completed') {
                BookingNotification::completed($booking);
            }elseif ($booking->status == 'cancelled') {
                BookingNotification::cancelled($booking);
            }elseif ($booking->status == 'rejected') {
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

        BookingNotification::addTransaction($transaction);

        $booking->load(['host', 'guest', 'listing', 'transactions', 'prices', 'review.user']);

        return $booking;
    }
}
