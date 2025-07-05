<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Booking Invoice - SawaStay</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap');
    
    body {
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 40px;
      color: #333;
      background: #f9f9f9;
      font-weight: 400;
      line-height: 1.6;
    }

    .invoice {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      border: 1px solid #e0e0e0;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 35px;
      border-bottom: 3px solid #F2506D;
      padding-bottom: 25px;
      position: relative;
    }

    .header::after {
      content: '';
      position: absolute;
      bottom: -3px;
      right: 0;
      width: 50px;
      height: 3px;
      background: #F2506D;
    }

    .logo {
      width: 120px;
      height: auto;
    }

    .invoice-info {
      text-align: right;
    }

    .invoice-number {
      font-size: 18px;
      font-weight: 700;
      color: #F2506D;
      margin-bottom: 5px;
      letter-spacing: 0.5px;
    }

    .invoice-date {
      color: #666;
      font-size: 14px;
      font-weight: 400;
    }

    h1 {
      text-align: center;
      color: #F2506D;
      margin: 0;
      font-weight: 800;
      font-size: 28px;
      letter-spacing: 1px;
      margin-bottom: 20px;
    }

    .section {
      margin-bottom: 25px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #F2506D;
      margin-bottom: 15px;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 8px;
      letter-spacing: 0.3px;
    }

    .guest-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 20px;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .info-label {
      font-weight: 600;
      color: #555;
      font-size: 14px;
    }

    .info-value {
      color: #333;
      font-weight: 500;
      font-size: 14px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    table th, table td {
      border: 1px solid #e0e0e0;
      padding: 15px 12px;
      text-align: left;
    }

    table th {
      background-color: #fef2f4;
      font-weight: 600;
      font-size: 14px;
      letter-spacing: 0.2px;
    }

    .total-row {
      background-color: #f5f5f5;
      font-weight: 700;
      font-size: 15px;
    }

    .payment-method {
      background: linear-gradient(135deg, #f8f9fa 0%, #fef2f4 100%);
      padding: 20px;
      border-radius: 10px;
      border-right: 5px solid #F2506D;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .payment-method p {
      margin: 8px 0;
      font-size: 14px;
    }

    .links {
      display: flex;
      justify-content: space-around;
      margin: 25px 0;
      flex-wrap: wrap;
      gap: 15px;
    }

    .links a {
      display: inline-block;
      padding: 12px 18px;
      color: #F2506D;
      text-decoration: none;
      border: 2px solid #F2506D;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-weight: 500;
      font-size: 13px;
      background: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
    }

    .links a:hover {
      background: linear-gradient(135deg, #F2506D 0%, #e91e63 100%);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(242, 80, 109, 0.3);
    }
    
    .links a:active {
      transform: translateY(0) scale(0.98);
      box-shadow: 0 2px 8px rgba(242, 80, 109, 0.2);
    }

    .footer {
      text-align: center;
      margin-top: 35px;
      padding: 25px;
      background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
      border-radius: 10px;
      font-weight: 600;
      color: #2e7d32;
      font-size: 16px;
      line-height: 1.8;
      box-shadow: 0 3px 15px rgba(0,0,0,0.08);
      border: 1px solid #c8e6c9;
    }

    .qr-code {
      text-align: center;
      margin: 20px 0;
    }

    .qr-code img {
      width: 120px;
      height: 120px;
      border: 2px solid #F2506D;
      border-radius: 8px;
      padding: 5px;
      background: white;
    }

    .qr-code p {
      font-size: 12px;
      color: #666;
      margin-top: 8px;
      font-weight: 500;
    }

    @media print {
      body {
        margin: 0;
        background: white;
      }
      .invoice {
        box-shadow: none;
        border: 1px solid #ccc;
      }
      
      .links {
        display: none !important;
      }
      
      table {
        box-shadow: none !important;
        border: 1px solid #000 !important;
      }
      
      table th, table td {
        border: 1px solid #000 !important;
        background: white !important;
      }
      
      .qr-code img {
        border: 1px solid #000 !important;
        background: white !important;
      }
    }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="header">
      <img src="{{ $invoice_data['logo_url'] ?? 'https://www.sawastay.com/brand/logo.svg' }}" alt="SawaStay Logo" class="logo">
      <div class="invoice-info">
        <div class="invoice-number">Invoice #{{ $invoice_data['booking_id'] ?? 'BK-2025-001' }}</div>
        <div class="invoice-date">Issue Date: {{ $invoice_data['invoice_date'] ?? date('d-m-Y') }}</div>
      </div>
    </div>

    <h1>Booking Invoice</h1>

    <div class="section">
      <div class="section-title">Booking Information</div>
      <div class="guest-info">
        <div class="info-item">
          <span class="info-label">Booking ID:</span>
          <span class="info-value">#{{ $invoice_data['booking_id'] ?? 'BK-2025-001' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Booking Status:</span>
          <span class="info-value">{{ $invoice_data['booking_status'] ?? 'Confirmed' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Guest Name:</span>
          <span class="info-value">{{ $invoice_data['guest_name'] ?? 'Ahmed Ali' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Phone Number:</span>
          <span class="info-value">{{ $invoice_data['guest_phone'] ?? '+963-9xxxxxxx' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Email:</span>
          <span class="info-value">{{ $invoice_data['guest_email'] ?? 'guest@example.com' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Accommodation:</span>
          <span class="info-value">{{ $invoice_data['listing_name'] ?? 'Luxury Apartment - Damascus, Bab Sharqi' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Check-in Date:</span>
          <span class="info-value">{{ $invoice_data['check_in_date'] ?? 'July 10, 2025' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Check-out Date:</span>
          <span class="info-value">{{ $invoice_data['check_out_date'] ?? 'July 14, 2025' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Number of Nights:</span>
          <span class="info-value">{{ $invoice_data['nights_count'] ?? '4' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Number of Guests:</span>
          <span class="info-value">{{ $invoice_data['guests_count'] ?? '2' }}</span>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Invoice Details</div>
      <table>
        <thead>
          <tr>
            <th>Item</th>
            <th>Details</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($invoice_data['pricing_breakdown']) && is_array($invoice_data['pricing_breakdown']))
            @foreach($invoice_data['pricing_breakdown'] as $breakdown)
              <tr>
                <td>{{ $breakdown['description'] ?? 'Night Price' }}</td>
                <td>{{ $breakdown['nights'] ?? '1' }} nights × {{ $breakdown['price_per_night'] ?? '50' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
                <td>{{ $breakdown['total'] ?? '50' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td>Accommodation Price</td>
              <td>{{ $invoice_data['nights_count'] ?? '4' }} nights × {{ $invoice_data['price_per_night'] ?? '50' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
              <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
            </tr>
          @endif
          <tr>
            <td>Subtotal</td>
            <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
            <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
          </tr>
          <tr>
            <td>Service Fee ({{ $invoice_data['service_fee_percentage'] ?? '5' }}%)</td>
            <td>{{ $invoice_data['service_fee'] ?? '10' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
            <td>{{ $invoice_data['service_fee'] ?? '10' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
          </tr>
          <tr>
            <td>Tax</td>
            <td>{{ $invoice_data['tax_amount'] ?? '0' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
            <td>{{ $invoice_data['tax_amount'] ?? '0' }} {{ $invoice_data['currency'] ?? 'USD' }}</td>
          </tr>
          <tr class="total-row">
            <td><strong>Total</strong></td>
            <td><strong>{{ $invoice_data['total_amount'] ?? '210' }} {{ $invoice_data['currency'] ?? 'USD' }}</strong></td>
            <td><strong>{{ $invoice_data['total_amount'] ?? '210' }} {{ $invoice_data['currency'] ?? 'USD' }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="section">
      <div class="section-title">Payment Method</div>
      <div class="payment-method">
        <p><strong>Payment Method:</strong> {{ $invoice_data['payment_method'] ?? 'ShamCash' }}</p>
        <p><strong>Payment Status:</strong> {{ $invoice_data['payment_status'] ?? 'Paid' }}</p>
        <p><strong>Payment Date:</strong> {{ $invoice_data['payment_date'] ?? date('d-m-Y') }}</p>
      </div>
    </div>

    <div class="qr-code">
      <p><strong>Booking QR Code</strong></p>
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $invoice_data['qr_code_url'] ?? 'https://sawastay.com/booking/BK-2025-001' }}" alt="QR Code for SawaStay">
    </div>

    <div class="section">
      <div class="section-title" style="font-size: 14px; margin-bottom: 10px;">Important Notes</div>
      <ul style="font-size: 11px; line-height: 1.5; color: #666; padding-left: 15px; margin: 0;">
        @foreach($invoice_data['notes'] ?? [
          'Please keep this invoice for accommodation or refund purposes',
          'Booking has been confirmed by SawaStay management',
          'Please arrive at the specified time (usually 2:00 PM)',
          'Please check out at the specified time (usually 11:00 AM)',
          'For inquiries, please contact us at: ' . ($invoice_data['contact_phone'] ?? '+963-xxx-xxxxxxx')
        ] as $note)
          <li>{{ $note }}</li>
        @endforeach
      </ul>
    </div>

    <div class="section links">
      @foreach($invoice_data['links'] ?? [
        ['url' => 'https://sawastay.com/terms', 'text' => '📜 Terms & Conditions'],
        ['url' => 'https://sawastay.com/privacy', 'text' => '🔒 Privacy Policy'],
        ['url' => 'https://sawastay.com/cancellation', 'text' => '↩️ Cancellation Policy'],
        ['url' => 'https://sawastay.com/contact', 'text' => '📞 Contact Us']
      ] as $link)
        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['text'] }}</a>
      @endforeach
    </div>

    <div class="footer">
      ✅ Thank you for using SawaStay! <br>
      We wish you a comfortable and safe stay. <br>
      <small>{{ $invoice_data['website_url'] ?? 'www.sawastay.com' }}</small>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const links = document.querySelectorAll('.links a');
      
      links.forEach(link => {
        link.setAttribute('title', 'Click to open ' + link.textContent);
        
        link.addEventListener('click', function(e) {
          this.style.transform = 'scale(0.95)';
          this.style.transition = 'transform 0.15s ease';
          
          setTimeout(() => {
            this.style.transform = '';
          }, 150);
          
          window.open(this.href, '_blank');
          e.preventDefault();
        });
        
        link.addEventListener('mouseenter', function() {
          this.style.cursor = 'pointer';
        });
        
        link.addEventListener('mouseleave', function() {
          this.style.cursor = 'default';
        });
      });
    });
    
    window.addEventListener('beforeprint', function() {
      console.log('Preparing invoice for printing...');
    });
    
    window.addEventListener('afterprint', function() {
      console.log('Printing completed');
    });
  </script>
</body>
</html> 