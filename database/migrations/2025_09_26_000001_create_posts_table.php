<?php

use App\Enums\ModerationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->index()->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('book_author')->index();
            $table->string('slug')->unique();
            $table->text('description');
            // Avaliação do autor (pode ser preenchida depois, então nullable)
            $table->tinyInteger('user_rating')->nullable();
            $table->string('image')->nullable();
            $table->string('moderation_status', 20)->default(ModerationStatus::Pending->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
