<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - Restaurant Booking</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #6d4c41 100%);
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Georgia', serif;
        }
        .booking-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
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
        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        #timeSlotContainer {
            display: none;
        }
        .timeout-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ffc107;
            color: #000;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .timeout-banner.warning {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div id="timeoutBanner" class="timeout-banner" style="display: none;">
        ⏱️ Time Remaining: <span id="timeRemaining">10:00</span>
    </div>

    <div class="container">
        <div class="booking-card">
            <h2 class="text-center mb-4">Reserve Your Table</h2>
            
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm mb-3">← Back to Home</a>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="bookingForm" method="POST" action="{{ route('booking.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="customer_name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                           value="{{ old('customer_name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="{{ old('phone') }}" placeholder="60123456789" required>
                    <small class="text-muted">10-15 digits only</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booking_date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="booking_date" name="booking_date" 
                               value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="party_size" class="form-label">Number of Guests *</label>
                        <select class="form-control" id="party_size" name="party_size" required>
                            <option value="">Select...</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('party_size') == $i ? 'selected' : '' }}>
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
                    <input type="hidden" id="booking_time" name="booking_time" value="{{ old('booking_time') }}">
                    <small class="text-muted" id="loadingSlots">Loading available times...</small>
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
                              rows="3" maxlength="500">{{ old('special_requests') }}</textarea>
                    <small class="text-muted">Maximum 500 characters</small>
                </div>

                <button type="submit" class="btn w-100 py-2" id="submitBtn" disabled style="background: #d4af37; color: #000; border: none; font-weight: bold; font-size: 16px;">
                    Complete Reservation
                </button>
            </form>
        </div>
    </div>

    <script>
        const blackoutDates = @json($blackoutDates ?? []);
        const bookingDateInput = document.getElementById('booking_date');
        const partySizeInput = document.getElementById('party_size');
        const timeSlotContainer = document.getElementById('timeSlotContainer');
        const timeSlotGrid = document.getElementById('timeSlotGrid');
        const bookingTimeInput = document.getElementById('booking_time');
        const loadingSlots = document.getElementById('loadingSlots');
        const submitBtn = document.getElementById('submitBtn');

        // Disable blackout dates
        bookingDateInput.addEventListener('input', function(e) {
            const selectedDate = e.target.value;
            if (blackoutDates.includes(selectedDate)) {
                alert('Sorry, the restaurant is closed on this date. Please select another date.');
                e.target.value = '';
                timeSlotContainer.style.display = 'none';
                submitBtn.disabled = true;
            }
        });

        function fetchAvailableSlots() {
            const date = bookingDateInput.value;
            const partySize = partySizeInput.value;

            if (!date || !partySize) {
                timeSlotContainer.style.display = 'none';
                submitBtn.disabled = true;
                return;
            }

            // Check if date is blackout
            if (blackoutDates.includes(date)) {
                alert('Sorry, the restaurant is closed on this date. Please select another date.');
                bookingDateInput.value = '';
                timeSlotContainer.style.display = 'none';
                submitBtn.disabled = true;
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
            submitBtn.disabled = false;
        }

        bookingDateInput.addEventListener('change', fetchAvailableSlots);
        partySizeInput.addEventListener('change', fetchAvailableSlots);

        // If there are old values, trigger the fetch
        if (bookingDateInput.value && partySizeInput.value) {
            fetchAvailableSlots();
        }

        // Session timeout functionality
        let timeoutSeconds = 600; // 10 minutes
        let warningSeconds = 60; // Show warning at 1 minute
        let timeoutTimer;
        let countdownInterval;
        const timeoutBanner = document.getElementById('timeoutBanner');
        const timeRemainingSpan = document.getElementById('timeRemaining');

        function startTimeout() {
            timeoutBanner.style.display = 'block';
            
            countdownInterval = setInterval(() => {
                timeoutSeconds--;
                
                const minutes = Math.floor(timeoutSeconds / 60);
                const seconds = timeoutSeconds % 60;
                timeRemainingSpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Show warning when less than 1 minute remains
                if (timeoutSeconds <= warningSeconds) {
                    timeoutBanner.classList.add('warning');
                }
                
                // Timeout expired
                if (timeoutSeconds <= 0) {
                    clearInterval(countdownInterval);
                    alert('Your session has timed out due to inactivity. Please start a new booking.');
                    window.location.href = '{{ route('home') }}';
                }
            }, 1000);
        }

        // Reset timeout on user activity
        function resetTimeout() {
            if (timeoutSeconds > 0 && timeoutSeconds < 540) { // Only reset if already started and not in first minute
                timeoutSeconds = 600;
                timeoutBanner.classList.remove('warning');
            }
        }

        // Start timeout when page loads
        startTimeout();

        // Reset timeout on user interactions
        document.addEventListener('click', resetTimeout);
        document.addEventListener('keypress', resetTimeout);
        document.addEventListener('change', resetTimeout);
    </script>
</body>
</html>
