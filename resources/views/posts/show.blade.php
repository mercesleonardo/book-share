<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <!-- Breadcrumbs -->
            <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ __('Dashboard') }}</a>
                <span>/</span>
                <a href="{{ route('posts.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ __('posts.title') }}</a>
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[180px]" title="{{ $post->title }}">{{ $post->title }}</span>
            </nav>
            <div class="flex flex-wrap items-start gap-3">
                <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <span>{{ $post->title }}</span>
                    @if($post->moderation_status)
                        <x-status-badge :status="$post->moderation_status->value" :show-approved="true" />
                    @endif
                </h1>
                @if($post->category)
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">{{ $post->category->name }}</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="flex flex-col md:flex-row md:items-start gap-8">
            <!-- Main content -->
            <div class="flex-1 min-w-0 space-y-6">
                @if($post->image)
                    <figure class="w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 shadow">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-auto object-cover max-h-[480px]">
                    </figure>
                @endif

                <article class="prose dark:prose-invert max-w-none leading-relaxed">
                    {!! nl2br(e($post->description)) !!}
                </article>
            </div>

            <!-- Sidebar meta -->
            <aside class="w-full md:w-64 md:shrink-0 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 tracking-wide uppercase">{{ __('posts.meta.info') }}</h2>
                    <dl class="text-xs space-y-3">
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('posts.fields.author') }}</dt>
                            <dd class="text-gray-800 dark:text-gray-200 font-medium">{{ $post->author }}</dd>
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

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">&larr; {{ __('posts.actions.back') }}</a>
                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ __('posts.actions.edit') }}</a>
                    @endcan
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
