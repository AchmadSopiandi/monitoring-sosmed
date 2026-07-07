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

        Schema::table('tweets', function (Blueprint $table) {
            $table->string('author')->nullable()->after('author_username');
            $table->unsignedInteger('reply_count')->default(0)->after('author');
            $table->unsignedInteger('like_count')->default(0)->after('reply_count');
            $table->unsignedInteger('repost_count')->default(0)->after('like_count');
            $table->timestamp('posted_at')->nullable()->after('repost_count');
        });

        Schema::table('tweet_comments', function (Blueprint $table) {
            $table->string('reply_id')->nullable()->after('tweet_reply_id');
            $table->longText('reply')->nullable()->after('comment');
            $table->string('sentiment')->default('Netral')->after('reply');
            $table->timestamp('created_time')->nullable()->after('sentiment');
        });

        Schema::table('api_settings', function (Blueprint $table) {
            $table->text('instagram_token')->nullable()->after('value');
            $table->string('instagram_user_id')->nullable()->after('instagram_token');
            $table->string('facebook_page_id')->nullable()->after('instagram_user_id');
            $table->text('twitter_bearer_token')->nullable()->after('facebook_page_id');
            $table->string('twitter_api_key')->nullable()->after('twitter_bearer_token');
            $table->text('twitter_api_secret')->nullable()->after('twitter_api_key');
            $table->string('twitter_client_id')->nullable()->after('twitter_api_secret');
            $table->text('twitter_client_secret')->nullable()->after('twitter_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_token',
                'instagram_user_id',
                'facebook_page_id',
                'twitter_bearer_token',
                'twitter_api_key',
                'twitter_api_secret',
                'twitter_client_id',
                'twitter_client_secret',
            ]);
        });

        Schema::table('tweet_comments', function (Blueprint $table) {
            $table->dropColumn(['reply_id', 'reply', 'sentiment', 'created_time']);
        });

        Schema::table('tweets', function (Blueprint $table) {
            $table->dropColumn(['author', 'reply_count', 'like_count', 'repost_count', 'posted_at']);
        });

        Schema::table('instagram_comments', function (Blueprint $table) {
            $table->dropColumn(['comment_id', 'sentiment', 'created_time']);
        });

        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->dropColumn(['media_id', 'media_url', 'thumbnail_url', 'like_count', 'comments_count', 'posted_at']);
        });
    }
};
