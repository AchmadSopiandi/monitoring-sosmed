<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('instagram_media_id')->unique();
            $table->longText('caption')->nullable();
            $table->text('permalink')->nullable();
            $table->string('media_type')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sentiments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('color', 24)->nullable();
            $table->timestamps();
        });

        Schema::create('instagram_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instagram_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sentiment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('instagram_comment_id')->unique();
            $table->string('username');
            $table->longText('comment');
            $table->unsignedInteger('like_count')->nullable();
            $table->timestamp('commented_at')->nullable();
            $table->timestamps();

            $table->index(['commented_at', 'sentiment_id']);
            $table->index('username');
        });

        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->string('tweet_id')->unique();
            $table->longText('text')->nullable();
            $table->longText('tweet')->nullable();
            $table->string('author_username')->nullable();
            $table->text('permalink')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tweet_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tweet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sentiment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tweet_reply_id')->unique();
            $table->string('username')->nullable();
            $table->longText('comment');
            $table->unsignedInteger('like_count')->nullable();
            $table->timestamp('commented_at')->nullable();
            $table->timestamps();

            $table->index(['commented_at', 'sentiment_id']);
            $table->index('username');
        });

        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('key');
            $table->text('value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->unique(['provider', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_settings');
        Schema::dropIfExists('tweet_comments');
        Schema::dropIfExists('tweets');
        Schema::dropIfExists('instagram_comments');
        Schema::dropIfExists('sentiments');
        Schema::dropIfExists('instagram_posts');
    }
};
