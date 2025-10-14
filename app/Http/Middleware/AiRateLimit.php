<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class AiRateLimit
{
    public function handle($request, Closure $next)
    {
        $uid = auth()->id() ?? 'guest';
        $perMin = (int) env('AI_RATE_PER_MINUTE', 60);
        $daily  = (int) (auth()->user()?->isJudge() ? env('AI_DAILY_QUOTA_JUDGE', 500) : env('AI_DAILY_QUOTA_USER', 200));

        // rate per menit
        $k1 = "ai:rate:$uid";
        $c1 = Redis::incr($k1);
        if ($c1 == 1) Redis::expire($k1, 60);
        if ($c1 > $perMin) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }

        // kuota harian
        $k2 = "ai:daily:$uid:".now()->toDateString();
        $c2 = Redis::incr($k2);
        if ($c2 == 1) Redis::expireAt($k2, now()->endOfDay()->timestamp);
        if ($c2 > $daily) {
            return response()->json(['message' => 'Daily quota reached'], 429);
        }

        return $next($request);
    }
}
