<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Booking - Restaurant Booking</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #6d4c41 100%);
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Georgia', serif;
        }
        .manage-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 700px;
            margin: 0 auto;
        }
        h2, h5 {
            color: #3e2723;
            letter-spacing: 1px;
        }
        .time-slot {
            border: 2px solid #dee2e6;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .time-slot:hover {
            border-color: #d4af37;
            background: #fff8e1;
        }
        .time-slot.selected {
            border-color: #d4af37;
            background: #d4af37;
            color: white;
        }
        #timeSlotContainer {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="manage-card">
            <h2 class="mb-4">Manage Your Booking</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(isset($message))
                <div class="alert alert-warning">{{ $message }}</div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Current Booking Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $booking->customer_name }}</p>
                            <p><strong>Email:</strong> {{ $booking->email }}</p>
                            <p><strong>Phone:</strong> {{ $booking->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $booking->booking_date->format('F j, Y') }}</p>
                            <p><strong>Time:</strong> {{ $booking->booking_time }}</p>
                            <p><strong>Guests:</strong> {{ $booking->party_size }}</p>
                            <p><strong>Table:</strong> {{ $booking->table_preference ? ucfirst($booking->table_preference) : 'No preference' }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $booking->status_color }}">{{ ucfirst($booking->status) }}</span></p>
                        </div>
                    </div>
                    @if($booking->special_requests)
                        <p><strong>Special Requests:</strong> {{ $booking->special_requests }}</p>
                    @endif
                </div>
            </div>

            @if($canModify)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Modify Booking</h5>
                        <form method="POST" action="{{ route('booking.update', ['token' => $booking->booking_token]) }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="booking_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                           value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d', strtotime('tomorrow')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="party_size" class="form-label">Number of Guests *</label>
                                    <select class="form-control" id="party_size" name="party_size" required>
                                        @for($i = 1; $i <= 8; $i++)
                                            <option value="{{ $i }}" {{ old('party_size', $booking->party_size) == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                            </option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">For parties larger than 8 guests, please call <strong>07-5566888</strong></small>
                                </div>
                            </div>

                            <div id="timeSlotContainer" class="mb-3">
                                <label class="form-label">Available Time Slots *</label>
                                <div id="timeSlotGrid" class="d-flex flex-wrap"></div>
                                <input type="hidden" id="booking_time" name="booking_time" value="{{ old('booking_time', $booking->booking_time) }}">
                                <small class="text-muted" id="loadingSlots">Loading available times...</small>
                            </div>

                            <div class="mb-3">
                                <label for="table_preference" class="form-label">Table Preference</label>
                                <select class="form-control" id="table_preference" name="table_preference">
                                    <option value="">No preference</option>
                                    <option value="indoor" {{ old('table_preference', $booking->table_preference) == 'indoor' ? 'selected' : '' }}>Indoor</option>
                                    <option value="outdoor" {{ old('table_preference', $booking->table_preference) == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                                    <option value="window" {{ old('table_preference', $booking->table_preference) == 'window' ? 'selected' : '' }}>Window Seat</option>
                                    <option value="high-top" {{ old('table_preference', $booking->table_preference) == 'high-top' ? 'selected' : '' }}>High-top Table</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" 
                                          rows="3" maxlength="500">{{ old('special_requests', $booking->special_requests) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Booking</button>
                        </form>
                    </div>
                </div>

                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title text-danger">Cancel Booking</h5>
                        <p class="text-muted">Once cancelled, this booking cannot be restored.</p>
                        <form method="POST" action="{{ route('booking.cancel', ['token' => $booking->booking_token]) }}" 
                              onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cancel Booking</button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Return to Home</a>
            </div>
        </div>
    </div>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const partySizeInput = document.getElementById('party_size');
        const timeSlotContainer = document.getElementById('timeSlotContainer');
        const timeSlotGrid = document.getElementById('timeSlotGrid');
        const bookingTimeInput = document.getElementById('booking_time');
        const loadingSlots = document.getElementById('loadingSlots');

        function fetchAvailableSlots() {
            const date = bookingDateInput.value;
            const partySize = partySizeInput.value;

            if (!date || !partySize) {
                return;
            }

            timeSlotContainer.style.display = 'block';
            loadingSlots.style.display = 'block';
            timeSlotGrid.innerHTML = '';

            fetch(`{{ route('booking.available-slots') }}?date=${date}&party_size=${partySize}`)
                .then(response => response.json())
                .then(slots => {
                    loadingSlots.style.display = 'none';
                    
                    if (slots.length === 0) {
                        timeSlotGrid.innerHTML = '<p class="text-danger">No available time slots for this date and party size.</p>';
                        return;
                    }

                    slots.forEach(slot => {
                        const slotDiv = document.createElement('div');
                        slotDiv.className = 'time-slot';
                        if (slot.time === '{{ $booking->booking_time }}') {
                            slotDiv.classList.add('selected');
                        }
                        slotDiv.textContent = slot.time;
                        slotDiv.onclick = () => selectTimeSlot(slotDiv, slot.time);
                        timeSlotGrid.appendChild(slotDiv);
                    });
                })
                .catch(error => {
                    loadingSlots.style.display = 'none';
                    timeSlotGrid.innerHTML = '<p class="text-danger">Error loading time slots. Please try again.</p>';
                });
        }

        function selectTimeSlot(element, time) {
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            element.classList.add('selected');
            bookingTimeInput.value = time;
        }

        if (bookingDateInput) {
            bookingDateInput.addEventListener('change', fetchAvailableSlots);
            partySizeInput.addEventListener('change', fetchAvailableSlots);
            
            // Load initial slots
            fetchAvailableSlots();
        }
    </script>
</body>
</html>
