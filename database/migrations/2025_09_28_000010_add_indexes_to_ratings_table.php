<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table): void {
            $table->index('post_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table): void {
            $table->dropIndex(['post_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
