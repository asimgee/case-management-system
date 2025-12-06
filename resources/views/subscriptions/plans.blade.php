<!-- [file name]: plans.blade.php -->
@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Choose Your Plan</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">Select the perfect plan for your legal practice</p>
    </div>

    <!-- Current Plan Status -->
    @if($currentPlan)
    <div class="card mb-8">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Current Plan: {{ $currentPlan->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($activeSubscription && $activeSubscription->isOnTrial())
                            Trial ends {{ $activeSubscription->trial_ends_at->diffForHumans() }}
                        @elseif($activeSubscription && $activeSubscription->isActive())
                            @if($activeSubscription->ends_at)
                                Plan ends {{ $activeSubscription->ends_at->diffForHumans() }}
                            @else
                                Active subscription
                            @endif
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $currentPlan->formatted_price }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">per {{ $currentPlan->billing_cycle }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        @foreach($plans as $plan)
            <div class="card card-hover {{ $currentPlan && $currentPlan->id === $plan->id ? 'ring-2 ring-blue-500' : '' }}">
                <div class="card-body">
                    <!-- Plan Header -->
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $plan->formatted_price }}</span>
                            <span class="text-gray-600 dark:text-gray-400">/{{ $plan->billing_cycle }}</span>
                        </div>
                        @if($plan->trial_days > 0)
                            <p class="text-sm text-green-600 dark:text-green-400 mt-2">{{ $plan->trial_days }} days free trial</p>
                        @endif
                    </div>

                    <!-- Plan Description -->
                    <p class="text-gray-600 dark:text-gray-400 text-center mb-6">{{ $plan->description }}</p>

                    <!-- Features -->
                    <ul class="space-y-3 mb-6">
                        @foreach($plan->features as $feature)
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                        
                        <!-- Limits -->
                        <li class="flex items-center">
                            <i class="fas fa-folder text-blue-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                @if($plan->max_cases === 0)
                                    Unlimited Cases
                                @else
                                    {{ $plan->max_cases }} Cases
                                @endif
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-users text-blue-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                @if($plan->max_clients === 0)
                                    Unlimited Clients
                                @else
                                    {{ $plan->max_clients }} Clients
                                @endif
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-database text-blue-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->max_storage_mb }} MB Storage
                            </span>
                        </li>
                    </ul>

                    <!-- Action Button -->
                    <div class="text-center">
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <button class="w-full bg-gray-500 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed" disabled>
                                Current Plan
                            </button>
                        @else
                            <form action="{{ route('subscriptions.subscribe', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full btn-primary py-3">
                                    @if($plan->isFree())
                                        Select Free Plan
                                    @else
                                        Subscribe Now
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Usage Statistics -->
    <div class="card">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Usage</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ auth()->user()->cases()->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Cases Created</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        @if(auth()->user()->getRemainingCases() === 'unlimited')
                            Unlimited remaining
                        @else
                            {{ auth()->user()->getRemainingCases() }} remaining
                        @endif
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">0</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Clients</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">Manage your clients</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">0 MB</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Storage Used</div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        {{ @$currentPlan->max_storage_mb }} MB available
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection