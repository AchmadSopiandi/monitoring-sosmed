<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->string('media_id')->nullable()->after('id');
            $table->longText('media_url')->nullable()->after('media_type');
            $table->longText('thumbnail_url')->nullable()->after('media_url');
            $table->unsignedInteger('like_count')->default(0)->after('thumbnail_url');
            $table->unsignedInteger('comments_count')->default(0)->after('like_count');
            $table->timestamp('posted_at')->nullable()->after('comments_count');
        });

        Schema::table('instagram_comments', function (Blueprint $table) {
            $table->string('comment_id')->nullable()->after('instagram_comment_id');
            $table->string('sentiment')->default('Netral')->after('comment');
            $table->timestamp('created_time')->nullable()->after('sentiment');
        });

        Schema::table('api_settings', function (Blueprint $table) {
            $table->text('instagram_token')->nullable()->after('value');
            $table->string('instagram_user_id')->nullable()->after('instagram_token');
            $table->string('facebook_page_id')->nullable()->after('instagram_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_token',
                'instagram_user_id',
                'facebook_page_id',
            ]);
        });

        Schema::table('instagram_comments', function (Blueprint $table) {
            $table->dropColumn(['comment_id', 'sentiment', 'created_time']);
        });

        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->dropColumn(['media_id', 'media_url', 'thumbnail_url', 'like_count', 'comments_count', 'posted_at']);
        });
    }
};
