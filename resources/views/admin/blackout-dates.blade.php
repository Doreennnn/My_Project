<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackout Dates - Restaurant Admin</title>
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
        .btn-danger {
            font-size: 0.875rem;
        }
        .blackout-item {
            border-left: 4px solid #dc3545;
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
                    <a class="nav-link" href="{{ route('admin.bookings.create') }}">Manual Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.capacity') }}">Capacity Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.blackout') }}">Blackout Dates</a>
                </li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🚫 Add Blackout Date</h5>
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

                        <form method="POST" action="{{ route('admin.blackout.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="{{ old('date') }}" min="{{ date('Y-m-d') }}" required>
                                <small class="text-muted">Select a date when the restaurant will be closed</small>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason (Optional)</label>
                                <input type="text" class="form-control" id="reason" name="reason" 
                                       value="{{ old('reason') }}" maxlength="255"
                                       placeholder="e.g., Public Holiday, Renovation, Private Event">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Blackout Date</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">ℹ️ About Blackout Dates</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>What are Blackout Dates?</strong></p>
                        <p class="small text-muted">
                            Blackout dates are days when the restaurant is closed and unavailable for bookings. 
                            Customers will not be able to select these dates when making reservations.
                        </p>
                        <p class="mb-2 mt-3"><strong>Examples:</strong></p>
                        <ul class="small text-muted">
                            <li>Public holidays</li>
                            <li>Renovation days</li>
                            <li>Private events</li>
                            <li>Staff training days</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Current Blackout Dates</h5>
                        <span class="badge bg-danger">{{ $blackoutDates->count() }} dates</span>
                    </div>
                    <div class="card-body">
                        @if($blackoutDates->isEmpty())
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">📅 No blackout dates set.</p>
                                <p class="text-muted small">Add dates when the restaurant will be closed.</p>
                            </div>
                        @else
                            <div class="list-group">
                                @foreach($blackoutDates as $blackout)
                                    <div class="list-group-item blackout-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    {{ \Carbon\Carbon::parse($blackout->date)->format('l, F j, Y') }}
                                                </h6>
                                                @if($blackout->reason)
                                                    <p class="mb-0 text-muted small">
                                                        <strong>Reason:</strong> {{ $blackout->reason }}
                                                    </p>
                                                @else
                                                    <p class="mb-0 text-muted small">No reason specified</p>
                                                @endif
                                                <p class="mb-0 text-muted small mt-1">
                                                    <em>Added {{ $blackout->created_at->diffForHumans() }}</em>
                                                </p>
                                            </div>
                                            <form method="POST" action="{{ route('admin.blackout.delete', $blackout) }}" 
                                                  onsubmit="return confirm('Are you sure you want to remove this blackout date?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    🗑️ Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
