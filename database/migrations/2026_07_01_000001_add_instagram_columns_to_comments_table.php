<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('instagram_comment_id')->nullable()->unique()->after('id');
            $table->string('instagram_media_id')->nullable()->after('instagram_comment_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropUnique(['instagram_comment_id']);
            $table->dropColumn(['instagram_comment_id', 'instagram_media_id']);
        });
    }
};
