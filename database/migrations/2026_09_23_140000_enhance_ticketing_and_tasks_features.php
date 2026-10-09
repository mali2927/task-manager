<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'task_id')) {
                $table->foreignId('task_id')->nullable()->after('project_id')->constrained('tasks')->nullOnDelete();
            }
            if (!Schema::hasColumn('tickets', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('resolution_summary'); // 1 to 5 stars
            }
            if (!Schema::hasColumn('tickets', 'rating_feedback')) {
                $table->text('rating_feedback')->nullable()->after('rating');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'ticket_id')) {
                $table->foreignId('ticket_id')->nullable()->after('parent_id')->constrained('tickets')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'ticket_id')) {
                $table->dropForeign(['ticket_id']);
                $table->dropColumn('ticket_id');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            }
            if (Schema::hasColumn('tickets', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('tickets', 'rating_feedback')) {
                $table->dropColumn('rating_feedback');
            }
        });
    }
};
