@props(['post'])

@auth
<form method="POST" action="{{ route('admin.comments.store') }}" class="space-y-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
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
