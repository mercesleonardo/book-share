<?php

namespace App\Http\Controllers;

use App\Models\{Post, User};
use Illuminate\Support\Facades\{Auth, Cache};
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

    // Basic personal metrics
        $totalPostsUser = Post::query()->where('user_id', $user->id)->count();
        $last30Days     = Post::query()->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))->count();
        $latestUserPost = Post::query()->where('user_id', $user->id)->latest()->first();

        $recentPosts = Post::query()->where('user_id', $user->id)
            ->latest()->limit(5)->get(['id', 'title', 'slug', 'created_at']);

        $metrics = [
            [
                'key'   => 'total_posts',
                'label' => __('dashboard.metrics.total_posts'),
                'value' => $totalPostsUser,
            ],
            [
                'key'   => 'last_30_days',
                'label' => __('dashboard.metrics.last_30_days'),
                'value' => $last30Days,
            ],
        ];

        $moderationQueue = collect();

        $trend14Days      = collect();
        $topCategories    = collect();
        $growthPercentage = null;

        if ($user->isAdmin() || $user->isModerator()) {
            // Simple 60s cache for global metrics
            [$totalPosts, $totalUsers, $postsToday, $postsWeek, $withImageRatio] = Cache::remember('dashboard.global_metrics', 60, function () {
                $totalPosts = Post::query()->count();
                $totalUsers = User::query()->count();
                $postsToday = Post::query()->whereDate('created_at', today())->count();
                $postsWeek  = Post::query()->where('created_at', '>=', now()->subDays(7))->count();
                $withImage  = Post::query()->whereNotNull('image')->where('image', '!=', '')->count();
                $ratio      = $totalPosts > 0 ? round(($withImage / $totalPosts) * 100, 1) : 0;

                return [$totalPosts, $totalUsers, $postsToday, $postsWeek, $ratio];
            });

            $metrics = array_merge($metrics, [
                [
                    'key'   => 'global_total_posts',
                    'label' => __('dashboard.metrics.global_total_posts'),
                    'value' => $totalPosts,
                ],
                [
                    'key'   => 'total_users',
                    'label' => __('dashboard.metrics.total_users'),
                    'value' => $totalUsers,
                ],
                [
                    'key'   => 'posts_today',
                    'label' => __('dashboard.metrics.posts_today'),
                    'value' => $postsToday,
                ],
                [
                    'key'   => 'posts_week',
                    'label' => __('dashboard.metrics.posts_week'),
                    'value' => $postsWeek,
                ],
                [
                    'key'   => 'with_image_ratio',
                    'label' => __('dashboard.metrics.with_image_ratio'),
                    'value' => $withImageRatio . '%',
                ],
            ]);

            // Moderation queue: posts with pending status
            $moderationQueue = Post::query()
                ->with('user:id,name')
                ->where('moderation_status', \App\Enums\ModerationStatus::Pending)
                ->latest()
                ->limit(10)
                ->get(['id', 'title', 'slug', 'created_at', 'user_id', 'moderation_status']);

            // 14-day time series
            [$trend14Days, $growthPercentage] = Cache::remember('dashboard.trend_14_days', 60, function () {
                $days   = collect(range(13, 0));
                $counts = $days->map(function ($subtract) {
                    $date  = today()->subDays($subtract);
                    $count = Post::query()->whereDate('created_at', $date)->count();

                    return ['date' => $date->toDateString(), 'count' => $count];
                });
                // Growth: compare sum of last 7 vs previous 7 days
                $last7  = $counts->slice(7)->sum('count');
                $prev7  = $counts->slice(0, 7)->sum('count');
                $growth = $prev7 === 0 ? ($last7 > 0 ? 100 : 0) : round((($last7 - $prev7) / $prev7) * 100, 1);

                return [$counts, $growth];
            });

            // Top categorias
            $topCategories = Cache::remember('dashboard.top_categories', 60, function () {
                return Post::query()
                    ->selectRaw('category_id, COUNT(*) as total')
                    ->groupBy('category_id')
                    ->orderByDesc('total')
                    ->with('category:id,name')
                    ->limit(5)
                    ->get();
            });
            $topCategoriesTotal = $topCategories->sum('total');
        }

        return view('dashboard', [
            'user'               => $user,
            'metrics'            => $metrics,
            'recentPosts'        => $recentPosts,
            'latestUserPost'     => $latestUserPost,
            'moderationQueue'    => $moderationQueue,
            'trend14Days'        => $trend14Days,
            'growthPercentage'   => $growthPercentage,
            'topCategories'      => $topCategories,
            'topCategoriesTotal' => $topCategories->sum('total'),
        ]);
    }
}
