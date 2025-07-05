<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فاتورة حجز - SawaStay</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap');
    
    body {
      font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      left: 0;
      width: 50px;
      height: 3px;
      background: #F2506D;
    }

    .logo {
      width: 120px;
      height: auto;
    }

    .invoice-info {
      text-align: left;
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
      text-align: right;
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
      border-left: 5px solid #F2506D;
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
        <div class="invoice-number">رقم الفاتورة: #{{ $invoice_data['booking_id'] ?? 'BK-2025-001' }}</div>
        <div class="invoice-date">تاريخ الإصدار: {{ $invoice_data['invoice_date'] ?? date('d-m-Y') }}</div>
      </div>
    </div>

    <h1>فاتورة حجز</h1>

    <div class="section">
      <div class="section-title">معلومات الحجز</div>
      <div class="guest-info">
        <div class="info-item">
          <span class="info-label">رقم الحجز:</span>
          <span class="info-value">#{{ $invoice_data['booking_id'] ?? 'BK-2025-001' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">حالة الحجز:</span>
          <span class="info-value">{{ $invoice_data['booking_status'] ?? 'مؤكد' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">اسم الضيف:</span>
          <span class="info-value">{{ $invoice_data['guest_name'] ?? 'أحمد العلي' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">رقم الهاتف:</span>
          <span class="info-value">{{ $invoice_data['guest_phone'] ?? '+963-9xxxxxxx' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">البريد الإلكتروني:</span>
          <span class="info-value">{{ $invoice_data['guest_email'] ?? 'guest@example.com' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">مكان الإقامة:</span>
          <span class="info-value">{{ $invoice_data['listing_name'] ?? 'شقة فاخرة - دمشق، باب شرقي' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">تاريخ الوصول:</span>
          <span class="info-value">{{ $invoice_data['check_in_date'] ?? '10 يوليو 2025' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">تاريخ المغادرة:</span>
          <span class="info-value">{{ $invoice_data['check_out_date'] ?? '14 يوليو 2025' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">عدد الليالي:</span>
          <span class="info-value">{{ $invoice_data['nights_count'] ?? '4' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">عدد الضيوف:</span>
          <span class="info-value">{{ $invoice_data['guests_count'] ?? '2' }}</span>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">تفاصيل الفاتورة</div>
      <table>
        <thead>
          <tr>
            <th>البند</th>
            <th>التفاصيل</th>
            <th>المبلغ</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($invoice_data['pricing_breakdown']) && is_array($invoice_data['pricing_breakdown']))
            @foreach($invoice_data['pricing_breakdown'] as $breakdown)
              <tr>
                <td>{{ $breakdown['description'] ?? 'سعر الليلة' }}</td>
                <td>{{ $breakdown['nights'] ?? '1' }} ليالي × {{ $breakdown['price_per_night'] ?? '50' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
                <td>{{ $breakdown['total'] ?? '50' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td>سعر الإقامة</td>
              <td>{{ $invoice_data['nights_count'] ?? '4' }} ليالي × {{ $invoice_data['price_per_night'] ?? '50' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
              <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
            </tr>
          @endif
          <tr>
            <td>المجموع الفرعي</td>
            <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
            <td>{{ $invoice_data['subtotal'] ?? '200' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
          </tr>
          <tr>
            <td>رسوم الخدمة ({{ $invoice_data['service_fee_percentage'] ?? '5' }}%)</td>
            <td>{{ $invoice_data['service_fee'] ?? '10' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
            <td>{{ $invoice_data['service_fee'] ?? '10' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
          </tr>
          <tr>
            <td>الضريبة</td>
            <td>{{ $invoice_data['tax_amount'] ?? '0' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
            <td>{{ $invoice_data['tax_amount'] ?? '0' }} {{ $invoice_data['currency'] ?? 'دولار' }}</td>
          </tr>
          <tr class="total-row">
            <td><strong>الإجمالي</strong></td>
            <td><strong>{{ $invoice_data['total_amount'] ?? '210' }} {{ $invoice_data['currency'] ?? 'دولار' }}</strong></td>
            <td><strong>{{ $invoice_data['total_amount'] ?? '210' }} {{ $invoice_data['currency'] ?? 'دولار' }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="section">
      <div class="section-title">طريقة الدفع</div>
      <div class="payment-method">
        <p><strong>طريقة الدفع:</strong> {{ $invoice_data['payment_method'] ?? 'ShamCash' }}</p>
        <p><strong>حالة الدفع:</strong> {{ $invoice_data['payment_status'] ?? 'مدفوع' }}</p>
        <p><strong>تاريخ الدفع:</strong> {{ $invoice_data['payment_date'] ?? date('d-m-Y') }}</p>
      </div>
    </div>

    <div class="qr-code">
      <p><strong>رمز QR للحجز</strong></p>
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $invoice_data['qr_code_url'] ?? 'https://sawastay.com/booking/BK-2025-001' }}" alt="QR Code for SawaStay">
    </div>

    <div class="section">
      <div class="section-title" style="font-size: 14px; margin-bottom: 10px;">ملاحظات مهمة</div>
      <ul style="font-size: 11px; line-height: 1.5; color: #666; padding-right: 15px; margin: 0;">
        @foreach($invoice_data['notes'] ?? [
          'يرجى الاحتفاظ بهذه الفاتورة لأغراض السكن أو الاسترداد',
          'تم تأكيد الحجز من قبل إدارة SawaStay',
          'يرجى الوصول في الوقت المحدد (عادةً الساعة 2:00 مساءً)',
          'يرجى المغادرة في الوقت المحدد (عادةً الساعة 11:00 صباحاً)',
          'للاستفسارات، يرجى التواصل معنا على: ' . ($invoice_data['contact_phone'] ?? '+963-xxx-xxxxxxx')
        ] as $note)
          <li>{{ $note }}</li>
        @endforeach
      </ul>
    </div>

    <div class="section links">
      @foreach($invoice_data['links'] ?? [
        ['url' => 'https://sawastay.com/terms', 'text' => '📜 الشروط والأحكام'],
        ['url' => 'https://sawastay.com/privacy', 'text' => '🔒 سياسة الخصوصية'],
        ['url' => 'https://sawastay.com/cancellation', 'text' => '↩️ سياسة الإلغاء'],
        ['url' => 'https://sawastay.com/contact', 'text' => '📞 اتصل بنا']
      ] as $link)
        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['text'] }}</a>
      @endforeach
    </div>

    <div class="footer">
      ✅ شكرًا لاستخدامك SawaStay! <br>
      نتمنى لك إقامة مريحة وآمنة. <br>
      <small>{{ $invoice_data['website_url'] ?? 'www.sawastay.com' }}</small>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const links = document.querySelectorAll('.links a');
      
      links.forEach(link => {
        link.setAttribute('title', 'انقر لفتح ' + link.textContent);
        
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
      console.log('جاري إعداد الفاتورة للطباعة...');
    });
    
    window.addEventListener('afterprint', function() {
      console.log('تم الانتهاء من الطباعة');
    });
  </script>
</body>
</html> 