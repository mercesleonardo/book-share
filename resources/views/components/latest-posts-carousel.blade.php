@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Post> $posts */
@endphp

@if($posts->isNotEmpty())
<div
    x-data="latestPostsCarousel({ interval: 6000 })"
    x-init="init()"
    x-cloak
    tabindex="0"
    class="relative w-full overflow-hidden rounded-xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-900 shadow ring-1 ring-black/5 dark:ring-white/5"
    aria-roledescription="carousel"
>
    <!-- Slides wrapper -->
    <ul class="relative flex transition-transform duration-700 ease-in-out" :style="`transform: translateX(-${active * 100}%);`" x-ref="track">
        @foreach($posts as $index => $post)
            <li
                class="w-full flex-shrink-0 px-6 py-10 md:px-10 flex flex-col md:flex-row gap-6 items-start"
                role="group"
                :aria-hidden="active !== {{ $index }}"
                aria-roledescription="slide"
                aria-label="Slide {{ $index + 1 }} de {{ $posts->count() }}"
            >
                <div class="flex-1 space-y-4">
                    <div class="flex items-center gap-3 text-xs font-medium text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1">
                            <svg class="size-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h18M3 12h18M3 20h18" /></svg>
                            {{ $post->category?->name }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        <span>{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                        <a href="{{ route('posts.show', $post) }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded">
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="text-sm md:text-base leading-relaxed text-slate-600 dark:text-slate-300 line-clamp-4 md:line-clamp-5 max-w-3xl">
                        {{ Str::limit(strip_tags($post->description), 280) }}
                    </p>
                    <div class="flex items-center gap-4 pt-2">
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <svg class="size-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 0c-3.87 0-7 1.79-7 4v2h14v-2c0-2.21-3.13-4-7-4Z"/></svg>
                            <span>{{ $post->user->name }}</span>
                        </div>
                        @if($post->user_rating)
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-600 dark:text-amber-400">
                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
                                <span>{{ $post->user_rating }}/5</span>
                            </span>
                        @endif
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded">
                            {{ __('Ler mais') }}
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </div>
                <div class="w-full md:w-1/3 flex items-center justify-center">
                    @if($post->image)
                        <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}" class="max-h-60 w-auto rounded-lg object-cover shadow-md ring-1 ring-black/5 dark:ring-white/10" loading="lazy" />
                    @else
                        <div class="h-48 w-full md:w-56 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-400">
                            <svg class="size-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="m4 15 4-4a2 2 0 0 1 3 0l7 7"/><path d="m14 13 1.5-1.5a2 2 0 0 1 3 0L20 13"/></svg>
                        </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    <!-- Controles -->
    <div class="absolute inset-x-0 bottom-3 flex items-center justify-between px-4 md:px-6">
        <div class="flex gap-2" role="tablist" aria-label="Selecionar slide">
            @foreach($posts as $index => $post)
                <button
                    type="button"
                    class="h-2.5 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    :class="active === {{ $index }} ? 'w-8 bg-indigo-500 dark:bg-indigo-400' : 'w-2.5 bg-slate-300/70 dark:bg-slate-600 hover:bg-slate-400 dark:hover:bg-slate-500'"
                    @click="goTo({{ $index }})"
                    :aria-current="active === {{ $index }}"
                    :aria-label="'Ir para slide {{ $index + 1 }}'"
                ></button>
            @endforeach
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="prev" class="p-2 rounded-full bg-white/70 dark:bg-slate-800/70 backdrop-blur hover:bg-white dark:hover:bg-slate-700 shadow ring-1 ring-black/5 dark:ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Anterior">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6" /></svg>
            </button>
            <button type="button" @click="togglePause" class="p-2 rounded-full bg-white/70 dark:bg-slate-800/70 backdrop-blur hover:bg-white dark:hover:bg-slate-700 shadow ring-1 ring-black/5 dark:ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500" :aria-label="paused ? 'Retomar rotação' : 'Pausar rotação'">
                <template x-if="paused">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3v18M19 3v18" /></svg>
                </template>
                <template x-if="!paused">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3l14 9L5 21V3z" /></svg>
                </template>
            </button>
            <button type="button" @click="next" class="p-2 rounded-full bg-white/70 dark:bg-slate-800/70 backdrop-blur hover:bg-white dark:hover:bg-slate-700 shadow ring-1 ring-black/5 dark:ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Próximo">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
            </button>
        </div>
    </div>

    <!-- Live region para leitores de tela -->
    <span class="sr-only" role="status" aria-live="polite" x-text="`Slide ${active + 1} de ${total}`"></span>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('latestPostsCarousel', ({ interval = 5000 }) => ({
            active: 0,
            total: {{ $posts->count() }},
            timer: null,
            paused: false,
            init() {
                if (this.total > 1) {
                    // respeita preferências do usuário
                    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    if (!reduceMotion) { this.start(); }
                    this.$el.addEventListener('mouseenter', () => { this.paused = true; this.stop(); });
                    this.$el.addEventListener('mouseleave', () => { this.paused = false; this.start(); });
                    this.$el.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowRight') { this.next(); }
                        if (e.key === 'ArrowLeft') { this.prev(); }
                    });
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) { this.stop(); }
                        else if (!this.paused && !reduceMotion) { this.start(); }
                    });
                }
            },
            start() {
                this.stop();
                this.timer = setInterval(() => { if (!this.paused) { this.next(); } }, interval);
            },
            stop() { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
            togglePause() { this.paused = !this.paused; if (this.paused) { this.stop(); } else { this.start(); } },
            next() { this.active = (this.active + 1) % this.total; },
            prev() { this.active = (this.active - 1 + this.total) % this.total; },
            goTo(i) { this.active = i; },
        }));
    });
</script>
@endif
