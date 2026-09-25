@extends('layouts.public')

@section('title', 'Courses & Intake Cohorts — Pearl Training Institute')
@section('meta_description', 'Explore practical short courses in Technology, Design, Business, and Cyber packages at Pearl Training Institute Kenya.')

@section('content')
    <!-- Page Header -->
    <section class="bg-slate-900 text-white py-14 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-2">Curriculum Catalog</span>
            <h1 class="font-heading font-black text-3xl sm:text-5xl text-white">All Programs & Intake Cohorts</h1>
            <p class="text-sm text-slate-300 max-w-2xl mt-3 font-normal">
                Hands-on practical short courses designed for immediate employability, freelance mastery, and business efficiency.
            </p>
        </div>
    </section>

    <!-- Courses Grid & Filters -->
    <section class="py-14 bg-slate-50 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 mb-10">
                <a href="{{ route('courses', ['category' => 'all']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ empty($category) || $category === 'all' ? 'bg-pearl-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('courses', ['category' => $cat]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $category === $cat ? 'bg-pearl-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            <!-- Course Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($courses as $course)
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="h-48 bg-slate-800 relative overflow-hidden">
                                <img src="{{ $course->image_url ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600' }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @if($course->featured_badge)
                                    <div class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">
                                        {{ $course->featured_badge }}
                                    </div>
                                @endif
                                <div class="absolute bottom-3 right-3 bg-slate-900/90 text-white text-xs font-semibold px-2.5 py-1 rounded-md">
                                    ⏱ {{ $course->duration_weeks }} Weeks
                                </div>
                            </div>

                            <div class="p-6">
                                <span class="text-[11px] font-bold text-pearl-600 uppercase tracking-wider block mb-1.5">{{ $course->category }}</span>
                                <h3 class="font-heading font-bold text-xl text-slate-900 leading-snug group-hover:text-pearl-600 transition mb-3">
                                    <a href="{{ route('courses.detail', $course->slug) }}">{{ $course->title }}</a>
                                </h3>
                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-3">
                                    {{ $course->short_description }}
                                </p>

                                <!-- Active Cohorts Badge -->
                                @if($course->activeCohorts->isNotEmpty())
                                    <div class="mt-4 p-2.5 rounded-lg bg-teal-50 border border-teal-100 text-[11px] text-teal-900 flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Next Intake: <strong>{{ $course->activeCohorts->first()->name }}</strong> (Starts {{ $course->activeCohorts->first()->start_date->format('d M Y') }})</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="px-6 pb-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] text-slate-400 font-semibold uppercase">Course Fee</div>
                                <div class="font-heading font-black text-xl text-slate-900">KES {{ number_format($course->total_fee) }}</div>
                                <div class="text-[10px] text-slate-400">Min Deposit: KES {{ number_format($course->deposit_required) }}</div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('courses.detail', $course->slug) }}" class="px-4 py-2.5 text-xs font-bold bg-slate-900 hover:bg-pearl-600 text-white rounded-xl transition">
                                    View Syllabus
                                </a>
                                <a href="{{ route('register', ['course_id' => $course->id]) }}" class="px-4 py-2.5 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-xl shadow-sm transition">
                                    Enroll
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-slate-200">
                        <div class="text-4xl mb-2">📚</div>
                        <h4 class="font-heading font-bold text-lg text-slate-700">No courses found in this category</h4>
                        <p class="text-xs text-slate-400 mt-1">Check back soon or browse other categories.</p>
                        <a href="{{ route('courses') }}" class="mt-4 inline-block px-4 py-2 text-xs font-bold bg-pearl-600 text-white rounded-lg">View All Courses</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $courses->links() }}
            </div>

        </div>
    </section>
@endsection
