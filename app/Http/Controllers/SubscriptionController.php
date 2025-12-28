<?php
// [file name]: SubscriptionController.php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = Plan::where('is_active', true)->get();
        $currentPlan = Auth::user()->currentPlan;
        $activeSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.plans', compact('plans', 'currentPlan', 'activeSubscription'));
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $user = Auth::user();
        
        // Check if user already has an active subscription
        if ($user->hasActiveSubscription()) {
            return back()->with('error', 'You already have an active subscription.');
        }

        // For free plans, activate immediately
        if ($plan->isFree()) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null
            ]);

            $user->update(['current_plan_id' => $plan->id]);

            return redirect()->route('dashboard')
                ->with('success', "You have successfully subscribed to the {$plan->name} plan.");
        }

        // For paid plans, redirect to payment gateway
        // This is a simplified version - integrate with Stripe/your payment provider
        return back()->with('info', 'Payment integration required for paid plans.');
    }

    public function cancelSubscription(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => now() // Or end of billing period
        ]);

        // Downgrade to free plan
        $freePlan = Plan::where('price', 0)->first();
        if ($freePlan) {
            $user->update(['current_plan_id' => $freePlan->id]);
        }

        return back()->with('success', 'Your subscription has been canceled.');
    }

    public function billingHistory()
    {
        $subscriptions = Auth::user()->subscriptions()->with('plan')->latest()->get();
        return view('subscriptions.history', compact('subscriptions'));
    }
}