@extends('layouts.portal')

@section('title', 'Course Materials Upload — Pearl Training Institute')
@section('page_title', 'Learning Materials Management')
@section('page_subtitle', 'Upload syllabus notes, slide decks, practical coding assignments, and external video links')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Instruction & Cohorts</div>
    <a href="{{ route('trainer.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('trainer.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🗓️</span> <span>Schedule & Classes</span>
    </a>
    <a href="{{ route('trainer.materials') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
        <span>📁</span> <span>Upload Materials</span>
    </a>
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📝</span> <span>Exams & Question Bank</span>
    </a>

    @canPermission('course.create')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Curriculum Permissions</div>
        <a href="{{ route('trainer.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
            <span>📚</span> <span>Propose Courses</span>
        </a>
    @endcanPermission

    @canPermission('student.admit')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Admissions Permissions</div>
        <a href="{{ route('trainer.admission') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
            <span>➕</span> <span>In-Person Admission</span>
        </a>
    @endcanPermission
@endsection

@section('portal_content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 7 Cols: Uploaded Materials List -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">Your Uploaded Materials</h3>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                @forelse($materials as $mat)
                    <div class="p-5 hover:bg-slate-50 transition flex items-center justify-between gap-4">
                        <div class="space-y-1 overflow-hidden">
                            <div class="flex items-center space-x-2">
                                <span class="font-heading font-bold text-sm text-slate-900 truncate">{{ $mat->title }}</span>
                                <span class="text-[10px] uppercase font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $mat->file_type }}</span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">{{ $mat->description }}</p>
                            <div class="text-[11px] text-slate-400">
                                Course: <strong class="text-slate-700">{{ $mat->course->title }}</strong> &bull; Uploaded {{ $mat->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($mat->external_url)
                                <a href="{{ $mat->external_url }}" target="_blank" class="px-3 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">Link &rarr;</a>
                            @else
                                <a href="{{ asset('storage/' . $mat->file_path) }}" download class="px-3 py-1.5 text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">Download</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">No materials uploaded yet.</div>
                @endforelse
            </div>

            <div>
                {{ $materials->links() }}
            </div>
        </div>

        <!-- Right 5 Cols: Upload Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Course Resources</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Upload Learning Resource</h3>
                    <p class="text-xs text-slate-400 mt-1">Make documents or video URLs available to enrolled students.</p>
                </div>

                <form action="{{ route('trainer.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Course *</label>
                        <select name="course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Cohort (Optional &mdash; Blank for all cohorts)</label>
                        <select name="cohort_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                            <option value="">-- Available to All Cohorts of Course --</option>
                            @foreach($cohorts as $coh)
                                <option value="{{ $coh->id }}">{{ $coh->course->title }} &mdash; {{ $coh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Resource Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Lecture 3: Eloquent Relationships PDF" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Short Description</label>
                        <textarea name="description" rows="2" placeholder="Brief outline of contents..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Resource Type *</label>
                        <select name="file_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                            <option value="document">PDF Document / Note</option>
                            <option value="slides">Presentation Slides</option>
                            <option value="video_link">External Video URL (YouTube/Vimeo)</option>
                            <option value="zip">Source Code ZIP / Archive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Upload File (Max 20MB)</label>
                        <input type="file" name="file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Or External Web URL (Optional)</label>
                        <input type="url" name="external_url" placeholder="https://..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-700/20 transition transform active:scale-95 text-center">
                        Upload & Publish Resource &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
