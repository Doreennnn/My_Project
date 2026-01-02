<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Booking - Restaurant Admin</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Georgia', serif;
        }
        .navbar {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #6d4c41 100%);
        }
        h5 {
            color: #3e2723;
            letter-spacing: 0.5px;
        }
        .nav-pills .nav-link.active {
            background-color: #d4af37;
            color: #000;
        }
        .nav-pills .nav-link {
            color: #5d4037;
        }
        .nav-pills .nav-link:hover {
            background-color: #fff8e1;
        }
        .btn-primary {
            background: #d4af37;
            border-color: #d4af37;
            color: #000;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: #c9a02e;
            border-color: #c9a02e;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Restaurant Admin Dashboard</span>
            <div class="d-flex">
                <span class="text-white me-3">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Daily View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.bookings') }}">All Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.bookings.create') }}">Manual Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.capacity') }}">Capacity Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.blackout') }}">Blackout Dates</a>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create Manual Booking (Walk-in / Phone)</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.bookings.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name *</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                   value="{{ old('customer_name') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="booking_date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                   value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="booking_time" class="form-label">Time *</label>
                            <select class="form-control" id="booking_time" name="booking_time" required>
                                <option value="">Select time...</option>
                                @foreach(['11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30'] as $time)
                                    <option value="{{ $time }}" {{ old('booking_time') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="party_size" class="form-label">Number of Guests *</label>
                            <select class="form-control" id="party_size" name="party_size" required>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('party_size') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">Staff can book up to 20 guests (subject to time slot capacity)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="table_preference" class="form-label">Table Preference</label>
                        <select class="form-control" id="table_preference" name="table_preference">
                            <option value="">No preference</option>
                            <option value="indoor" {{ old('table_preference') == 'indoor' ? 'selected' : '' }}>Indoor</option>
                            <option value="outdoor" {{ old('table_preference') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                            <option value="window" {{ old('table_preference') == 'window' ? 'selected' : '' }}>Window Seat</option>
                            <option value="high-top" {{ old('table_preference') == 'high-top' ? 'selected' : '' }}>High-top Table</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="special_requests" class="form-label">Special Requests</label>
                        <textarea class="form-control" id="special_requests" name="special_requests" 
                                  rows="3">{{ old('special_requests') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Booking</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
