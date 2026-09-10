<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tweet_comments');
        Schema::dropIfExists('tweets');

        if (Schema::hasTable('api_settings')) {
            $columns = [
                'twitter_bearer_token',
                'twitter_api_key',
                'twitter_api_secret',
                'twitter_client_id',
                'twitter_client_secret',
            ];

            $existingColumns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('api_settings', $column)));
            if ($existingColumns !== []) {
                Schema::table('api_settings', function (Blueprint $table) use ($existingColumns) {
                    $table->dropColumn($existingColumns);
                });
            }
        }
    }

    public function down(): void
    {
        // Twitter is intentionally not restored when this migration is rolled back.
    }
};