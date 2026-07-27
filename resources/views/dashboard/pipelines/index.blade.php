@extends('layouts.dashboard')

@section('title', 'Recruitment Pipelines - Recruiter Dashboard')
@section('header_title', 'Pipeline Templates')

@section('content')
<!-- Header Bar -->
<div class="flex justify-between items-center mb-6">
    <p class="text-xs text-gray-500">Configure hiring stages and templates mapped to job postings.</p>
    <a href="{{ route('pipelines.create') }}" class="btn btn-primary btn-sm px-5 py-2.5 rounded-full font-bold shadow-md shadow-[#FF6B00]/15">
        <i class="fa-solid fa-plus text-[10px]"></i> Create Template
    </a>
</div>

<!-- Templates list grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($templates as $tmpl)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition duration-200">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="badge badge-gray border border-gray-200 uppercase font-extrabold text-[8px] tracking-wider">{{ $tmpl->department_hint ?? 'General' }}</span>
                    @if($tmpl->is_default)
                        <span class="badge badge-orange font-bold text-[8px] uppercase tracking-wider">Default</span>
                    @endif
                </div>

                <h3 class="font-extrabold text-sm text-[#111318] mb-2">{{ $tmpl->name }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed mb-6">{{ $tmpl->description }}</p>

                <!-- Stages indicator line -->
                <div class="space-y-3.5 mb-6">
                    <h4 class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">Hiring Stages ({{ $tmpl->stages_count }})</h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($tmpl->stages()->orderBy('sort_order')->get() as $stage)
                            <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-150 rounded-full px-3 py-1 text-[10px] font-semibold text-gray-750">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $stage->color }};"></span>
                                {{ $stage->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer actions -->
            <div class="flex gap-2 border-t border-gray-100 pt-4 mt-auto">
                <a href="{{ route('pipelines.edit', ['id' => $tmpl->id]) }}" class="btn btn-secondary btn-sm flex-grow rounded-xl text-xs py-2">
                    <i class="fa-regular fa-edit mr-1"></i> Edit Template
                </a>
                
                @if(!$tmpl->is_default)
                    <form action="{{ route('pipelines.destroy', ['id' => $tmpl->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this template?')" class="btn btn-danger btn-sm p-2 px-3 rounded-xl hover:bg-red-650" title="Delete Template">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
