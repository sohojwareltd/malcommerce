<?php

namespace App\Http\Controllers;

use App\Models\DigitalCourse;
use App\Models\JobCircular;
use App\Models\Product;
use App\Models\Video;
use App\Models\WorkshopSeminar;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->where('is_digital', false)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->where('only_on_categories', false)
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_digital', false)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->get();

        $featuredCourses = DigitalCourse::query()
            ->active()
            ->where('is_featured', true)
            ->with('category')
            ->withCount(['activeLessons as lessons_count'])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $featuredJobs = JobCircular::where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $featuredWorkshops = WorkshopSeminar::where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('event_date')->orWhere('event_date', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $featuredVideos = Video::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('home', compact(
            'products',
            'featuredProducts',
            'featuredCourses',
            'featuredJobs',
            'featuredWorkshops',
            'featuredVideos'
        ));
    }
}
