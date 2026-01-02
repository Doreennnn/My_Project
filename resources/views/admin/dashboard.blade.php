<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restaurant Booking</title>
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
        h5, h3, h6 {
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
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .booking-row:hover {
            background: #f8f9fa;
        }
        table tbody tr.booking-row-odd,
        table tbody tr.booking-row-odd td {
            background-color: #e9ecef !important;
        }
        table tbody tr.booking-row-even,
        table tbody tr.booking-row-even td {
            background-color: #ffffff !important;
        }
        table tbody tr.special-requests-row {
            border-top: none !important;
        }
        table tbody tr.special-requests-row td {
            padding-top: 0 !important;
            padding-bottom: 8px !important;
            border-top: none !important;
        }
        table tbody tr.booking-row td {
            padding-bottom: 4px !important;
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

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Navigation Tabs -->
        <div class="mb-4">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">Daily View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.bookings') }}">All Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.bookings.create') }}">Manual Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.capacity') }}">Capacity Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.blackout') }}">Blackout Dates</a>
                </li>
            </ul>
        </div>

        <!-- Date Selector -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Select Date:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control" name="date" value="{{ $date }}" 
                               onchange="this.form.submit()">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">View</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">🔄 Refresh</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card stat-card border-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Total</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card border-warning">
                    <div class="card-body">
                        <h6 class="text-muted">Pending</h6>
                        <h3>{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card border-success">
                    <div class="card-body">
                        <h6 class="text-muted">Confirmed</h6>
                        <h3>{{ $stats['confirmed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card border-info">
                    <div class="card-body">
                        <h6 class="text-muted">Seated</h6>
                        <h3>{{ $stats['seated'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card border-secondary">
                    <div class="card-body">
                        <h6 class="text-muted">Completed</h6>
                        <h3>{{ $stats['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card border-danger">
                    <div class="card-body">
                        <h6 class="text-muted">No-show</h6>
                        <h3>{{ $stats['no_show'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Reservations for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h5>
            </div>
            <div class="card-body">
                @if($bookings->isEmpty())
                    <p class="text-muted text-center py-4">No bookings for this date.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Guests</th>
                                    <th>Table</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $index => $booking)
                                    <tr class="booking-row {{ $loop->iteration % 2 == 1 ? 'booking-row-odd' : 'booking-row-even' }}">
                                        <td><strong>{{ $booking->booking_time }}</strong></td>
                                        <td>{{ $booking->customer_name }}</td>
                                        <td>
                                            <small>{{ $booking->email }}<br>{{ $booking->phone }}</small>
                                        </td>
                                        <td>{{ $booking->party_size }}</td>
                                        <td>{{ $booking->table_preference ? ucfirst($booking->table_preference) : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status_color }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($booking->status == 'pending')
                                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                                                    </form>
                                                @endif
                                                
                                                @if($booking->status == 'confirmed')
                                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="seated">
                                                        <button type="submit" class="btn btn-info btn-sm">Seated</button>
                                                    </form>
                                                @endif
                                                
                                                @if($booking->status == 'seated')
                                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn btn-secondary btn-sm">Completed</button>
                                                    </form>
                                                @endif
                                                
                                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="no-show">
                                                        <button type="submit" class="btn btn-dark btn-sm">No-Show</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @if($booking->special_requests)
                                        <tr class="special-requests-row {{ $loop->iteration % 2 == 1 ? 'booking-row-odd' : 'booking-row-even' }}">
                                            <td colspan="7" class="text-muted small ps-5">
                                                <em>Special Requests: {{ $booking->special_requests }}</em>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
