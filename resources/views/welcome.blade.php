<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Furniture Co. - Timeless Design, Modern Living</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@300;400;600&family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --sand: #E8DCC4;
            --walnut: #6B5B4C;
            --cream: #FAF7F0;
            --charcoal: #2C2C2C;
            --copper: #B87333;
            --sage: #9CAF88;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--sand) 0%, var(--cream) 100%);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(184, 115, 51, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(156, 175, 136, 0.1) 0%, transparent 50%);
            animation: backgroundFloat 20s ease-in-out infinite;
        }

        @keyframes backgroundFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -20px) scale(1.05); }
        }

        .hero-content {
            text-align: center;
            z-index: 10;
            animation: fadeInUp 1.2s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-family: 'Crimson Pro', serif;
            font-size: 5.5rem;
            font-weight: 300;
            color: var(--walnut);
            letter-spacing: -2px;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero-tagline {
            font-size: 1.3rem;
            color: var(--walnut);
            font-weight: 300;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeIn 1.5s ease-out 0.5s forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .cta-button {
            display: inline-block;
            padding: 18px 48px;
            background: var(--walnut);
            color: var(--cream);
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            animation: fadeIn 1.5s ease-out 1s forwards;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--copper);
            transition: left 0.5s ease;
            z-index: -1;
        }

        .cta-button:hover::before {
            left: 0;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
            opacity: 0.6;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        .scroll-indicator::after {
            content: '↓';
            font-size: 2rem;
            color: var(--walnut);
        }

        /* Featured Collections */
        .collections {
            padding: 120px 5%;
            background: var(--cream);
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-header h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 3.5rem;
            font-weight: 300;
            color: var(--walnut);
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--walnut);
            opacity: 0.7;
            letter-spacing: 1px;
        }

        .collections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .collection-card {
            position: relative;
            height: 500px;
            border-radius: 2px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInScale 0.8s ease-out backwards;
        }

        .collection-card:nth-child(1) { animation-delay: 0.1s; }
        .collection-card:nth-child(2) { animation-delay: 0.2s; }
        .collection-card:nth-child(3) { animation-delay: 0.3s; }
        .collection-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .collection-card:hover {
            transform: translateY(-10px);
        }

        .collection-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .collection-card:hover .collection-image {
            transform: scale(1.1);
        }

        .collection-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 40px;
            background: linear-gradient(to top, rgba(44, 44, 44, 0.95), transparent);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.5s ease;
        }

        .collection-card:hover .collection-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        .collection-name {
            font-family: 'Crimson Pro', serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--cream);
            margin-bottom: 0.5rem;
        }

        .collection-tagline {
            font-size: 0.95rem;
            color: var(--sand);
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .collection-items {
            font-size: 0.85rem;
            color: var(--sand);
            opacity: 0.8;
        }

        /* Stats Section */
        .stats {
            padding: 100px 5%;
            background: var(--walnut);
            color: var(--cream);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-item {
            text-align: center;
            animation: fadeInUp 0.8s ease-out backwards;
        }

        .stat-item:nth-child(1) { animation-delay: 0.1s; }
        .stat-item:nth-child(2) { animation-delay: 0.2s; }
        .stat-item:nth-child(3) { animation-delay: 0.3s; }
        .stat-item:nth-child(4) { animation-delay: 0.4s; }

        .stat-number {
            font-family: 'Crimson Pro', serif;
            font-size: 4rem;
            font-weight: 300;
            margin-bottom: 0.5rem;
            color: var(--copper);
        }

        .stat-label {
            font-size: 0.95rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        /* Philosophy Section */
        .philosophy {
            padding: 120px 5%;
            background: var(--sand);
            position: relative;
            overflow: hidden;
        }

        .philosophy::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(156, 175, 136, 0.15), transparent);
            animation: float 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, 30px) rotate(180deg); }
        }

        .philosophy-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .philosophy h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 3rem;
            font-weight: 300;
            color: var(--walnut);
            margin-bottom: 2rem;
            line-height: 1.3;
        }

        .philosophy p {
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--walnut);
            opacity: 0.85;
            margin-bottom: 1.5rem;
        }

        /* Footer */
        footer {
            padding: 80px 5% 40px;
            background: var(--charcoal);
            color: var(--sand);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-section h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 1.5rem;
            color: var(--cream);
        }

        .footer-section p,
        .footer-section a {
            color: var(--sand);
            opacity: 0.8;
            text-decoration: none;
            line-height: 2;
            transition: opacity 0.3s ease;
        }

        .footer-section a:hover {
            opacity: 1;
            color: var(--copper);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid rgba(232, 220, 196, 0.2);
            opacity: 0.6;
            font-size: 0.9rem;
        }

        /* Floating Background Elements */
        .bg-element {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .bg-element-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(184, 115, 51, 0.08), transparent);
            top: 10%;
            right: 5%;
            animation: floatSlow 20s ease-in-out infinite;
        }

        .bg-element-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(156, 175, 136, 0.08), transparent);
            bottom: 20%;
            left: 10%;
            animation: floatSlow 25s ease-in-out infinite reverse;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(50px, -50px); }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }

            .hero-tagline {
                font-size: 1rem;
            }

            .section-header h2 {
                font-size: 2.5rem;
            }

            .collection-card {
                height: 400px;
            }

            .stat-number {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="bg-element bg-element-1"></div>
    <div class="bg-element bg-element-2"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Artisan Furniture Co.</h1>
            <p class="hero-tagline">Timeless Design, Modern Living</p>
            <a href="#collections" class="cta-button">Explore Collections</a>
        </div>
        <div class="scroll-indicator"></div>
    </section>

    <!-- Featured Collections -->
    <section id="collections" class="collections">
        <div class="section-header">
            <h2>Our Collections</h2>
            <p>Curated designs for every aesthetic</p>
        </div>

        <div class="collections-grid">
            @foreach($featuredCollections as $collection)
            <div class="collection-card" style="background-color: {{ $collection['color'] }}">
                <!-- In production, add actual images -->
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, {{ $collection['color'] }} 0%, color-mix(in srgb, {{ $collection['color'] }} 70%, black) 100%);"></div>
                <div class="collection-overlay">
                    <h3 class="collection-name">{{ $collection['name'] }}</h3>
                    <p class="collection-tagline">{{ $collection['tagline'] }}</p>
                    <p class="collection-items">{{ $collection['items'] }} pieces</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['years_experience'] }}</div>
                <div class="stat-label">Years of Excellence</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $stats['satisfied_customers'] }}</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $stats['furniture_pieces'] }}</div>
                <div class="stat-label">Unique Designs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $stats['cities_delivered'] }}</div>
                <div class="stat-label">Cities Served</div>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section class="philosophy">
        <div class="philosophy-content">
            <h2>Where Craftsmanship Meets Vision</h2>
            <p>Every piece we create tells a story. From the careful selection of sustainable materials to the final stroke of a craftsman's hand, we believe furniture should be more than functional—it should inspire.</p>
            <p>Our commitment to timeless design means creating pieces that transcend trends, becoming cherished elements of your home for generations to come.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Artisan Furniture Co.</h3>
                <p>Crafting exceptional furniture since 2009. Each piece is a testament to quality, design, and sustainable practices.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="/shop">Shop</a></p>
                <p><a href="/collections">Collections</a></p>
                <p><a href="/about">About Us</a></p>
                <p><a href="/contact">Contact</a></p>
            </div>
            <div class="footer-section">
                <h3>Customer Care</h3>
                <p><a href="/shipping">Shipping Info</a></p>
                <p><a href="/returns">Returns</a></p>
                <p><a href="/warranty">Warranty</a></p>
                <p><a href="/faq">FAQ</a></p>
            </div>
            <div class="footer-section">
                <h3>Connect</h3>
                <p><a href="#">Instagram</a></p>
                <p><a href="#">Pinterest</a></p>
                <p><a href="#">Facebook</a></p>
                <p><a href="mailto:hello@artisanfurniture.com">hello@artisanfurniture.com</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Artisan Furniture Co. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe sections for scroll reveal
        document.querySelectorAll('.section-header, .stat-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });

        // Parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.bg-element');
            parallaxElements.forEach((el, index) => {
                const speed = (index + 1) * 0.3;
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    </script>
</body>
</html>