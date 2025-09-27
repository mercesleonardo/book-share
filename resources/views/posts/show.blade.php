<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $post->title }}</h2>
    </x-slot>
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('posts.index') }}" class="text-sm text-blue-600 hover:underline">&larr; {{ __('posts.actions.back') }}</a>
        </div>
        <h1 class="text-2xl font-bold mb-2">{{ $post->title }}</h1>
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex flex-wrap gap-4">
            <span>{{ __('posts.fields.author') }}: {{ $post->author }}</span>
            @if($post->category)
                <span>{{ __('posts.fields.category') }}: {{ $post->category->name }}</span>
            @endif
        </div>
        @if($post->image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="rounded shadow max-h-96">
            </div>
        @endif
        <div class="prose dark:prose-invert max-w-none">
            {!! nl2br(e($post->description)) !!}
        </div>
    </div>
</x-app-layout>
