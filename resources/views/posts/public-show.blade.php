<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <!-- Breadcrumbs -->
            <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-gray-700 dark:hover:text-gray-200">Home</a>
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[180px]" title="{{ $post->title }}">{{ $post->title }}</span>
            </nav>
            <div class="flex flex-wrap items-start gap-3">
                <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <span>{{ $post->title }}</span>
                    @if($post->category)
                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">{{ $post->category->name }}</span>
                    @endif
                </h1>
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

                <!-- Book author info -->
                @if($post->book_author)
                    <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50 dark:bg-blue-900/20">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Autor do livro</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $post->book_author }}</p>
                    </div>
                @endif

                <article class="prose dark:prose-invert max-w-none leading-relaxed text-gray-800 dark:text-gray-200">
                    {!! nl2br(e($post->description)) !!}
                </article>

                <!-- Prev / Next navigation -->
                <nav class="flex items-center justify-between mt-10 text-sm" aria-label="Post navigation">
                    <div>
                        @if($previous)
                            <a href="{{ route('posts.show', $previous) }}" class="group inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                <span class="text-xs" aria-hidden="true">&larr;</span>
                                Anterior
                            </a>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($next)
                            <a href="{{ route('posts.show', $next) }}" class="group inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                Próximo
                                <span class="text-xs" aria-hidden="true">&rarr;</span>
                            </a>
                        @endif
                    </div>
                </nav>

                <!-- Related posts -->
                @if($related->count() > 0)
                    <section class="mt-12">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-wide uppercase mb-4">Posts Relacionados</h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($related as $relatedPost)
                                <a href="{{ route('posts.show', $relatedPost) }}" class="block p-4 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 transition group">
                                    @if($relatedPost->image)
                                        <img src="{{ asset('storage/' . $relatedPost->image) }}" alt="{{ $relatedPost->title }}" class="w-full h-32 object-cover rounded mb-3">
                                    @endif
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $relatedPost->title }}</div>
                                    @if($relatedPost->book_author)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $relatedPost->book_author }}</div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Comments -->
                <x-post-comments :post="$post" />
            </div>

            <!-- Sidebar meta -->
            <aside class="w-full md:w-64 md:shrink-0 space-y-6">
                <x-post-info :post="$post" />
                <x-post-ratings-summary :post="$post" />

                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">&larr; Voltar para Home</a>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
