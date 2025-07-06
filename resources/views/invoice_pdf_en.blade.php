<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Booking Invoice - SawaStay</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 20px;
      color: #333;
      background: white;
      font-weight: 400;
      line-height: 1.4;
      font-size: 12px;
    }

    .invoice {
      max-width: 100%;
      margin: 0;
      background: white;
      padding: 20px;
      border: 1px solid #e0e0e0;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      border-bottom: 2px solid #F2506D;
      padding-bottom: 15px;
    }

    .logo {
      width: 80px;
      height: auto;
    }

    .invoice-info {
      text-align: right;
    }

    .invoice-number {
      font-size: 16px;
      font-weight: 700;
      color: #F2506D;
      margin-bottom: 5px;
    }

    .invoice-date {
      color: #666;
      font-size: 12px;
      font-weight: 400;
    }

    h1 {
      text-align: center;
      color: #F2506D;
      margin: 0;
      font-weight: 700;
      font-size: 20px;
      margin-bottom: 15px;
    }

    .section {
      margin-bottom: 20px;
    }

    .section-title {
      font-size: 14px;
      font-weight: 600;
      color: #F2506D;
      margin-bottom: 10px;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 5px;
    }

    .guest-info {
      display: table;
      width: 100%;
      margin-bottom: 15px;
    }

    .info-item {
      display: table-row;
    }

    .info-label {
      display: table-cell;
      font-weight: 600;
      color: #555;
      font-size: 11px;
      padding: 3px 10px 3px 0;
      width: 30%;
    }

    .info-value {
      display: table-cell;
      color: #333;
      font-weight: 500;
      font-size: 11px;
      padding: 3px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 11px;
    }

    table th, table td {
      border: 1px solid #e0e0e0;
      padding: 8px;
      text-align: left;
    }

    table th {
      background-color: #fef2f4;
      font-weight: 600;
      font-size: 11px;
    }

    .total-row {
      background-color: #f5f5f5;
      font-weight: 700;
      font-size: 12px;
    }

    .payment-method {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      border-left: 3px solid #F2506D;
      margin: 15px 0;
    }

    .payment-method p {
      margin: 5px 0;
      font-size: 11px;
    }

    .notes {
      margin: 20px 0;
      padding: 15px;
      background: #fef2f4;
      border-radius: 5px;
    }

    .notes h3 {
      color: #F2506D;
      font-size: 13px;
      margin-bottom: 10px;
    }

    .notes ul {
      margin: 0;
      padding-left: 20px;
    }

    .notes li {
      margin-bottom: 5px;
      font-size: 11px;
    }

    .footer {
      margin-top: 30px;
      text-align: center;
      font-size: 10px;
      color: #666;
      border-top: 1px solid #e0e0e0;
      padding-top: 15px;
    }

    .qr-code {
      text-align: center;
      margin: 20px 0;
    }

    .qr-code img {
      width: 80px;
      height: 80px;
    }

    .contact-info {
      text-align: center;
      margin: 15px 0;
      font-size: 11px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="header">
      <div class="logo">
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjQwIiB2aWV3Qm94PSIwIDAgMTIwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8dGV4dCB4PSI2MCIgeT0iMjUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IiNGMjUwNkQiIHRleHQtYW5jaG9yPSJtaWRkbGUiPlNhd2FTdGF5PC90ZXh0Pgo8L3N2Zz4K" alt="SawaStay Logo">
      </div>
      <div class="invoice-info">
        <div class="invoice-number">Invoice #{{ $invoice_data['booking_id'] }}</div>
        <div class="invoice-date">{{ $invoice_data['invoice_date'] }}</div>
      </div>
    </div>

    <h1>Booking Invoice</h1>

    <div class="section">
      <div class="section-title">Guest Information</div>
      <div class="guest-info">
        <div class="info-item">
          <div class="info-label">Name:</div>
          <div class="info-value">{{ $invoice_data['guest_name'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Phone:</div>
          <div class="info-value">{{ $invoice_data['guest_phone'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Email:</div>
          <div class="info-value">{{ $invoice_data['guest_email'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Status:</div>
          <div class="info-value">{{ $invoice_data['booking_status'] }}</div>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Booking Details</div>
      <div class="guest-info">
        <div class="info-item">
          <div class="info-label">Property:</div>
          <div class="info-value">{{ $invoice_data['listing_name'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Check-in Time:</div>
          <div class="info-value">{{ $invoice_data['check_in_date'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Check-out Time:</div>
          <div class="info-value">{{ $invoice_data['check_out_date'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Nights:</div>
          <div class="info-value">{{ $invoice_data['nights_count'] }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Guests:</div>
          <div class="info-value">{{ $invoice_data['guests_count'] }}</div>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Pricing Breakdown</div>
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th>Nights</th>
            <th>Price/Night</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice_data['pricing_breakdown'] as $item)
          <tr>
            <td>{{ $item['description'] }}</td>
            <td>{{ $item['nights'] }}</td>
            <td>{{ $invoice_data['currency'] }} {{ number_format($item['price_per_night'], 2) }}</td>
            <td>{{ $invoice_data['currency'] }} {{ number_format($item['total'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="section">
      <div class="section-title">Payment Summary</div>
      <table>
        <tr>
          <td><strong>Subtotal:</strong></td>
          <td>{{ $invoice_data['currency'] }} {{ number_format($invoice_data['subtotal'], 2) }}</td>
        </tr>
        @if($invoice_data['service_fee'] > 0)
        <tr>
          <td><strong>Service Fee ({{ $invoice_data['service_fee_percentage'] }}%):</strong></td>
          <td>{{ $invoice_data['currency'] }} {{ number_format($invoice_data['service_fee'], 2) }}</td>
        </tr>
        @endif
        @if($invoice_data['tax_amount'] > 0)
        <tr>
          <td><strong>Tax:</strong></td>
          <td>{{ $invoice_data['currency'] }} {{ number_format($invoice_data['tax_amount'], 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
          <td><strong>Total Amount:</strong></td>
          <td><strong>{{ $invoice_data['currency'] }} {{ number_format($invoice_data['total_amount'], 2) }}</strong></td>
        </tr>
      </table>
    </div>

    <div class="payment-method">
      <p><strong>Payment Method:</strong> {{ $invoice_data['payment_method'] }}</p>
      <p><strong>Payment Status:</strong> {{ $invoice_data['payment_status'] }}</p>
      <p><strong>Payment Date:</strong> {{ $invoice_data['payment_date'] }}</p>
    </div>

    <div class="notes">
      <h3>Important Notes:</h3>
      <ul>
        @foreach($invoice_data['notes'] as $note)
        <li>{{ $note }}</li>
        @endforeach
      </ul>
    </div>

    <div class="qr-code">
      <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($invoice_data['qr_code_url']))) }}" alt="QR Code">
    </div>

    <div class="contact-info">
      <p><strong>Contact Us:</strong> {{ $invoice_data['contact_phone'] }}</p>
    </div>

    <div class="footer">
      <p>Thank you for choosing SawaStay!</p>
      <p>This invoice was generated automatically by the SawaStay system.</p>
    </div>
  </div>
</body>
</html> 