<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\PrintshopService;
use App\Models\ServiceRequest;
use App\Models\Certificate;
use App\Models\SystemSetting;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function home()
    {
        $featuredCourses = Course::where('status', 'published')
            ->with(['activeCohorts'])
            ->take(4)
            ->get();

        $services = PrintshopService::where('is_active', true)->take(6)->get();

        return view('public.home', compact('featuredCourses', 'services'));
    }

    public function courses(Request $request)
    {
        $category = $request->query('category');
        $query = Course::where('status', 'published')->with(['activeCohorts']);

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $courses = $query->paginate(9);
        $categories = Course::where('status', 'published')->distinct()->pluck('category');

        return view('public.courses', compact('courses', 'categories', 'category'));
    }

    public function courseDetail(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('status', 'published')
            ->with(['cohorts' => function ($q) {
                $q->whereIn('status', ['enrolling', 'ongoing'])->with('leadTrainer');
            }])
            ->firstOrFail();

        $relatedCourses = Course::where('status', 'published')
            ->where('id', '!=', $course->id)
            ->where('category', $course->category)
            ->take(3)
            ->get();

        return view('public.course-detail', compact('course', 'relatedCourses'));
    }

    public function printshop()
    {
        $services = PrintshopService::where('is_active', true)->get();
        return view('public.printshop', compact('services'));
    }

    public function submitServiceRequest(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'service_type' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ]);

        $requestCode = 'SR-' . date('Ym') . '-' . sprintf('%04d', rand(1, 9999));

        $filePath = null;
        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('printshop_requests', 'public');
        }

        // Calculate estimated quote if service matches
        $service = PrintshopService::where('name', $validated['service_type'])->first();
        $quoted = $service ? ((float)$service->unit_price * (int)$validated['quantity']) : 0.00;

        $serviceRequest = ServiceRequest::create([
            'request_code' => $requestCode,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => MpesaService::formatPhoneNumber($validated['customer_phone']),
            'customer_email' => $validated['customer_email'] ?? null,
            'service_type' => $validated['service_type'],
            'quantity' => $validated['quantity'],
            'instructions' => $validated['instructions'] ?? null,
            'attachment_path' => $filePath,
            'quoted_amount' => $quoted,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        return back()->with('service_success', [
            'code' => $requestCode,
            'name' => $validated['customer_name'],
            'amount' => $quoted,
        ]);
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        $settings = SystemSetting::pluck('value', 'key');
        return view('public.contact', compact('settings'));
    }

    public function verifyCertificate(Request $request)
    {
        $code = trim($request->query('code', ''));
        $certificate = null;

        if (!empty($code)) {
            $certificate = Certificate::where('verification_code', $code)
                ->orWhere('certificate_number', $code)
                ->with(['student', 'course', 'cohort', 'issuer'])
                ->first();
        }

        return view('public.verify-certificate', compact('certificate', 'code'));
    }
}
