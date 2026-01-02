<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gourmet House - Fine Dining Experience</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Georgia', serif; }
        .hero { 
            background-image: url('/images/background.jfif'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            color: white; 
            text-align: center;
            position: relative;
        }
        /* Dark overlay for better text readability */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(62, 39, 35, 0.5);
            z-index: 1;
        }
        .hero > * {
            position: relative;
            z-index: 2;
        }
        .hero h1 { font-size: 64px; margin-bottom: 20px; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); }
        .hero p { font-size: 24px; margin-bottom: 40px; font-style: italic; }
        .admin-link { position: absolute; top: 30px; right: 40px; }
        .hero-buttons { display: flex; gap: 20px; margin-top: 30px; justify-content: center; flex-wrap: wrap; }
        .btn { padding: 18px 40px; text-decoration: none; border-radius: 50px; font-size: 18px; font-weight: bold; transition: all 0.3s; display: inline-block; }
        .btn-primary { background: #d4af37; color: #000; }
        .btn-primary:hover { background: #f4cf57; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4); }
        .btn-secondary { background: transparent; color: white; border: 2px solid white; padding: 12px 30px; font-size: 16px; }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); color: white; transform: translateY(-2px); }
        .features { background: #f9f9f9; padding: 80px 20px; text-align: center; }
        .features h2 { font-size: 42px; margin-bottom: 50px; color: #333; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; max-width: 1200px; margin: auto; }
        .feature-item { background: white; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .feature-icon { font-size: 48px; margin-bottom: 20px; }
        .feature-item h3 { font-size: 24px; margin-bottom: 15px; color: #333; }
        .feature-item p { color: #666; line-height: 1.6; }
        .info-section { background: #fff; padding: 80px 20px; }
        .info-section h2 { font-size: 42px; margin-bottom: 50px; color: #3e2723; text-align: center; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .info-card { background: #f9f9f9; padding: 30px; border-radius: 12px; border-left: 4px solid #d4af37; }
        .info-card h3 { color: #3e2723; margin-bottom: 15px; font-size: 22px; }
        .info-card p { color: #555; line-height: 1.8; margin-bottom: 10px; }
        .info-card .highlight { color: #d4af37; font-weight: bold; }
        .scroll-down { margin-top: 40px; display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: all 0.3s; }
        .scroll-down:hover { transform: translateY(5px); }
        .scroll-down span { color: white; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .scroll-down .arrow { font-size: 32px; color: #d4af37; animation: bounce 2s infinite; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>

<div class="hero">
    <div class="admin-link">
        <a href="/admin/login" class="btn btn-secondary">🔐 Admin</a>
    </div>
    
    <h1>🍽️ The Gourmet House</h1>
    <p>Experience Culinary Excellence</p>
    <p style="font-size: 18px; max-width: 600px; line-height: 1.8;">
        Welcome to The Gourmet House, where exceptional cuisine meets warm hospitality. 
        Reserve your table today and embark on an unforgettable dining journey.
    </p>
    <div class="hero-buttons">
        <a href="/menu" class="btn btn-primary">🍴 View Menu</a>
        <a href="/book" class="btn btn-primary">📅 Reserve a Table</a>
    </div>
    <div class="scroll-down" onclick="document.getElementById('info-section').scrollIntoView({behavior: 'smooth'});">
        <span>View More Details</span>
        <div class="arrow">↓</div>
    </div>
</div>

<div class="info-section" id="info-section">
    <h2>Restaurant Information</h2>
    <div class="info-grid">
        <div class="info-card">
            <h3>⏰ Operating Hours</h3>
            <p><strong>Lunch Service</strong></p>
            <p>Monday - Sunday<br>11:00 AM - 2:30 PM</p>
            <p style="margin-top: 15px;"><strong>Dinner Service</strong></p>
            <p>Monday - Sunday<br>5:00 PM - 9:30 PM</p>
        </div>
        
        <div class="info-card">
            <h3>📍 Location & Contact</h3>
            <p><strong>Address:</strong><br>33, Jalan Indah 16/6, Taman Bukit Indah,<br>81200 Johor Bahru, Johor Darul Ta'zim</p>
            <p style="margin-top: 15px;"><strong>Phone:</strong><br><span class="highlight">07-5566888</span></p>
            <p><strong>Email:</strong><br>info@gourmethouse.com</p>
        </div>
        
        <div class="info-card">
            <h3>🎫 Reservation Policy</h3>
            <p>• Online booking available for up to <span class="highlight">8 guests</span></p>
            <p>• For parties larger than 8, please call us</p>
            <p>• Advance booking recommended</p>
            <p>• Free cancellation up to booking date</p>
        </div>
    </div>
</div>

</body>
</html>