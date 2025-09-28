<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearRatingsCache extends Command
{
    protected $signature = 'ratings:clear {post_id? : Limpar apenas para um post específico}';

    protected $description = 'Limpa chaves de cache relacionadas às médias e contagens de avaliações.';

    public function handle(): int
    {
        $postId = $this->argument('post_id');

        if ($postId) {
            /** @var Post|null $post */
            $post = Post::find($postId);

            if (!$post) {
                $this->error('Post não encontrado.');

                return self::FAILURE;
            }
            Cache::forget('post:ratings:avg:' . $post->id);
            Cache::forget('post:ratings:count:' . $post->id);
            $this->info("Cache de ratings limpo para o post ID {$post->id}.");

            return self::SUCCESS;
        }

        // Limpeza global: iterar posts existentes e esquecer chaves conhecidas.
        Post::query()->select('id')->chunk(500, function ($chunk): void {
            foreach ($chunk as $p) {
                Cache::forget('post:ratings:avg:' . $p->id);
                Cache::forget('post:ratings:count:' . $p->id);
            }
        });

        $this->info('Cache de ratings limpo para todos os posts.');

        return self::SUCCESS;
    }
}
