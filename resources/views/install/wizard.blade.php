@extends('layouts.install')

@section('content')

<div class="min-h-screen flex w-full items-center justify-center p-4">
    <div class="glass tilt-card w-full max-w-4xl p-8">
        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                @for($i = 1; $i <= 12; $i++)
                    @if($i > 1)
                        <div class="flex-1 h-1 mx-2 {{ $i <= $step ? 'bg-[var(--color-primary)] shadow-[0_0_10px_var(--color-primary)]' : 'bg-white/10' }} transition-all duration-300 rounded-full"></div>
                    @endif
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300 {{ $i < $step ? 'bg-[var(--color-success)]/20 border-2 border-[var(--color-success)] text-[var(--color-success)]' : ($i == $step ? 'bg-[var(--color-primary)] text-white shadow-[0_0_20px_var(--color-primary)]' : 'bg-white/5 border-2 border-white/10 text-white/40') }}">
                        @if($i < $step)
                            ✓
                        @else
                            {{ $i }}
                        @endif
                    </div>
                @endfor
            </div>
            <h3 class="text-center text-white/60 text-sm font-medium tracking-wider uppercase">Step {{ $step }} of 12</h3>
        </div>

        <!-- Step Content -->
        <div class="mb-8">
            @if($step == 1)
                @include('install.steps.step-1')
            @elseif($step == 2)
                @include('install.steps.step-2')
            @elseif($step == 3)
                @include('install.steps.step-3')
            @elseif($step == 4)
                @include('install.steps.step-4')
            @elseif($step == 5)
                @include('install.steps.step-5')
            @elseif($step == 6)
                @include('install.steps.step-6')
            @elseif($step == 7)
                @include('install.steps.step-7')
            @elseif($step == 8)
                @include('install.steps.step-8')
            @elseif($step == 9)
                @include('install.steps.step-9')
            @elseif($step == 10)
                @include('install.steps.step-10')
            @elseif($step == 11)
                @include('install.steps.step-11')
            @elseif($step == 12)
                @include('install.steps.step-12')
            @endif
        </div>

        <!-- Navigation Buttons -->
        @if($step != 12 || !session('installation_complete'))
            <div class="flex justify-between items-center mt-8">
                @if($step > 1)
                    <a href="{{ route('install', ['step' => $step - 1]) }}" class="ng-btn ng-btn-secondary px-6 py-3">
                        Previous
                    </a>
                @else
                    <div></div>
                @endif

                @if($step == 2)
                    @if(isset($data['requirements_passed']) && $data['requirements_passed'])
                        <button type="submit" form="step-form" class="ng-btn ng-btn-primary px-6 py-3">
                            Next
                        </button>
                    @endif
                @elseif($step == 12)
                    <button type="submit" form="step-form" class="ng-btn ng-btn-success px-6 py-3">
                        Complete Installation
                    </button>
                @elseif($step < 12)
                    <button type="submit" form="step-form" class="ng-btn ng-btn-primary px-6 py-3">
                        Next
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
