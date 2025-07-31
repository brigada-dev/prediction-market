<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class RewardController extends Controller
{
    /**
     * Reward tokens to a user after watching an ad or completing a task.
     */
    public function rewardTokens(Request $request): JsonResponse
    {
        $request->validate([
            'task_type' => 'required|string|in:ad_view,daily_login,referral,task_completion',
            'task_id' => 'nullable|string|max:255',
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = Auth::user();
        $taskType = $request->task_type;
        $taskId = $request->task_id ?? 'default';

        // Rate limiting key
        $key = "reward_tokens:{$user->id}:{$taskType}:{$taskId}";

        // Different rate limits for different task types
        $rateLimits = [
            'ad_view' => ['max' => 20, 'decay' => 3600], // 20 per hour
            'daily_login' => ['max' => 1, 'decay' => 86400], // 1 per day
            'referral' => ['max' => 10, 'decay' => 86400], // 10 per day
            'task_completion' => ['max' => 5, 'decay' => 3600], // 5 per hour
        ];

        $limit = $rateLimits[$taskType];

        if (RateLimiter::tooManyAttempts($key, $limit['max'])) {
            return response()->json([
                'error' => 'Rate limit exceeded for this task type',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key, $limit['decay']);

        // Different token rewards for different tasks
        $tokenRewards = [
            'ad_view' => rand(10, 25),
            'daily_login' => 100,
            'referral' => 500,
            'task_completion' => rand(50, 150),
        ];

        $tokensEarned = $tokenRewards[$taskType];

        try {
            DB::transaction(function () use ($user, $tokensEarned) {
                $user->balance = bcadd((string) $user->balance, (string) $tokensEarned, 2);
                $user->save();
            });

            // Log the reward for analytics
            Log::info('Tokens rewarded', [
                'user_id' => $user->id,
                'task_type' => $taskType,
                'task_id' => $taskId,
                'tokens_earned' => $tokensEarned,
                'new_balance' => $user->balance,
            ]);

            return response()->json([
                'success' => true,
                'message' => "You earned {$tokensEarned} tokens!",
                'tokens_earned' => $tokensEarned,
                'new_balance' => (float) $user->balance,
                'task_type' => $taskType,
            ]);

        } catch (\Exception $e) {
            Log::error('Token reward failed', [
                'user_id' => $user->id,
                'task_type' => $taskType,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to reward tokens'
            ], 500);
        }
    }

    /**
     * Get available tasks for earning tokens.
     */
    public function getAvailableTasks(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = Auth::user();

        $tasks = [
            [
                'type' => 'ad_view',
                'name' => 'Watch Advertisement',
                'description' => 'Watch a short video ad to earn tokens',
                'tokens' => '10-25',
                'available' => !RateLimiter::tooManyAttempts("reward_tokens:{$user->id}:ad_view:default", 20),
                'cooldown' => RateLimiter::availableIn("reward_tokens:{$user->id}:ad_view:default"),
            ],
            [
                'type' => 'daily_login',
                'name' => 'Daily Login Bonus',
                'description' => 'Get bonus tokens for logging in daily',
                'tokens' => '100',
                'available' => !RateLimiter::tooManyAttempts("reward_tokens:{$user->id}:daily_login:default", 1),
                'cooldown' => RateLimiter::availableIn("reward_tokens:{$user->id}:daily_login:default"),
            ],
            [
                'type' => 'task_completion',
                'name' => 'Complete Tasks',
                'description' => 'Complete various platform tasks for tokens',
                'tokens' => '50-150',
                'available' => !RateLimiter::tooManyAttempts("reward_tokens:{$user->id}:task_completion:default", 5),
                'cooldown' => RateLimiter::availableIn("reward_tokens:{$user->id}:task_completion:default"),
            ],
        ];

        return response()->json([
            'tasks' => $tasks,
            'user_balance' => (float) $user->balance,
        ]);
    }

    /**
     * Get user's token earning history.
     */
    public function getTokenHistory(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        // This is a simple implementation. In a real app, you'd have a token_transactions table
        return response()->json([
            'message' => 'Token history feature coming soon',
            'current_balance' => (float) Auth::user()->balance,
        ]);
    }

    /**
     * Bonus tokens for special events or admin rewards.
     */
    public function bonusTokens(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1|max:10000',
            'reason' => 'required|string|max:255',
        ]);

        // This should be protected by admin middleware in production
        if (!Auth::check() || !Auth::user()->email === 'admin@admin.com') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user = User::findOrFail($request->user_id);
            $amount = (float) $request->amount;
            $reason = $request->reason;

            DB::transaction(function () use ($user, $amount) {
                $user->balance = bcadd((string) $user->balance, (string) $amount, 2);
                $user->save();
            });

            Log::info('Bonus tokens awarded', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
                'new_balance' => $user->balance,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Awarded {$amount} bonus tokens to {$user->name}",
                'user' => $user->name,
                'amount' => $amount,
                'new_balance' => (float) $user->balance,
            ]);

        } catch (\Exception $e) {
            Log::error('Bonus tokens failed', [
                'admin_id' => Auth::id(),
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to award bonus tokens'
            ], 500);
        }
    }
}
