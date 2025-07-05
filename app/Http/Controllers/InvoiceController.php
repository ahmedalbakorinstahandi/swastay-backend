<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generateInvoice($booking_id)
    {

        $language = 'ar';

        $booking = Booking::find($booking_id);

        

        // تجهيز مصفوفة البيانات
        $invoice_data = [
            'booking_id' => $booking->id,
            'invoice_date' => $booking->created_at->format('Y-m-d'),
            'guest_name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
            'guest_phone' => $booking->guest->country_code . $booking->guest->phone_number,
            'guest_email' => $booking->guest->email,
            'booking_status' => $booking->status,
            'listing_name' => $booking->listing->title[$language],
            'check_in_date' => $booking->rule->check_in_time->format('h:i A'),
            'check_out_date' => $booking->rule->check_out_time->format('h:i A'),
            'nights_count' => $booking->prices->count(),
            'guests_count' => $booking->adults_count + $booking->children_count + $booking->infants_count,
            'payment_method' => $booking->transactions->first()->method ?? 'none',
            'payment_status' => $booking->transactions->first()->status ?? 'pending',
            'payment_date' => $booking->transactions->first()->created_at->format('Y-m-d'),
            'currency' => 'دولار',

            // تفصيل الأسعار حسب نوع اليوم
            'pricing_breakdown' => $this->calculatePricingBreakdown($booking),

            'subtotal' => $booking->getTotalPriceAttribute(),
            'service_fee_percentage' => $booking->service_fees ?? 0,
            'service_fee' => $booking->getFinalTotalPriceAttribute() - $booking->getTotalPriceAttribute(),
            'tax_amount' => 0, // TODO: add tax amount
            'total_amount' => $booking->getFinalTotalPriceAttribute(),

            'qr_code_url' => "https://sawastay.com/bookings/{$booking->id}",
            'contact_phone' => '+963-xxx-xxxxxxx',

            'notes' => [
                'يرجى الاحتفاظ بهذه الفاتورة لأغراض السكن أو الاسترداد',
                'تم تأكيد الحجز من قبل إدارة SawaStay',
                'يرجى الوصول في الوقت المحدد (عادةً الساعة 2:00 مساءً)',
                'يرجى المغادرة في الوقت المحدد (عادةً الساعة 11:00 صباحاً)',
                'للاستفسارات، يرجى التواصل معنا على: +963-xxx-xxxxxxx'
            ],

            'links' => [
                ['url' => 'https://sawastay.com/terms', 'text' => '📜 الشروط والأحكام'],
                ['url' => 'https://sawastay.com/privacy', 'text' => '🔒 سياسة الخصوصية'],
                ['url' => 'https://sawastay.com/cancellation', 'text' => '↩️ سياسة الإلغاء'],
                ['url' => 'https://sawastay.com/contact', 'text' => '📞 اتصل بنا']
            ]
        ];

        // إرجاع الفاتورة العربية
        return view('invoice_ar', compact('invoice_data'));

        // أو إرجاع الفاتورة الإنجليزية
        // return view('invoice_en', compact('invoice_data'));
    }


    private function calculatePricingBreakdown($booking)
    {
        $breakdown = [];

        $booking->prices->each(function ($price) use ($breakdown) {
            $breakdown[] = [
                'description' => $price->type == 'normal' ? 'أيام عادية' : 'أيام نهاية الأسبوع',
                'nights' => 1,
                'price_per_night' => $price->price,
                'total' => $price->price
            ];
        });

        // while ($current_date < $check_out) {
        //     if ($current_date->isWeekend()) {
        //         $weekend_nights++;
        //     } else {
        //         $weekday_nights++;
        //     }
        //     $current_date->addDay();
        // }

        // if ($weekday_nights > 0) {
        //     $breakdown[] = [
        //         'description' => 'أيام عادية',
        //         'nights' => $weekday_nights,
        //         'price_per_night' => $booking->listing->weekday_price,
        //         'total' => $weekday_nights * $booking->listing->weekday_price
        //     ];
        // }

        // if ($weekend_nights > 0) {
        //     $breakdown[] = [
        //         'description' => 'أيام نهاية الأسبوع',
        //         'nights' => $weekend_nights,
        //         'price_per_night' => $booking->listing->weekend_price,
        //         'total' => $weekend_nights * $booking->listing->weekend_price
        //     ];
        // }

        return $breakdown;
    }
}
