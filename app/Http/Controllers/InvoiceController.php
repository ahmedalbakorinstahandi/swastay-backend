<?php

namespace App\Http\Controllers;

use App\Http\Permissions\BookingPermission;
use App\Models\Booking;
use App\Models\Setting;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function generateInvoice($booking_id, Request $request)
    {

        
        $language = explode(',', $request->header('Accept-Language', 'en'))[0];
        
        $booking = Booking::find($booking_id);

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }


        BookingPermission::canShow($booking);

        $phone = Setting::where('key', 'phone')->first()->value;


        // تجهيز مصفوفة البيانات
        $invoice_data = [
            'booking_id' => $booking->id,
            'invoice_date' => $booking->created_at->format('Y-m-d'),
            'guest_name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
            'guest_phone' => $booking->guest->country_code . $booking->guest->phone_number,
            'guest_email' => $booking->guest->email,
            'booking_status' => $booking->status,
            'listing_name' => $booking->listing->title[$language],
            'check_in_date' => $booking->check_in,
            'check_out_date' => $booking->check_out,
            'nights_count' => $booking->prices->count(),    
            'guests_count' => $booking->adults_count + $booking->children_count + $booking->infants_count,
            'payment_method' => $booking->transactions->first()->method ?? 'none',
            'payment_status' => $booking->transactions->first()->status ?? 'pending',
            'payment_date' => $booking->transactions->first()?->created_at->format('Y-m-d') ?? 'none',
            'currency' => 'دولار',

            // تفصيل الأسعار حسب نوع اليوم
            'pricing_breakdown' => $this->calculatePricingBreakdown($booking),

            'subtotal' => $booking->getTotalPriceAttribute(),
            'service_fee_percentage' => $booking->service_fees ?? 0,
            'service_fee' => $booking->getFinalTotalPriceAttribute() - $booking->getTotalPriceAttribute(),
            'tax_amount' => 0, // TODO: add tax amount
            'total_amount' => $booking->getFinalTotalPriceAttribute(),

            'qr_code_url' => "https://sawastay.com/bookings/{$booking->id}",
            'contact_phone' => $phone,

            'notes' =>

            $language == 'ar' ?
                [
                    'يرجى الاحتفاظ بهذه الفاتورة لأغراض السكن أو الاسترداد',
                    'تم تأكيد الحجز من قبل إدارة SawaStay',
                    'يرجى الوصول في الوقت المحدد (عادةً الساعة ' . $booking->check_in . ')',
                    'يرجى المغادرة في الوقت المحدد (عادةً الساعة ' . $booking->check_out . ')',
                    'للاستفسارات، يرجى التواصل معنا على: ' . $phone
                ] :
                [
                    'Please keep this invoice for your stay or refund',
                    'The booking has been confirmed by SawaStay',
                    'Please arrive at the specified time (usually at ' . $booking->check_in . ')',
                    'Please leave at the specified time (usually at ' . $booking->check_out . ')',
                    'For inquiries, please contact us on: ' . $phone
                ],

            'links' => [
                ['url' => 'https://www.sawastay.com/pages/terms', 'text' => $language == 'ar' ? '📜 الشروط والأحكام' : '📜 Terms and Conditions'],
                ['url' => 'https://www.sawastay.com/pages/privacy', 'text' => $language == 'ar' ? '🔒 سياسة الخصوصية' : '🔒 Privacy Policy'],
                ['url' => 'https://www.sawastay.com/pages/booking-policy', 'text' => $language == 'ar' ? '↩️ سياسة الحجز والإلغاء' : '↩️ Booking and Cancellation Policy'],
                ['url' => 'https://sawastay.com/contact', 'text' => $language == 'ar' ? '📞 اتصل بنا' : '📞 Contact Us']
            ]
        ];

        return view('invoice_' . $language, compact('invoice_data'));
    }

    public function generateInvoicePdf($booking_id, Request $request)
    {
        $language = explode(',', $request->header('Accept-Language', 'en'))[0];

        $booking = Booking::find($booking_id);

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.not_found');
        }

        BookingPermission::canShow($booking);

        $phone = Setting::where('key', 'phone')->first()->value;

        // تجهيز مصفوفة البيانات
        $invoice_data = [
            'booking_id' => $booking->id,
            'invoice_date' => $booking->created_at->format('Y-m-d'),
            'guest_name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
            'guest_phone' => $booking->guest->country_code . $booking->guest->phone_number,
            'guest_email' => $booking->guest->email,
            'booking_status' => $booking->status,
            'listing_name' => $booking->listing->title[$language],
            'check_in_date' => $booking->check_in,
            'check_out_date' => $booking->check_out,
            'nights_count' => $booking->prices->count(),
            'guests_count' => $booking->adults_count + $booking->children_count + $booking->infants_count,
            'payment_method' => $booking->transactions->first()->method ?? 'none',
            'payment_status' => $booking->transactions->first()->status ?? 'pending',
            'payment_date' => $booking->transactions->first()?->created_at->format('Y-m-d') ?? 'none',
            'currency' => 'دولار',

            // تفصيل الأسعار حسب نوع اليوم
            'pricing_breakdown' => $this->calculatePricingBreakdown($booking),

            'subtotal' => $booking->getTotalPriceAttribute(),
            'service_fee_percentage' => $booking->service_fees ?? 0,
            'service_fee' => $booking->getFinalTotalPriceAttribute() - $booking->getTotalPriceAttribute(),
            'tax_amount' => 0, // TODO: add tax amount
            'total_amount' => $booking->getFinalTotalPriceAttribute(),

            'qr_code_url' => "https://sawastay.com/bookings/{$booking->id}",
            'contact_phone' => $phone,

            'notes' =>

            $language == 'ar' ?
                [
                    'يرجى الاحتفاظ بهذه الفاتورة لأغراض السكن أو الاسترداد',
                    'تم تأكيد الحجز من قبل إدارة SawaStay',
                    'يرجى الوصول في الوقت المحدد (عادةً الساعة ' . $booking->check_in . ')',
                    'يرجى المغادرة في الوقت المحدد (عادةً الساعة ' . $booking->check_out . ')',
                    'للاستفسارات، يرجى التواصل معنا على: ' . $phone
                ] :
                [
                    'Please keep this invoice for your stay or refund',
                    'The booking has been confirmed by SawaStay',
                    'Please arrive at the specified time (usually at ' . $booking->check_in . ')',
                    'Please leave at the specified time (usually at ' . $booking->check_out . ')',
                    'For inquiries, please contact us on: ' . $phone
                ],

            'links' => [
                ['url' => 'https://www.sawastay.com/pages/terms', 'text' => $language == 'ar' ? '📜 الشروط والأحكام' : '📜 Terms and Conditions'],
                ['url' => 'https://www.sawastay.com/pages/privacy', 'text' => $language == 'ar' ? '🔒 سياسة الخصوصية' : '🔒 Privacy Policy'],
                ['url' => 'https://www.sawastay.com/pages/booking-policy', 'text' => $language == 'ar' ? '↩️ سياسة الحجز والإلغاء' : '↩️ Booking and Cancellation Policy'],
                ['url' => 'https://sawastay.com/contact', 'text' => $language == 'ar' ? '📞 اتصل بنا' : '📞 Contact Us']
            ]
        ];

        // Generate HTML using the same template
        $html = view('invoice_' . $language, compact('invoice_data'))->render();

        // Configure mPDF
        $mpdf = new Mpdf([
            'tempDir' => storage_path('framework/cache'),
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_left' => 5,
            'margin_right' => 5,
            'default_font' => 'cairo',
            'default_font_size' => 12,
            'useOTL' => 0xFF,
            'useKashida' => 75,
            'shrink_tables_to_fit' => 1,
            'use_kwt' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'title_magnification' => 1,
            'use_auto_font_size' => true
        ]);

        // Set document properties
        $mpdf->SetTitle("Invoice #{$booking->id}");
        $mpdf->SetAuthor('SawaStay');
        $mpdf->SetCreator('SawaStay System');

        // Set text direction based on language
        if ($language === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        // Register Cairo font for Arabic text
        $mpdf->fontdata['cairo'] = [
            'R' => 'Cairo-Regular.ttf',
            'B' => 'Cairo-Bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ];
        $mpdf->SetFont('cairo');

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Return PDF directly without saving to storage
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice_' . $booking->id . '.pdf"');
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
