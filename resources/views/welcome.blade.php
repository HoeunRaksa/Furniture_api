<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniture Art & Design | Premium Collections</title>
    
    <!-- MDB UI Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #fdfdfc;
            --accent-color: #1b1b18;
            --text-muted: #706f6c;
            --glass-bg: rgba(255, 255, 255, 0.7);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--primary-bg);
            color: var(--accent-color);
            overflow-x: hidden;
        }

        h1, h2, h3, .playfair {
            font-family: 'Playfair Display', serif;
        }

        /* Glass Navbar */
        .navbar-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -1px;
            font-size: 1.5rem;
        }

        /* Hero Section */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            padding: 100px 0;
        }

        .hero-text {
            max-width: 600px;
            z-index: 2;
        }

        .hero-title {
            font-size: 4.5rem;
            line-height: 1.1;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero-image-container {
            position: absolute;
            right: -10%;
            top: 50%;
            transform: translateY(-50%);
            width: 60%;
            z-index: 1;
        }

        .floating-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
        }

        .floating-card:hover {
            transform: translateY(-10px);
        }

        /* Categories Section */
        .section-title {
            font-size: 2.5rem;
            margin-bottom: 3rem;
            text-align: center;
        }

        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            height: 400px;
            cursor: pointer;
        }

        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .category-card:hover img {
            transform: scale(1.1);
        }

        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
            color: white;
        }

        /* UI Elements */
        .btn-premium {
            background: var(--accent-color);
            color: white;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: scale(1.05);
            color: white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .btn-outline-premium {
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            padding: 10px 33px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: none;
            transition: all 0.3s ease;
        }

        .btn-outline-premium:hover {
            background: var(--accent-color);
            color: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal {
            animation: fadeInUp 1s ease forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }

        @media (max-width: 992px) {
            .hero-title { font-size: 3rem; }
            .hero-image-container { position: relative; right: 0; width: 100%; margin-top: 3rem; transform: none; top: 0; }
            .hero-section { text-align: center; }
            .hero-text { margin: 0 auto; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-glass fixed-top">
        <div class="container py-2">
            <a class="navbar-brand" href="#">
                <i class="bi bi-box-seam me-2"></i>HOEUN<span class="text-muted">RAKSA</span>
            </a>
            <button class="navbar-toggler" type="button" data-mdb-collapse-init data-mdb-target="#navbarNav">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item px-3"><a class="nav-link fw-500" href="#home">Home</a></li>
                    <li class="nav-item px-3"><a class="nav-link fw-500" href="#collections">Collections</a></li>
                    <li class="nav-item px-3"><a class="nav-link fw-500" href="#about">About</a></li>
                    <li class="nav-item ps-lg-4">
                        @auth
                            <a href="{{ route('home') }}" class="btn btn-premium d-flex align-items-center">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-premium d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> Login Admin
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container px-4">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-text reveal">
                        <span class="text-uppercase tracking-wider fw-bold text-muted mb-3 d-block">Premium Home Decor</span>
                        <h1 class="hero-title">The Art of <br><span class="playfair italic text-muted">Minimalist</span> Living</h1>
                        <p class="lead mb-5 text-muted">Discover our curated collection of artisanal furniture designed to elevate your living space with timeless elegance and modern functionality.</p>
                        <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                            <a href="#collections" class="btn btn-premium btn-lg">Explore Collection</a>
                            <a href="#about" class="btn btn-outline-premium btn-lg">Our Story</a>
                        </div>
                        
                        <div class="mt-5 d-flex gap-5 pt-3 border-top justify-content-center justify-content-lg-start">
                            <div>
                                <h4 class="fw-bold mb-0">{{ $customers_count ?? '12k+' }}</h4>
                                <small class="text-muted">Customers</small>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">{{ $products_count ?? '1.5k+' }}</h4>
                                <small class="text-muted">Designs</small>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">{{ $categories_count ?? '20+' }}</h4>
                                <small class="text-muted">Categories</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="hero-image-container reveal delay-1">
                <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1200&q=80" class="img-fluid rounded-4 shadow-2-strong" alt="Premium Furniture">
                <div class="floating-card position-absolute d-none d-lg-block" style="bottom: 10%; left: -5%;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-2 rounded-circle me-3">
                            <i class="bi bi-stars text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Quality First</h6>
                            <small class="text-muted">Certified Materials</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-dark" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Showcase -->
    <section class="py-5" id="collections">
        <div class="container py-5">
            <h2 class="section-title reveal">Our Signature Collections</h2>
            <div class="row g-4">
                <div class="col-md-4 reveal delay-1">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1583847268964-b28dc2f51ac9?auto=format&fit=crop&w=800&q=80" alt="Living Room">
                        <div class="category-overlay">
                            <h4 class="fw-bold m-0">Living Spaces</h4>
                            <p class="small opacity-75">Comfort meets aesthetics</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal delay-1">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1595515106969-1ce29566ff1c?auto=format&fit=crop&w=800&q=80" alt="Dining Room">
                        <div class="category-overlay">
                            <h4 class="fw-bold m-0">Kitchen & Dining</h4>
                            <p class="small opacity-75">Crafted for memories</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal delay-1">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80" alt="Bedroom">
                        <div class="category-overlay">
                            <h4 class="fw-bold m-0">Serene Bedrooms</h4>
                            <p class="small opacity-75">Ultimate relaxation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 border-top bg-white">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>HOEUNRAKSA</h5>
                    <p class="text-muted small mb-0">&copy; 2026 Hoeun Raksa Furniture. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-4 mt-md-0 text-muted">
                    <a href="#" class="mx-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="mx-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="mx-2"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- MDB JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
</body>
</html>
