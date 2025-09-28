<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('stars'); // 1..5
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
            // Para bancos que suportam check; se SQLite em testes ignora
            if (config('database.default') !== 'sqlite') {
                $table->check('stars >= 1 and stars <= 5');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
