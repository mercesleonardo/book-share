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
                        <img
                            src="{{ asset('storage/' . $post->image) }}"
                            alt="{{ $post->title }}"
                            loading="lazy"
                            class="w-full h-auto object-cover max-h-[480px]"
                            sizes="(max-width: 768px) 100vw, 800px"
                        >
                    </figure>
                @endif

                <article class="prose dark:prose-invert max-w-none leading-relaxed text-gray-800 dark:text-gray-200">
                    {!! \App\Support\Markdown::toHtml($post->description) !!}
                </article>

                <!-- Prev / Next navigation -->
                <nav class="flex items-center justify-between mt-10 text-sm" aria-label="Post navigation">
                    <div>
                        @if($previous)
                            <a href="{{ route('posts.show', $previous) }}" class="group inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                <span class="text-xs" aria-hidden="true">&larr;</span>
                                {{ __('posts.navigation.previous') }}
                            </a>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($next)
                            <a href="{{ route('posts.show', $next) }}" class="group inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('posts.navigation.next') }}
                                <span class="text-xs" aria-hidden="true">&rarr;</span>
                            </a>
                        @endif
                    </div>
                </nav>

                <!-- Related posts -->
                <section class="mt-12">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide uppercase mb-4">{{ __('posts.related.title') }}</h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($related as $r)
                            <a href="{{ route('posts.show', $r) }}" class="block p-4 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 transition group">
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-100 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $r->title }}</div>
                            </a>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('posts.related.none') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <!-- Sidebar meta -->
            <aside class="w-full md:w-64 md:shrink-0 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
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

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 shadow-sm space-y-4">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide uppercase">{{ __('posts.meta.ratings') }}</h2>
                    <div class="text-xs flex flex-col gap-2">
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.author_rating') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 flex items-center gap-1">
                                {{ $post->user_rating }}/5
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/></svg>
                            </span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('posts.meta.community_average') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 flex items-center gap-1">
                                @php($avg = $post->community_average_rating)
                                {{ $avg !== null ? number_format($avg, 1, ',', '.') : '—' }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor"><path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"/></svg>
                            </span>
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
                                    <div class="flex flex-row-reverse justify-end gap-1 [&>input]:hidden" x-data="{ value: {{ $userRating?->stars ?? 0 }} }">
                                        @for($i = 5; $i >= 1; $i--)
                                            <label class="cursor-pointer" :class="{'opacity-40': value < {{ $i }}}">
                                                <input type="radio" name="stars" value="{{ $i }}" @checked(($userRating?->stars ?? 0)==$i) x-on:change="value={{ $i }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-yellow-400">
                                                    <path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <x-primary-button class="w-full justify-center">{{ $userRating ? __('posts.meta.update_rating') : __('posts.meta.rate') }}</x-primary-button>
                            </form>
                        @endif
                    @endauth
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
