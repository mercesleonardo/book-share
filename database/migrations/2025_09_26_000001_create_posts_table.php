<?php

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
            $table->string('author');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
