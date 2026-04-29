<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // Speed up user-scoped queries (most common)
            $table->index('user_id');
            // Speed up status filtering
            $table->index('status');
            // Speed up sorting by date
            $table->index('created_at');
            // Composite index for the most common admin query: status + created_at
            $table->index(['status', 'created_at']);
            // Composite index for user + status filter
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
        });
    }
};
