<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu - The Gourmet House</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Georgia', serif; 
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #6d4c41 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 48px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        .header p {
            font-size: 20px;
            font-style: italic;
            margin-bottom: 30px;
        }
        .back-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #d4af37;
            color: #000;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .back-btn:hover {
            background: #f4cf57;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }
        .carousel-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .carousel {
            position: relative;
            width: 100%;
            height: 600px;
            perspective: 1000px;
        }
        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform-origin: center center;
            cursor: pointer;
        }
        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            background: white;
            padding: 10px;
        }
        /* Stacking effect - cards stack vertically underneath */
        .carousel-slide:nth-child(1) {
            z-index: 4;
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        .carousel-slide:nth-child(2) {
            z-index: 3;
            transform: translateY(30px) scale(1);
            opacity: 0.25;
        }
        .carousel-slide:nth-child(3) {
            z-index: 2;
            transform: translateY(30px) scale(1);
            opacity: 0.25;
        }
        .carousel-slide:nth-child(4) {
            z-index: 1;
            transform: translateY(30px) scale(1);
            opacity: 0.25;
        }
        .carousel-slide.active {
            z-index: 10 !important;
            transform: translateY(0) scale(1) !important;
            opacity: 1 !important;
        }
        .carousel-slide.next-1 {
            z-index: 9 !important;
            transform: translateY(30px) scale(1) !important;
            opacity: 0.25 !important;
        }
        .carousel-slide.next-2 {
            z-index: 8 !important;
            transform: translateY(30px) scale(1) !important;
            opacity: 0.25 !important;
        }
        .carousel-slide.next-3 {
            z-index: 7 !important;
            transform: translateY(30px) scale(1) !important;
            opacity: 0.25 !important;
        }
        .carousel-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-top: 40px;
        }
        .carousel-btn {
            background: #3e2723;
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-btn:hover {
            background: #d4af37;
            color: #000;
            transform: scale(1.1);
        }
        .carousel-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: scale(1);
        }
        .carousel-indicators {
            display: flex;
            gap: 10px;
        }
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ccc;
            cursor: pointer;
            transition: all 0.3s;
        }
        .indicator.active {
            background: #d4af37;
            transform: scale(1.3);
        }
        .slide-counter {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .placeholder-notice {
            background: #fff3cd;
            border: 2px solid #d4af37;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            display: none;
        }
        .placeholder-notice h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        .placeholder-notice p {
            color: #856404;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🍽️ Our Menu</h1>
        <p>Discover our culinary delights</p>
        <a href="/" class="back-btn">← Back to Home</a>
    </div>

    <div class="placeholder-notice">
        <h3>📸 Upload Your Menu Photos</h3>
        <p>Place your menu images (menu1.jfif, menu2.jfif, menu3.jfif, menu4.jfif) in the <strong>public/images/menu</strong> folder.</p>
    </div>

    <div class="carousel-container">
        <div class="carousel" id="carousel">
            <div class="carousel-slide active">
                <img src="/images/menu/menu1.jfif" alt="Menu Page 1" onerror="this.src='https://via.placeholder.com/800x600/3e2723/d4af37?text=Menu+1+Not+Found'">
            </div>
            <div class="carousel-slide next-1">
                <img src="/images/menu/menu2.jfif" alt="Menu Page 2" onerror="this.src='https://via.placeholder.com/800x600/5d4037/d4af37?text=Menu+2+Not+Found'">
            </div>
            <div class="carousel-slide next-2">
                <img src="/images/menu/menu3.jfif" alt="Menu Page 3" onerror="this.src='https://via.placeholder.com/800x600/6d4c41/d4af37?text=Menu+3+Not+Found'">
            </div>
            <div class="carousel-slide next-3">
                <img src="/images/menu/menu4.jfif" alt="Menu Page 4" onerror="this.src='https://via.placeholder.com/800x600/8d6e63/d4af37?text=Menu+4+Not+Found'">
            </div>
        </div>

        <div class="carousel-controls">
            <button class="carousel-btn" id="prevBtn" onclick="previousSlide()">‹</button>
            <div class="carousel-indicators" id="indicators"></div>
            <button class="carousel-btn" id="nextBtn" onclick="nextSlide()">›</button>
        </div>

        <div class="slide-counter" id="slideCounter"></div>
    </div>
</div>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const indicatorsContainer = document.getElementById('indicators');
    const slideCounter = document.getElementById('slideCounter');

    // Create indicators
    for (let i = 0; i < totalSlides; i++) {
        const indicator = document.createElement('div');
        indicator.className = 'indicator';
        indicator.onclick = () => goToSlide(i);
        indicatorsContainer.appendChild(indicator);
    }

    function updateCarousel() {
        // Remove all classes from slides
        slides.forEach((slide, index) => {
            slide.classList.remove('active', 'next-1', 'next-2', 'next-3');
            
            // Calculate position relative to current slide
            let position = index - currentSlide;
            
            if (position < 0) {
                // Slides before current (wrap to end)
                position = totalSlides + position;
            }
            
            // Apply classes based on position
            if (position === 0) {
                slide.classList.add('active');
            } else if (position === 1) {
                slide.classList.add('next-1');
            } else if (position === 2) {
                slide.classList.add('next-2');
            } else if (position === 3) {
                slide.classList.add('next-3');
            }
        });
        
        // Update indicators
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });

        // No disabled buttons - circular navigation
        prevBtn.disabled = false;
        nextBtn.disabled = false;

        // Update counter
        slideCounter.textContent = `${currentSlide + 1} / ${totalSlides}`;
    }

    function nextSlide() {
        // Circular navigation - go to first slide after last
        currentSlide = (currentSlide + 1) % totalSlides;
        updateCarousel();
    }

    function previousSlide() {
        // Circular navigation - go to last slide before first
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateCarousel();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateCarousel();
    }

    // Click on slides to navigate
    slides.forEach((slide, index) => {
        slide.addEventListener('click', () => {
            if (index !== currentSlide) {
                goToSlide(index);
            }
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') previousSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });

    // Initialize
    updateCarousel();
</script>

</body>
</html>
