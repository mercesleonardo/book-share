@props(['post'])

@php
    // For now we just take all loaded comments (already eager-loaded limited by controller if needed)
    $comments = $post->comments->take(30); // soft cap
@endphp

<section class="mt-14" aria-labelledby="comments-title">
    <h2 id="comments-title" class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide uppercase flex items-center gap-2">
        {{ __('posts.meta.comments') }}
        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ $post->comments->count() }})</span>
    </h2>

    <div class="mt-4 space-y-6">
        @auth
            <form method="POST" action="{{ route('comments.store') }}" class="space-y-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <div>
                    <label for="comment-content" class="sr-only">{{ __('comments.form.placeholder') }}</label>
                    <textarea
                        id="comment-content"
                        name="content"
                        required
                        maxlength="1000"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900/40 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500"
                        rows="3"
                        placeholder="{{ __('comments.form.placeholder') }}">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <x-primary-button>{{ __('comments.form.submit') }}</x-primary-button>
                </div>
            </form>
        @endauth

        <ul class="space-y-4" role="list">
            @forelse($comments as $comment)
                <li class="group flex gap-3 items-start">
                    <div class="flex-1 min-w-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                <span aria-hidden="true">•</span>
                                <time datetime="{{ $comment->created_at }}" title="{{ $comment->created_at }}">{{ $comment->created_at->diffForHumans() }}</time>
                            </div>
                            @auth
                                @if(auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('{{ __('comments.form.delete_confirm') }}');" class="ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline opacity-70 group-hover:opacity-100 transition">{{ __('comments.form.delete') }}</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                            {{ $comment->content }}
                        </div>
                    </div>
                </li>
            @empty
                <li class="text-xs text-gray-500 dark:text-gray-400">{{ __('comments.none') }}</li>
            @endforelse
        </ul>
    </div>
</section>
