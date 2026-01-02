<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capacity Settings - Restaurant Admin</title>
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
                    <a class="nav-link" href="{{ route('admin.bookings.create') }}">Manual Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.capacity') }}">Capacity Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.blackout') }}">Blackout Dates</a>
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
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Set Capacity for Time Slot</h5>
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

                        <form method="POST" action="{{ route('admin.capacity.update') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="{{ old('date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Time Slots *</label>
                                <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllTimeSlots()">
                                            Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllTimeSlots()">
                                            Deselect All
                                        </button>
                                    </div>
                                    <div style="column-count: 3; column-gap: 20px;">
                                        @foreach(['11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30'] as $time)
                                            <div class="form-check" style="break-inside: avoid;">
                                                <input class="form-check-input time-slot-checkbox" type="checkbox" 
                                                       name="time_slots[]" value="{{ $time }}" id="time_{{ str_replace(':', '', $time) }}">
                                                <label class="form-check-label" for="time_{{ str_replace(':', '', $time) }}">
                                                    {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <small class="text-muted">Select one or multiple time slots to update</small>
                            </div>

                            <div class="mb-3">
                                <label for="capacity" class="form-label">Maximum Capacity (Guests) *</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                       value="{{ old('capacity', 50) }}" min="1" max="200" required>
                                <small class="text-muted">This capacity will be applied to all selected time slots</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Capacity</button>
                        </form>

                        <script>
                            function selectAllTimeSlots() {
                                document.querySelectorAll('.time-slot-checkbox').forEach(cb => cb.checked = true);
                            }
                            function deselectAllTimeSlots() {
                                document.querySelectorAll('.time-slot-checkbox').forEach(cb => cb.checked = false);
                            }
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Current Capacity Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Date Filter -->
                        <form method="GET" action="{{ route('admin.capacity') }}" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="date" name="filter_date" class="form-control" 
                                           value="{{ request('filter_date') }}" 
                                           placeholder="Filter by date">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                            @if(request('filter_date'))
                                <div class="mt-2">
                                    <a href="{{ route('admin.capacity') }}" class="btn btn-sm btn-outline-secondary">Clear Filter</a>
                                </div>
                            @endif
                        </form>

                        @if($settings->isEmpty())
                            <p class="text-muted">No custom capacity settings yet. Default is 50 guests per time slot.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Max Capacity</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($settings as $setting)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($setting->date)->format('M j, Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($setting->time_slot)->format('g:i A') }}</td>
                                                <td>{{ $setting->capacity ?? 50 }} guests</td>
                                                <td>{{ $setting->created_at->format('M j, Y g:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
