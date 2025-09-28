@props(['post'])
<div {{ $attributes->class('bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-sm') }}>
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 tracking-wide uppercase">{{ __('posts.meta.info') }}</h2>
    <dl class="text-xs space-y-3">
        <div class="flex justify-between gap-3">
            <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.fields.author') }}</dt>
            <dd class="text-gray-800 dark:text-gray-200 font-medium">{{ $post->book_author }}</dd>
        </div>
        @if($post->category)
            <div class="flex justify-between gap-3">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.fields.category') }}</dt>
                <dd class="text-gray-800 dark:text-gray-200 font-medium">{{ $post->category->name }}</dd>
            </div>
        @endif
        <div class="flex justify-between gap-3">
            <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.created') }}</dt>
            <dd class="text-gray-800 dark:text-gray-200 font-medium" title="{{ $post->created_at }}">{{ $post->created_at->format('d/m/Y') }}</dd>
        </div>
        <div class="flex justify-between gap-3">
            <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.updated') }}</dt>
            <dd class="text-gray-800 dark:text-gray-200 font-medium" title="{{ $post->updated_at }}">{{ $post->updated_at->diffForHumans() }}</dd>
        </div>
        @if($post->moderation_status)
            <div class="flex justify-between gap-3">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.status') }}</dt>
                <dd class="text-gray-800 dark:text-gray-200 font-medium">{{ $post->moderation_status->label() }}</dd>
            </div>
        @endif
    </dl>
</div>