<?php


namespace App\Http\Services;

use App\Http\Permissions\BookingPermission;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Setting;
use App\Services\FilterService;
use App\Services\MessageService;

class BookingService
{
    public function index($filters = [])
    {

        $query = Booking::query()->with(['host', 'guest', 'listing']);

        $query = BookingPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $filters,
            ['message', 'host_notes', 'admin_notes'],
            ['price', 'commission', 'service_fees', 'adults_count', 'children_count', 'infants_count', 'pets_count'],
            ['start_date', 'end_date'],
            ['status', 'payment_method', 'currency'],
            ['status', 'payment_method']
        );
    }

    public function show($id)
    {
        $booking = Booking::where('id', $id)->first();

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }

        $booking->load(['host', 'guest', 'listing']);

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

        $booking->load(['host', 'guest', 'listing']);

        return $booking;
    }

    public function update(Booking $booking, array $data)
    {
        $booking->update($data);

        $booking->load(['host', 'guest', 'listing']);

        return $booking;
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
    }
}
