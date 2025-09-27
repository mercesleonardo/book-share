<?php

use App\Enums\ModerationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('moderation_status', 20)->default(ModerationStatus::Pending->value)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn('moderation_status');
        });
    }
};
