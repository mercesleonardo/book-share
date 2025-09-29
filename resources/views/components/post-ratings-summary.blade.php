@props(['post'])
<div {{ $attributes->class('bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-sm space-y-4') }}>
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide uppercase">{{ __('posts.meta.ratings') }}</h2>
    <div class="text-xs flex flex-col gap-2">
        <div class="flex justify-between gap-3 items-center">
            <span class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.author_rating') }}</span>
            <x-star-rating-display :value="$post->user_rating" :precision="1" />
        </div>
        <div class="flex justify-between gap-3 items-center">
            <span class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.community_average') }}</span>
            @php($avg = $post->community_average_rating)
            <div>
                @if($avg !== null)
                    <x-star-rating-display :value="$avg" :precision="1" />
                @else
                    <span class="text-xs text-gray-400 dark:text-gray-600">—</span>
                @endif
            </div>
        </div>
        <div class="flex justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.community_count') }}</span>
            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $post->community_ratings_count }}</span>
        </div>
    </div>
    @auth
        @if(auth()->id() !== $post->user_id)
            @php($userRating = $post->ratings->firstWhere('user_id', auth()->id()))
            <form method="POST" action="{{ route('posts.ratings.store', $post) }}" class="space-y-3">
                @csrf
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('posts.meta.your_rating') }}</span>
                    <x-star-rating name="stars" :value="$userRating?->stars" :half="false" />
                </div>
                <x-primary-button class="w-full justify-center">{{ $userRating ? __('posts.meta.update_rating') : __('posts.meta.rate') }}</x-primary-button>
            </form>
        @endif
    @endauth
</div>
