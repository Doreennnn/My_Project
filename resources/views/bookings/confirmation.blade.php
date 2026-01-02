<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Restaurant Booking</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .detail-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="success-icon">✓</div>
            <h2 class="mb-3">Booking Confirmed!</h2>
            <p class="text-muted">We've sent a confirmation email to <strong>{{ $booking->email }}</strong></p>

            <div class="detail-box">
                <h5 class="mb-3">Reservation Details</h5>
                
                <div class="detail-item">
                    <span><strong>Name:</strong></span>
                    <span>{{ $booking->customer_name }}</span>
                </div>
                
                <div class="detail-item">
                    <span><strong>Date:</strong></span>
                    <span>{{ $booking->booking_date->format('F j, Y') }}</span>
                </div>
                
                <div class="detail-item">
                    <span><strong>Time:</strong></span>
                    <span>{{ $booking->booking_time }}</span>
                </div>
                
                <div class="detail-item">
                    <span><strong>Party Size:</strong></span>
                    <span>{{ $booking->party_size }} {{ $booking->party_size == 1 ? 'Guest' : 'Guests' }}</span>
                </div>
                
                @if($booking->table_preference)
                <div class="detail-item">
                    <span><strong>Table Preference:</strong></span>
                    <span>{{ ucfirst($booking->table_preference) }}</span>
                </div>
                @endif
                
                @if($booking->special_requests)
                <div class="detail-item">
                    <span><strong>Special Requests:</strong></span>
                    <span>{{ $booking->special_requests }}</span>
                </div>
                @endif
                
                <div class="detail-item">
                    <span><strong>Status:</strong></span>
                    <span class="badge bg-{{ $booking->status_color }}">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Need to make changes?</strong><br>
                Save this link to manage your booking later.
            </div>

            <a href="{{ route('booking.manage', ['token' => $booking->booking_token]) }}" 
               class="btn btn-primary btn-lg mb-3 w-100">
                Manage Your Booking
            </a>

            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>
