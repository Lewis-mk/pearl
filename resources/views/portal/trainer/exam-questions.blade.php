@extends('layouts.portal')

@section('title', 'Manage Questions & Slots: ' . $exam->title . ' — Pearl Training Institute')
@section('page_title', 'Question Bank & Sit-in Slots: ' . $exam->title)
@section('page_subtitle', $exam->course->title . ' — Total Marks: ' . $exam->total_marks)

@section('sidebar_menu')
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>&larr;</span> <span>Back to Exams List</span>
    </a>
@endsection

@section('portal_content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 7 Cols: Question Bank & Sit-in Slots List -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Question Bank List -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Current Question Bank ({{ $exam->questions->count() }} Questions)</h3>
                    <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded">
                        Allocated: {{ $exam->questions->sum('marks') }} / {{ $exam->total_marks }} Marks
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($exam->questions as $index => $q)
                        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="font-bold text-xs text-blue-700 uppercase">Question #{{ $index + 1 }} &bull; {{ str_replace('_', ' ', $q->type) }}</span>
                                <span class="font-mono text-xs font-bold text-slate-700">{{ $q->marks }} Marks</span>
                            </div>
                            <p class="text-xs font-semibold text-slate-900 leading-relaxed">{{ $q->question_text }}</p>

                            @if($q->type === 'multiple_choice' && !empty($q->options))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                    @foreach($q->options as $opt)
                                        <div class="p-2.5 rounded-lg border text-xs flex items-center space-x-2 {{ $opt['key'] === $q->correct_answer ? 'bg-emerald-50 border-emerald-300 font-bold text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                            <span class="font-mono">({{ $opt['key'] }})</span>
                                            <span>{{ $opt['text'] }}</span>
                                            @if($opt['key'] === $q->correct_answer)
                                                <span class="text-[10px] text-emerald-700 uppercase ml-auto">✓ Correct</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                            No questions added to this exam yet. Add a question using the form on the right.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Published Sit-in Slots Section -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="font-heading font-bold text-lg text-slate-900">Configured Physical Sit-in Exam Dates</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($exam->sitinSlots as $slot)
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">{{ $slot->slot_datetime->format('d M Y, h:i A') }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $slot->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $slot->status }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-600">📍 Venue: <strong>{{ $slot->venue }}</strong></div>
                            <div class="text-xs text-slate-400">
                                Booked Capacity: <strong class="text-slate-800">{{ $slot->booked_count }} / {{ $slot->capacity }}</strong> students
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 p-6 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                            No physical sit-in dates published for this exam.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right 5 Cols: Add Question & Add Sit-in Slot Forms -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Add Question Form -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-md space-y-4">
                <h3 class="font-heading font-bold text-lg text-slate-900">Add Question to Bank</h3>

                <form action="{{ route('trainer.exam.questions.store', $exam->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Type *</label>
                        <select id="qTypeSelect" name="type" required onchange="toggleOptionFields()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            <option value="multiple_choice">Multiple Choice (Auto-graded MCQ)</option>
                            <option value="free_response">Free Response / Coding Task (Manual Trainer Grading)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Text *</label>
                        <textarea name="question_text" rows="3" required placeholder="Enter question statement or coding requirement..." class="w-full p-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Allocated Marks *</label>
                        <input type="number" name="marks" value="20" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <!-- Multiple Choice Options Group -->
                    <div id="mcqFields" class="space-y-2.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase">MCQ Options & Correct Key</label>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-xs font-bold w-4">A:</span>
                                <input type="text" name="options[A]" placeholder="Option A text" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs bg-white">
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-xs font-bold w-4">B:</span>
                                <input type="text" name="options[B]" placeholder="Option B text" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs bg-white">
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-xs font-bold w-4">C:</span>
                                <input type="text" name="options[C]" placeholder="Option C text" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs bg-white">
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-xs font-bold w-4">D:</span>
                                <input type="text" name="options[D]" placeholder="Option D text" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs bg-white">
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-[11px] font-bold text-emerald-800 uppercase mb-1">Correct Answer Key *</label>
                            <select name="correct_answer" class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-300 text-xs bg-white font-bold font-mono">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl font-heading font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition text-center">
                        Add Question to Bank &rarr;
                    </button>
                </form>
            </div>

            <!-- Add Sit-in Slot Form -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-md space-y-4">
                <h3 class="font-heading font-bold text-lg text-slate-900">Publish Physical Sit-in Exam Date</h3>

                <form action="{{ route('trainer.exam.sitin.store', $exam->id) }}" method="POST" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Date & Time *</label>
                        <input type="datetime-local" name="slot_datetime" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Physical Exam Venue / Room *</label>
                        <input type="text" name="venue" required placeholder="e.g. Main Lab 1, Pearl Campus" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Seating Capacity Limit *</label>
                        <input type="number" name="capacity" value="25" min="1" max="200" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl font-heading font-bold text-xs text-white bg-slate-900 hover:bg-blue-600 shadow-md transition text-center">
                        Publish Sit-in Slot &rarr;
                    </button>
                </form>
            </div>

        </div>

    </div>

    <script>
        function toggleOptionFields() {
            const select = document.getElementById('qTypeSelect');
            const mcqFields = document.getElementById('mcqFields');
            if (select.value === 'multiple_choice') {
                mcqFields.classList.remove('hidden');
            } else {
                mcqFields.classList.add('hidden');
            }
        }
    </script>
@endsection
