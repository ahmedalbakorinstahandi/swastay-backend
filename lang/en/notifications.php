<?php

return [
    'welcome' => [
        'title' => 'Welcome to SawaStay',
        'body' => 'Welcome to the SawaStay family, we wish you a wonderful stay experience :first_name',
        'message1' => 'Hello :first_name :last_name, we are happy to have you with us! Your account has been created successfully and you can now enjoy our services. If you need any help, please contact us. You can visit our website at the following link: https://sawastay.com/',
        'message2' => 'Welcome to SawaStay!\n\nThank you for joining the SawaStay family!\nWe are excited to announce that our official launch date will be June 1, 2025, and we look forward to providing an exceptional experience for both hosts and guests alike.\n\nWe are here to support you every step of your journey as a host. If you have any questions or need any assistance, please contact us through:\n\nEmail: contact@sawastay.com\nPhone: +963 935 919 671\n\nWith best regards,\nThe SawaStay Team',
    ],

    'listing' => [
        'host' => [
            'approved' => [
                'title' => 'Listing Approved Successfully',
                'body' => 'Congratulations! Your listing :listing_id has been approved successfully. Guests can now book your listing and enjoy a wonderful stay. We wish you a great success in your journey with SawaStay.',
            ],
            'rejected' => [
                'title' => 'Listing Rejected',
                'body' => 'Your listing :listing_id has been rejected. Please check the reason and update your listing to be approved.',
            ],
            'paused' => [
                'title' => 'Listing Paused',
                'body' => 'Your listing :listing_id has been paused. Please check the reason and update your listing to be approved.',
            ],
            'first_created' => [
                'title' => 'Listing First Created',
                'body' => 'Your listing :listing_id has been created successfully. Guests can now book your listing and enjoy a wonderful stay. We wish you a great success in your journey with SawaStay.',
            ],
        ],
        'admin' => [
            'created' => [
                'title' => 'Listing Created',
                'body' => 'A new listing has been created by :full_name. Please review the listing and approve or reject it.',
            ],
        ],
    ],

    'booking' => [
        'host' => [
            'created' => [
                'title' => 'New Booking Request',
                'body' => 'A guest has requested to book your listing :listing_id for the booking :booking_id. Please review the booking details and respond as soon as possible.',
            ],
        ],
        'employee' => [
            'created' => [
                'title' => 'New Booking Request',
                'body' => 'A guest has requested to book your listing :listing_id for the booking :booking_id. Please review the booking details and respond as soon as possible.',
            ],
        ],

        'guest' => [
            'created' => [
                'title' => 'Booking Request Received',
                'body' => 'Your booking request #:booking_id for listing #:listing_id has been received successfully. We will check the property availability and get back to you shortly.',
                'message' => 'Welcome to SawaStay! 👋\nThank you for choosing SawaStay for your stay. Your booking request #:booking_id for listing #:listing_id has been received successfully ✨\nOur team will verify the property availability and get back to you shortly.\nWe are here to help you anytime! 🌟',
            ],
            'accepted' => [
                'title' => 'Booking Request Accepted',
                'body' => 'Your booking request #:booking_id for listing #:listing_id has been accepted successfully. The property availability has been verified, please complete the payment to confirm your booking.',
                'message' => 'Welcome to SawaStay! 👋\nThank you for choosing SawaStay for your stay. Your booking request #:booking_id for listing #:listing_id has been accepted successfully ✨\nThe property availability has been verified, please complete the payment to confirm your booking.\nYou can upload your payment proof through the following link:\nhttps://www.sawastay.com/en/bookings/:booking_id/transactions\nWe are here to help you anytime! 🌟',
            ],
            'confirmed' => [
                'title' => 'Booking Confirmed',
                'body' => 'Your booking #:booking_id for listing #:listing_id has been confirmed successfully. Have a wonderful stay!',
                'message' => 'Welcome to SawaStay! 👋\nThank you for choosing SawaStay for your stay. Your booking #:booking_id for listing #:listing_id has been confirmed successfully ✨\nYour payment has been verified and your booking is confirmed.\nHave a wonderful stay! 🌟\nWe are here to help you anytime! 🏠',
            ],
            'completed' => [
                'title' => 'Thank You for Staying with Us',
                'body' => 'Thank you for choosing SawaStay! We hope you enjoyed your stay at listing #:listing_id',
                'message' => 'Hello :first_name! 👋\nThank you so much for choosing SawaStay for your stay. We hope you enjoyed your experience with us at listing #:listing_id ✨\nWe are always happy to serve you and look forward to seeing you again on your next trip! 🌟\nThank you for your trust and we hope to see you soon 🏠',
            ],
            'cancelled' => [
                'title' => 'Booking Cancelled',
                'body' => 'Your booking #:booking_id for listing #:listing_id has been cancelled. We hope to see you soon on another trip.',
                'message' => 'Hello :first_name! 👋\nWe would like to inform you that your booking #:booking_id for listing #:listing_id has been cancelled ✨\nWe understand that circumstances may change, and we hope to see you soon on another trip 🌟\nThe SawaStay team is always at your service! 🏠',
            ],
            'rejected' => [
                'title' => 'Booking Request Rejected',
                'body' => 'Sorry, your booking request #:booking_id for listing #:listing_id has been rejected due to unavailability at this time. We are here to help you find the right place for your stay.',
                'message' => 'Hello :first_name! 👋\nWe regret to inform you that your booking request #:booking_id for listing #:listing_id has been rejected due to unavailability during the requested time ✨\nWe have many other options and suggestions that might suit you! We would be happy to help you find the perfect place for your stay 🏠\nOur team is ready to provide assistance and suitable recommendations for you 🌟\nWe are always here to serve you! 💫',
            ],
        ],



    ],

    'listing_review' => [
        'guest' => [
            'blocked' => [
                'title' => 'Your Review Has Been Blocked',
                'body' => 'Sorry :guest_first_name, your review :review_id for listing :listing_id has been blocked for violating our community guidelines. We ask you to adhere to our review guidelines and avoid using any inappropriate or offensive content.',
            ],
        ],
        'host' => [
            'blocked' => [
                'title' => 'Guest Review Blocked',
                'body' => 'Hello :host_first_name, don\'t worry! We have blocked review :review_id from guest :guest_first_name for listing :listing_id for violating our community guidelines. We always strive to protect your reputation and your listing\'s reputation from any inappropriate content.',
            ],
            'created' => [
                'title' => 'New Review Created',
                'body' => 'A new review has been created by :guest_first_name for listing :listing_id. Please review the review and approve or reject it.',
            ],
        ],
    ],

    'user' => [
        'admin' => [
            'verification' => [
                'title' => 'User Verification Request',
                'body' => 'A user has requested to verify their identity. Please review the request and approve or reject it.',
            ],
        ],
        'verification' => [
            'approved' => [
                'title' => 'Verification Approved',
                'body' => 'Hello :user_first_name, your verification has been approved successfully. You can now enjoy our services.',
                'message' => 'Hello :user_first_name, your verification has been approved successfully. You can now enjoy our services.',
            ],
            'rejected' => [
                'title' => 'Verification Rejected',
                'body' => 'Hello :user_first_name, sorry your verification has been rejected. Please check the reason and update your verification to be approved. If you need any help, our support team is always here to help you.',
                'message' => 'Hello :user_first_name, sorry your verification has been rejected. Please check the reason and update your verification to be approved. If you need any help, our support team is always here to help you.',
            ],
            'stopped' => [
                'title' => 'Your Account Has Been Stopped',
                'body' => 'Hello :user_first_name, sorry your account has been stopped. Please contact us for more information.',
                'message' => 'Hello :user_first_name, sorry your account has been stopped. Please contact us for more information.',
            ],
        ],
    ],

    'transaction' => [
        'admin' => [
            'add_transaction' => [
                'title' => 'New Transaction Added',
                'body' => 'A new transaction has been added to the booking #:booking_id by :full_name.',
            ],
        ],
    ],

    'admin' => [
        'user' => [
            'new' => [
                'title' => 'New User Registered',
                'body' => 'A new user has registered with the following details: :full_name.',
            ],
        ],
    ],

];
