@props(['post'])

<a href="{{ route('posts.show', $post) }}" {{ $attributes->merge(['class' => 'block bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group']) }}>
    <!-- Image -->
    <div class="aspect-[4/3] bg-gray-200 dark:bg-gray-700">
        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}"
                 alt="{{ $post->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        @endif
    </div>

    <!-- Content -->
    <div class="p-4">
        <!-- Category badge -->
        <div class="mb-2">
            <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                {{ $post->category->name }}
            </span>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
            {{ $post->title }}
        </h3>

        <!-- Book Author -->
        @if($post->book_author)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                por <span class="font-medium">{{ $post->book_author }}</span>
            </p>
        @endif

        <!-- Description -->
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-3">
            {{ $post->description }}
        </p>

        <!-- User Rating -->
        @if($post->user_rating)
            <div class="flex items-center mb-3">
                <x-star-rating-display :value="$post->user_rating" />
                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    Avaliação do usuário
                </span>
            </div>
        @endif

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <span>Por {{ $post->user->name }}</span>
            </div>
            <div class="text-xs text-gray-400 dark:text-gray-500">
                {{ $post->created_at->diffForHumans() }}
            </div>
        </div>
    </div>
</a>
