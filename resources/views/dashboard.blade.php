<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10 space-y-10" x-data="{}">
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('moderationModal', () => ({
                    action: null,
                    postTitle: '',
                    url: '',
                    openModal(action, title, url) {
                        this.action = action;
                        this.postTitle = title;
                        this.url = url;
                        this.$dispatch('open-modal', 'moderate-post');
                    }
                }));
            });
        </script>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('dashboard.welcome', ['name' => $user->name]) }}</h1>
                    @if($latestUserPost)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('dashboard.sections.latest_post') }}:
                            <a href="{{ route('admin.posts.show', $latestUserPost) }}" class="underline">{{ $latestUserPost->title }}</a>
                        </p>
                    @endif
                </div>
                <x-primary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.posts.create') }}'">
                    {{ __('dashboard.actions.create_post') }}
                </x-primary-button>
            </div>
        </div>

        @if($user->isAdmin() || $user->isModerator())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">{{ __('dashboard.sections.trend_14_days') }}</h3>
                            @if(!is_null($growthPercentage))
                                <span class="text-sm {{ $growthPercentage >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $growthPercentage >= 0 ? '+' : '' }}{{ $growthPercentage }}%
                                </span>
                            @endif
                        </div>
                        <div class="h-20">
                            <x-sparkline :data="$trend14Days->pluck('count')->toArray()" />
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
                            @foreach($trend14Days as $point)
                                <div class="flex items-center gap-1">
                                    <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($point['date'])->format('d/m') }}:</span>
                                    <span>{{ $point['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <x-top-categories :rows="$topCategories" :total="$topCategoriesTotal" />
                </div>
            </div>
        @endif

        @if(($user->isAdmin() || $user->isModerator()) && $moderationQueue->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded shadow mt-10">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">{{ __('dashboard.sections.moderation_queue') }}</h3>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700" x-data="moderationModal()">
                            @foreach($moderationQueue as $post)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                            <span>{{ $post->title }}</span>
                                            <x-status-badge :status="$post->moderation_status?->value" :show-approved="true" />
                                        </div>
                                        <div class="text-xs text-gray-500 flex gap-2">
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                            <span>•</span>
                                            <span>{{ $post->user?->name }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <x-secondary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.posts.show', $post) }}'">{{ __('dashboard.actions.view') }}</x-secondary-button>
                                        @can('update', $post)
                                            @php
                                                $approveUrl = route('admin.posts.approve', $post);
                                                $rejectUrl = route('admin.posts.reject', $post);
                                                $postTitleJs = json_encode($post->title);
                                            @endphp

                                            <x-secondary-button
                                                x-data
                                                x-on:click="openModal('approve', {{ $postTitleJs }}, '{{ $approveUrl }}')"
                                            >{{ __('dashboard.moderation.approve') }}</x-secondary-button>

                                            <x-secondary-button
                                                x-data
                                                class="!text-red-600"
                                                x-on:click="openModal('reject', {{ $postTitleJs }}, '{{ $rejectUrl }}')"
                                            >{{ __('dashboard.moderation.reject') }}</x-secondary-button>
                                        @endcan
                                    </div>
                                </li>
                            @endforeach
                            <!-- Single Dynamic Modal -->
                            <x-modal name="moderate-post" :show="false">
                                <div class="p-6" x-data="{ submitting: false }" @submit.prevent="if(submitting) return; submitting=true; $refs.moderationForm.submit()">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4" x-text="action === 'approve' ? '{{ __('dashboard.moderation.confirm_approve') }}' : '{{ __('dashboard.moderation.confirm_reject') }}'"></h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6" x-text="postTitle"></p>
                                    <div class="flex justify-end gap-3">
                                        <x-secondary-button x-data x-on:click="$dispatch('close-modal', 'moderate-post')" x-bind:disabled="submitting">{{ __('dashboard.actions.cancel') }}</x-secondary-button>
                                        <form method="POST" x-ref="moderationForm" x-bind:action="url" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <template x-if="action === 'approve'">
                                                <x-primary-button type="submit" x-bind:disabled="submitting">
                                                    <span x-show="!submitting">{{ __('dashboard.moderation.approve') }}</span>
                                                    <span x-show="submitting">{{ __('dashboard.actions.processing') }}</span>
                                                </x-primary-button>
                                            </template>
                                            <template x-if="action === 'reject'">
                                                <x-danger-button type="submit" x-bind:disabled="submitting">
                                                    <span x-show="!submitting">{{ __('dashboard.moderation.reject') }}</span>
                                                    <span x-show="submitting">{{ __('dashboard.actions.processing') }}</span>
                                                </x-danger-button>
                                            </template>
                                        </form>
                                    </div>
                                </div>
                            </x-modal>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($metrics as $metric)
                    <x-metric-card :label="$metric['label']" :value="$metric['value']" />
                @endforeach
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">{{ __('dashboard.sections.recent_posts') }}</h3>
                        <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">{{ __('dashboard.actions.view_all') }}</a>
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentPosts as $post)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <span>{{ $post->title }}</span>
                                        @if($post->moderation_status)
                                            <x-status-badge :status="$post->moderation_status->value" :show-approved="true" />
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <x-secondary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.posts.show', $post) }}'">{{ __('dashboard.actions.view') }}</x-secondary-button>
                                    @can('update', $post)
                                        <x-secondary-button x-data x-on:click.prevent="window.location.href='{{ route('admin.posts.edit', $post) }}'">{{ __('dashboard.actions.edit') }}</x-secondary-button>
                                    @endcan
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm text-gray-500">{{ __('dashboard.sections.no_posts') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
