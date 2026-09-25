@extends('layouts.portal')

@section('title', 'Exam in Progress: ' . $exam->title . ' — Pearl Training Institute')
@section('page_title', $exam->title)
@section('page_subtitle', 'Online Exam & Assessment in Progress')

@section('sidebar_menu')
    <a href="{{ route('student.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>&larr;</span> <span>Back to Exams</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Sticky Exam Timer Header -->
        <div class="p-5 rounded-2xl bg-slate-900 text-white flex items-center justify-between shadow-lg sticky top-20 z-40">
            <div>
                <span class="text-[10px] uppercase font-bold text-teal-400">Timed Assessment</span>
                <h3 class="font-heading font-bold text-base text-white truncate max-w-md">{{ $exam->title }}</h3>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs text-slate-400">Time Remaining:</span>
                <div id="examTimer" class="px-4 py-2 bg-slate-800 border border-slate-700 text-amber-400 font-mono font-black text-xl rounded-xl">
                    {{ sprintf('%02d:00', $exam->duration_minutes) }}
                </div>
            </div>
        </div>

        <!-- Questions Form -->
        <form id="examForm" action="{{ route('student.exams.submit', $exam->id) }}" method="POST" class="space-y-6">
            @csrf

            @foreach($questions as $index => $q)
                <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-pearl-50 text-pearl-800 font-bold flex items-center justify-center text-sm">
                                Q{{ $index + 1 }}
                            </span>
                            <span class="text-[10px] uppercase font-bold text-slate-400">
                                {{ str_replace('_', ' ', $q->type) }}
                            </span>
                        </div>
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-mono font-bold">
                            {{ $q->marks }} Marks
                        </span>
                    </div>

                    <div class="text-sm font-semibold text-slate-900 leading-relaxed whitespace-pre-line">
                        {{ $q->question_text }}
                    </div>

                    <!-- Multiple Choice Option Set -->
                    @if($q->type === 'multiple_choice' && !empty($q->options))
                        <div class="space-y-2.5 pt-2">
                            @foreach($q->options as $opt)
                                <label class="p-4 rounded-xl border border-slate-200 hover:border-pearl-500 hover:bg-slate-50 transition flex items-center space-x-3 cursor-pointer text-xs">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt['key'] }}" class="text-pearl-600 focus:ring-pearl-500 w-4 h-4">
                                    <span class="font-bold font-mono text-slate-700">({{ $opt['key'] }})</span>
                                    <span class="text-slate-800">{{ $opt['text'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <!-- Free Response Textarea -->
                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1">Your Detailed Answer / Solution:</label>
                            <textarea name="answers[{{ $q->id }}]" rows="5" placeholder="Type your answer, code, or explanation here..." class="w-full p-4 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 leading-relaxed font-mono"></textarea>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Submit Button -->
            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                    * Once you submit, your score for objective questions will be calculated and free-response sent to your trainer.
                </div>
                <button type="submit" onclick="return confirm('Are you sure you want to finish and submit your exam attempt?');" class="px-8 py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95">
                    Finish & Submit Exam &rarr;
                </button>
            </div>
        </form>

    </div>

    <!-- Countdown Timer Script -->
    <script>
        let durationMinutes = {{ $exam->duration_minutes }};
        let totalSeconds = durationMinutes * 60;
        const timerDisplay = document.getElementById('examTimer');
        const examForm = document.getElementById('examForm');

        const interval = setInterval(() => {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(interval);
                timerDisplay.textContent = "00:00";
                alert("Time has expired! Submitting your exam attempt automatically.");
                examForm.submit();
                return;
            }

            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            timerDisplay.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

            if (totalSeconds <= 300) { // 5 minutes warning
                timerDisplay.classList.add('text-rose-500', 'animate-pulse');
            }
        }, 1000);
    </script>
@endsection
