<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Restaurant Admin</title>
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

    <div class="container-fluid">
        <div class="mb-4">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Daily View</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.bookings') }}">All Bookings</a>
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bookings') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="seated" {{ request('status') == 'seated' ? 'selected' : '' }}>Seated</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no-show" {{ request('status') == 'no-show' ? 'selected' : '' }}>No-show</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Date</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">Apply Filters</button>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-secondary d-block w-100" onclick="location.reload()" title="Refresh">🔄</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Reservations ({{ $bookings->total() }} total)</h5>
            </div>
            <div class="card-body">
                @if($bookings->isEmpty())
                    <p class="text-muted text-center py-4">No bookings found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ route('admin.bookings', array_merge(request()->all(), ['sort' => 'booking_date', 'direction' => request('sort') == 'booking_date' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Date
                                            @if(request('sort') == 'booking_date')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.bookings', array_merge(request()->all(), ['sort' => 'booking_time', 'direction' => request('sort') == 'booking_time' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Time
                                            @if(request('sort') == 'booking_time')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.bookings', array_merge(request()->all(), ['sort' => 'customer_name', 'direction' => request('sort') == 'customer_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Customer
                                            @if(request('sort') == 'customer_name')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Contact</th>
                                    <th>Guests</th>
                                    <th>Table</th>
                                    <th>
                                        <a href="{{ route('admin.bookings', array_merge(request()->all(), ['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Status
                                            @if(request('sort') == 'status')
                                                <span>{{ request('direction') == 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_date->format('M j, Y') }}</td>
                                        <td>{{ $booking->booking_time }}</td>
                                        <td>{{ $booking->customer_name }}</td>
                                        <td>
                                            <small>
                                                {{ $booking->email }}<br>
                                                {{ $booking->phone }}
                                            </small>
                                        </td>
                                        <td>{{ $booking->party_size }}</td>
                                        <td>{{ $booking->table_preference ? ucfirst($booking->table_preference) : '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="seated" {{ $booking->status == 'seated' ? 'selected' : '' }}>Seated</option>
                                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    <option value="no-show" {{ $booking->status == 'no-show' ? 'selected' : '' }}>No-show</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <small>{{ $booking->created_at->format('M j, g:i A') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('booking.manage', $booking->booking_token) }}" class="btn btn-outline-primary btn-sm" target="_blank">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
