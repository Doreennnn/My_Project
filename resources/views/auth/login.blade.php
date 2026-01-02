<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Restaurant Booking</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #6d4c41 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Georgia', serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        h2 {
            color: #3e2723;
            letter-spacing: 1px;
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
            color: #000;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">Admin Login</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
            <a href="{{ route('home') }}" class="text-muted">← Back to Home</a>
        </div>

        <!--
        <div class="mt-4 p-3 bg-light rounded">
            <small class="text-muted">
                <strong>Default Admin Credentials:</strong><br>
                Email: admin@restaurant.com<br>
                Password: password
            </small>
        </div>
        -->
    </div>
</body>
</html>
