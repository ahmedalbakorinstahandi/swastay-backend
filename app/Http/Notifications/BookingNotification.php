<?php

namespace App\Http\Notifications;

use App\Models\User;
use App\Services\FirebaseService;
use App\Services\WhatsappMessageService;

class BookingNotification
{
    public static function created($booking)
    {
        // to host , to admin , to employee , to guest
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->host_id,
            [
                $booking->host_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.host.created.title',
            'notifications.booking.host.created.body',
            [
                'booking_id' => '#' . $booking->id,
            ],
            [],
        );




        $admin_ids = User::where('role', 'admin')->pluck('id')->toArray();

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $admin_ids,
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.employee.created.title',
            'notifications.booking.employee.created.body',
            [
                'booking_id' => '#' . $booking->id,
            ],
            [],
        );

        $employee_ids = User::where('role', 'employee')->pluck('id')->toArray();

        FirebaseService::sendToTopicAndStorage(
            'role-employee',
            $employee_ids,
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.employee.created.title',
            'notifications.booking.employee.created.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
            ],
            [],
        );

        // to guest
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.created.title',
            'notifications.booking.guest.created.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.created.message', [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }


    public static function accepted($booking)
    {
        // to guest
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.accepted.title',
            'notifications.booking.guest.accepted.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.accepted.message', [
                'booking_id' =>  $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }

    public static function confirmed($booking)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.confirmed.title',
            'notifications.booking.guest.confirmed.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.confirmed.message', [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }

    public static function completed($booking)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.completed.title',
            'notifications.booking.guest.completed.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.completed.message', [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }

    public static function rejected($booking)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.rejected.title',
            'notifications.booking.guest.rejected.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.rejected.message', [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }

    public static function cancelled($booking)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $booking->guest_id,
            [
                $booking->guest_id,
            ],
            [
                'notifiable_id' => $booking->id,
                'notifiable_type' => 'booking',
            ],
            'notifications.booking.guest.cancelled.title',
            'notifications.booking.guest.cancelled.body',
            [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ],
            [],
        );

        WhatsappMessageService::send(
            $booking->guest->country_code . $booking->guest->phone_number,
            __('notifications.booking.guest.cancelled.message', [
                'booking_id' => '#' . $booking->id,
                'listing_id' => '#' . $booking->listing_id,
                'first_name' => $booking->guest->first_name,
            ]),
        );
    }

    public static function addTransaction($transaction)
    {
        // to admin

        $admin_ids = User::where('role', 'admin')->pluck('id')->toArray();
        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $admin_ids,
            [
                'notifiable_id' => $transaction->id,
                'notifiable_type' => 'transaction',
            ],
            'notifications.transaction.admin.add_transaction.title',
            'notifications.transaction.admin.add_transaction.body',
            [
                'transaction_id' => '#' . $transaction->id,
                'booking_id' => '#' . $transaction->transactionable_id,
                'amount' => $transaction->amount,
                'method' => $transaction->method,
                'full_name' => $transaction->user->first_name . ' ' . $transaction->user->last_name,
            ],
            [],
        );
    }
}
