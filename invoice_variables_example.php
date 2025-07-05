<?php

// مثال لمصفوفة البيانات المطلوبة للفاتورة
$invoice_data = [
    // معلومات أساسية
    'booking_id' => 'BK-2025-001',
    'invoice_date' => '2025-01-15',
    'logo_url' => 'https://www.sawastay.com/brand/logo.svg',
    'website_url' => 'www.sawastay.com',
    
    // معلومات الضيف
    'guest_name' => 'أحمد العلي',
    'guest_phone' => '+963-9xxxxxxx',
    'guest_email' => 'ahmed@example.com',
    
    // معلومات الحجز
    'booking_status' => 'مؤكد',
    'listing_name' => 'شقة فاخرة - دمشق، باب شرقي',
    'check_in_date' => '10 يوليو 2025',
    'check_out_date' => '14 يوليو 2025',
    'nights_count' => '4',
    'guests_count' => '2',
    
    // معلومات الدفع
    'payment_method' => 'ShamCash',
    'payment_status' => 'مدفوع',
    'payment_date' => '2025-01-15',
    
    // معلومات العملة
    'currency' => 'دولار',
    
    // تفصيل الأسعار (الجديد - يمكن استخدامه لعرض أسعار مختلفة للأيام العادية وأيام نهاية الأسبوع)
    'pricing_breakdown' => [
        [
            'description' => 'أيام عادية',
            'nights' => '2',
            'price_per_night' => '50',
            'total' => '100'
        ],
        [
            'description' => 'أيام نهاية الأسبوع',
            'nights' => '2',
            'price_per_night' => '75',
            'total' => '150'
        ]
    ],
    
    // أو يمكن استخدام الطريقة البسيطة (بدون تفصيل)
    // 'price_per_night' => '50',
    // 'subtotal' => '200',
    
    // المبالغ النهائية
    'subtotal' => '250',
    'service_fee_percentage' => '5',
    'service_fee' => '12.5',
    'tax_amount' => '0',
    'total_amount' => '262.5',
    
    // معلومات إضافية
    'qr_code_url' => 'https://sawastay.com/booking/BK-2025-001',
    'contact_phone' => '+963-xxx-xxxxxxx',
    
    // الملاحظات
    'notes' => [
        'يرجى الاحتفاظ بهذه الفاتورة لأغراض السكن أو الاسترداد',
        'تم تأكيد الحجز من قبل إدارة SawaStay',
        'يرجى الوصول في الوقت المحدد (عادةً الساعة 2:00 مساءً)',
        'يرجى المغادرة في الوقت المحدد (عادةً الساعة 11:00 صباحاً)',
        'للاستفسارات، يرجى التواصل معنا على: +963-xxx-xxxxxxx'
    ],
    
    // الروابط
    'links' => [
        [
            'url' => 'https://sawastay.com/terms',
            'text' => '📜 الشروط والأحكام'
        ],
        [
            'url' => 'https://sawastay.com/privacy',
            'text' => '🔒 سياسة الخصوصية'
        ],
        [
            'url' => 'https://sawastay.com/cancellation',
            'text' => '↩️ سياسة الإلغاء'
        ],
        [
            'url' => 'https://sawastay.com/contact',
            'text' => '📞 اتصل بنا'
        ]
    ]
];

// مثال للاستخدام في Controller
/*
public function generateInvoice($booking_id)
{
    // جلب بيانات الحجز من قاعدة البيانات
    $booking = Booking::find($booking_id);
    
    // تجهيز مصفوفة البيانات
    $invoice_data = [
        'booking_id' => $booking->id,
        'invoice_date' => now()->format('Y-m-d'),
        'guest_name' => $booking->guest_name,
        'guest_phone' => $booking->guest_phone,
        'guest_email' => $booking->guest_email,
        'booking_status' => $booking->status,
        'listing_name' => $booking->listing->name,
        'check_in_date' => $booking->check_in_date->format('d F Y'),
        'check_out_date' => $booking->check_out_date->format('d F Y'),
        'nights_count' => $booking->nights_count,
        'guests_count' => $booking->guests_count,
        'payment_method' => $booking->payment_method,
        'payment_status' => $booking->payment_status,
        'payment_date' => $booking->payment_date->format('Y-m-d'),
        'currency' => 'دولار',
        
        // تفصيل الأسعار حسب نوع اليوم
        'pricing_breakdown' => $this->calculatePricingBreakdown($booking),
        
        'subtotal' => $booking->subtotal,
        'service_fee_percentage' => 5,
        'service_fee' => $booking->service_fee,
        'tax_amount' => $booking->tax_amount,
        'total_amount' => $booking->total_amount,
        
        'qr_code_url' => "https://sawastay.com/booking/{$booking->id}",
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

// دالة لحساب تفصيل الأسعار
private function calculatePricingBreakdown($booking)
{
    $breakdown = [];
    $check_in = $booking->check_in_date;
    $check_out = $booking->check_out_date;
    
    $current_date = $check_in->copy();
    $weekend_nights = 0;
    $weekday_nights = 0;
    
    while ($current_date < $check_out) {
        if ($current_date->isWeekend()) {
            $weekend_nights++;
        } else {
            $weekday_nights++;
        }
        $current_date->addDay();
    }
    
    if ($weekday_nights > 0) {
        $breakdown[] = [
            'description' => 'أيام عادية',
            'nights' => $weekday_nights,
            'price_per_night' => $booking->listing->weekday_price,
            'total' => $weekday_nights * $booking->listing->weekday_price
        ];
    }
    
    if ($weekend_nights > 0) {
        $breakdown[] = [
            'description' => 'أيام نهاية الأسبوع',
            'nights' => $weekend_nights,
            'price_per_night' => $booking->listing->weekend_price,
            'total' => $weekend_nights * $booking->listing->weekend_price
        ];
    }
    
    return $breakdown;
}
*/

// مثال للنسخة الإنجليزية
$invoice_data_en = [
    'booking_id' => 'BK-2025-001',
    'invoice_date' => '2025-01-15',
    'logo_url' => 'https://www.sawastay.com/brand/logo.svg',
    'website_url' => 'www.sawastay.com',
    
    'guest_name' => 'Ahmed Ali',
    'guest_phone' => '+963-9xxxxxxx',
    'guest_email' => 'ahmed@example.com',
    
    'booking_status' => 'Confirmed',
    'listing_name' => 'Luxury Apartment - Damascus, Bab Sharqi',
    'check_in_date' => 'July 10, 2025',
    'check_out_date' => 'July 14, 2025',
    'nights_count' => '4',
    'guests_count' => '2',
    
    'payment_method' => 'ShamCash',
    'payment_status' => 'Paid',
    'payment_date' => '2025-01-15',
    
    'currency' => 'USD',
    
    'pricing_breakdown' => [
        [
            'description' => 'Weekdays',
            'nights' => '2',
            'price_per_night' => '50',
            'total' => '100'
        ],
        [
            'description' => 'Weekends',
            'nights' => '2',
            'price_per_night' => '75',
            'total' => '150'
        ]
    ],
    
    'subtotal' => '250',
    'service_fee_percentage' => '5',
    'service_fee' => '12.5',
    'tax_amount' => '0',
    'total_amount' => '262.5',
    
    'qr_code_url' => 'https://sawastay.com/booking/BK-2025-001',
    'contact_phone' => '+963-xxx-xxxxxxx',
    
    'notes' => [
        'Please keep this invoice for accommodation or refund purposes',
        'Booking has been confirmed by SawaStay management',
        'Please arrive at the specified time (usually 2:00 PM)',
        'Please check out at the specified time (usually 11:00 AM)',
        'For inquiries, please contact us at: +963-xxx-xxxxxxx'
    ],
    
    'links' => [
        ['url' => 'https://sawastay.com/terms', 'text' => '📜 Terms & Conditions'],
        ['url' => 'https://sawastay.com/privacy', 'text' => '🔒 Privacy Policy'],
        ['url' => 'https://sawastay.com/cancellation', 'text' => '↩️ Cancellation Policy'],
        ['url' => 'https://sawastay.com/contact', 'text' => '📞 Contact Us']
    ]
];

?> 