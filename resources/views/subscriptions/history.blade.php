<!-- [file name]: history.blade.php -->
@extends('layouts.app')

@section('title', 'Billing History')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing History</h1>
        <a href="{{ route('subscriptions.plans') }}" class="btn-secondary">
            <i class="fas fa-credit-card mr-2"></i> View Plans
        </a>
    </div>

    <!-- Current Subscription -->
    @if(@$activeSubscription)
    <div class="card mb-6">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Subscription</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Plan</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $activeSubscription->plan->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activeSubscription->isActive() ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ ucfirst($activeSubscription->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Price</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $activeSubscription->plan->formatted_price }}/{{ $activeSubscription->plan->billing_cycle }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Actions</p>
                    @if($activeSubscription->isActive() && !$activeSubscription->plan->isFree())
                    <form action="{{ route('subscriptions.cancel') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel your subscription?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">
                            Cancel Subscription
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Subscription History -->
    <div class="card">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subscription History</h3>
            
            @if($subscriptions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $subscription->plan->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $subscription->plan->formatted_price }}/{{ $subscription->plan->billing_cycle }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $subscription->status === 'canceled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                        {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $subscription->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-receipt text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">No subscription history found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection