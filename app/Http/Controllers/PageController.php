<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the welcome page with featured furniture collections
     */
    public function welcome(): View
    {
        $featuredCollections = [
            [
                'name' => 'Modern Minimalist',
                'tagline' => 'Clean lines, timeless beauty',
                'image' => 'modern-collection.jpg',
                'items' => 24,
                'color' => '#E8DCC4',
            ],
            [
                'name' => 'Vintage Revival',
                'tagline' => 'Heritage meets contemporary',
                'image' => 'vintage-collection.jpg',
                'items' => 18,
                'color' => '#B8A88A',
            ],
            [
                'name' => 'Urban Industrial',
                'tagline' => 'Raw materials, refined design',
                'image' => 'industrial-collection.jpg',
                'items' => 31,
                'color' => '#9B8B7E',
            ],
            [
                'name' => 'Scandinavian Comfort',
                'tagline' => 'Hygge for your home',
                'image' => 'scandinavian-collection.jpg',
                'items' => 27,
                'color' => '#D4C5B0',
            ],
        ];

        $stats = [
            'years_experience' => 15,
            'satisfied_customers' => '10,000+',
            'furniture_pieces' => '500+',
            'cities_delivered' => 50,
        ];

        return view('welcome', compact('featuredCollections', 'stats'));
    }

    /**
     * Display the shop page with furniture catalog
     */
    public function shop(): View
    {
        $categories = [
            'Living Room',
            'Bedroom',
            'Dining',
            'Office',
            'Outdoor',
            'Storage',
        ];

        $filters = [
            'style' => ['Modern', 'Vintage', 'Industrial', 'Scandinavian', 'Traditional'],
            'material' => ['Wood', 'Metal', 'Fabric', 'Leather', 'Glass'],
            'price_range' => ['Under $500', '$500-$1000', '$1000-$2000', '$2000+'],
        ];

        return view('shop', compact('categories', 'filters'));
    }

    /**
     * Display furniture collection details
     */
    public function collection(string $slug): View
    {
        // In production, this would fetch from database
        $collections = [
            'modern-minimalist' => [
                'name' => 'Modern Minimalist',
                'description' => 'Our Modern Minimalist collection embodies the philosophy that less is more. Each piece is carefully crafted with clean lines, functional design, and premium materials that stand the test of time.',
                'philosophy' => 'Simplicity in form, richness in detail',
                'items_count' => 24,
            ],
            'vintage-revival' => [
                'name' => 'Vintage Revival',
                'description' => 'Celebrating the craftsmanship of bygone eras while incorporating contemporary comfort and durability.',
                'philosophy' => 'Honoring tradition, embracing innovation',
                'items_count' => 18,
            ],
            'urban-industrial' => [
                'name' => 'Urban Industrial',
                'description' => 'Raw materials meet refined design in our Urban Industrial collection, perfect for modern loft living.',
                'philosophy' => 'Strength in simplicity',
                'items_count' => 31,
            ],
        ];

        $collection = $collections[$slug] ?? abort(404);

        return view('collection', compact('collection'));
    }

    /**
     * Display the about us page
     */
    public function about(): View
    {
        $timeline = [
            ['year' => 2009, 'event' => 'Company founded with a vision to revolutionize furniture design'],
            ['year' => 2012, 'event' => 'Opened our first flagship store'],
            ['year' => 2015, 'event' => 'Launched sustainable furniture line'],
            ['year' => 2018, 'event' => 'Expanded to international markets'],
            ['year' => 2021, 'event' => 'Introduced AR furniture preview technology'],
            ['year' => 2024, 'event' => 'Celebrating 15 years of design excellence'],
        ];

        $values = [
            [
                'title' => 'Sustainable Craftsmanship',
                'description' => 'Every piece is built to last, using responsibly sourced materials',
                'icon' => 'leaf',
            ],
            [
                'title' => 'Timeless Design',
                'description' => 'Creating furniture that transcends trends and seasons',
                'icon' => 'compass',
            ],
            [
                'title' => 'Artisan Quality',
                'description' => 'Handcrafted by skilled artisans who take pride in their work',
                'icon' => 'hammer',
            ],
            [
                'title' => 'Customer First',
                'description' => 'Your satisfaction and comfort are our top priorities',
                'icon' => 'heart',
            ],
        ];

        return view('about', compact('timeline', 'values'));
    }

    /**
     * Display the contact page
     */
    public function contact(): View
    {
        $locations = [
            [
                'city' => 'New York',
                'address' => '123 Fifth Avenue, NYC 10001',
                'phone' => '+1 (212) 555-0123',
                'hours' => 'Mon-Sat: 10am-8pm, Sun: 12pm-6pm',
            ],
            [
                'city' => 'Los Angeles',
                'address' => '456 Sunset Boulevard, LA 90028',
                'phone' => '+1 (323) 555-0456',
                'hours' => 'Mon-Sat: 10am-8pm, Sun: 12pm-6pm',
            ],
            [
                'city' => 'Chicago',
                'address' => '789 Michigan Avenue, CHI 60611',
                'phone' => '+1 (312) 555-0789',
                'hours' => 'Mon-Sat: 10am-8pm, Sun: 12pm-6pm',
            ],
        ];

        return view('contact', compact('locations'));
    }

    /**
     * Admin dashboard
     */
    public function admin(): View
    {
        // Check authentication in production
        $metrics = [
            'total_orders' => 1247,
            'pending_orders' => 23,
            'revenue_this_month' => 145890,
            'new_customers' => 89,
        ];

        return view('admin.dashboard', compact('metrics'));
    }

    /**
     * API endpoint for featured products
     */
    public function getFeaturedProducts(): JsonResponse
    {
        $products = [
            [
                'id' => 1,
                'name' => 'Oslo Lounge Chair',
                'price' => 1299,
                'image' => 'oslo-chair.jpg',
                'collection' => 'Scandinavian Comfort',
            ],
            [
                'id' => 2,
                'name' => 'Brooklyn Dining Table',
                'price' => 2499,
                'image' => 'brooklyn-table.jpg',
                'collection' => 'Urban Industrial',
            ],
            [
                'id' => 3,
                'name' => 'Monaco Velvet Sofa',
                'price' => 3299,
                'image' => 'monaco-sofa.jpg',
                'collection' => 'Modern Minimalist',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
