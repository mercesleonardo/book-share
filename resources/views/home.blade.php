<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ config('app.name', 'BookShare') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Discover and share great books!') }}
                </p>
            </div>
            @auth
                <x-primary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.dashboard') }}'">
                    Ir para Dashboard
                </x-primary-button>
            @else
                <div class="flex gap-3">
                    <x-secondary-button x-data x-on:click.prevent="window.location.href='{{ route('login') }}'">
                        Entrar
                    </x-secondary-button>
                    <x-primary-button x-data x-on:click.prevent="window.location.href='{{ route('register') }}'">
                        Registrar
                    </x-primary-button>
                </div>
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->count() > 0)
                <!-- Hero Section -->
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Books Shared by the Community') }}
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ __('Explore a diverse collection of books recommended by our users. Find your next read or share your favorite books.') }}
                    </p>
                </div>

                <!-- Posts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($posts as $post)
                        <x-book-card :post="$post" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="mx-auto max-w-md">
                        <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Nenhum livro compartilhado ainda
                        </h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Seja o primeiro a compartilhar
                        </p>
                        @auth
                            <div class="mt-6">
                                <x-primary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.posts.create') }}'">
                                    {{ __('Share Book') }}
                                </x-primary-button>
                            </div>
                        @else
                            <div class="mt-6">
                                <x-primary-button x-data x-on:click.prevent="window.location.href='{{ route('register') }}'">
                                    {{ __('Register to Share') }}
                                </x-primary-button>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
