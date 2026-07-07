<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->longText('comment')->change();
        });

        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->longText('caption')->nullable()->change();
            $table->longText('media_url')->nullable()->change();
            $table->longText('thumbnail_url')->nullable()->change();
            $table->text('permalink')->nullable()->change();
        });

        Schema::table('instagram_comments', function (Blueprint $table) {
            $table->longText('comment')->change();
        });

        if (! Schema::hasColumn('tweets', 'tweet')) {
            Schema::table('tweets', function (Blueprint $table) {
                $table->longText('tweet')->nullable()->after('text');
            });
        }

        Schema::table('tweets', function (Blueprint $table) {
            $table->longText('text')->nullable()->change();
            $table->longText('tweet')->nullable()->change();
            $table->text('permalink')->nullable()->change();
        });

        Schema::table('tweet_comments', function (Blueprint $table) {
            $table->longText('comment')->change();
            $table->longText('reply')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('comment')->change();
        });

        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->text('caption')->nullable()->change();
            $table->string('media_url')->nullable()->change();
            $table->string('thumbnail_url')->nullable()->change();
            $table->string('permalink')->nullable()->change();
        });

        Schema::table('instagram_comments', function (Blueprint $table) {
            $table->text('comment')->change();
        });

        Schema::table('tweets', function (Blueprint $table) {
            $table->text('text')->nullable()->change();
            $table->text('tweet')->nullable()->change();
            $table->string('permalink')->nullable()->change();
        });

        Schema::table('tweet_comments', function (Blueprint $table) {
            $table->text('comment')->change();
            $table->text('reply')->nullable()->change();
        });
    }
};
