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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->foreignId('status_id')->constrained('task_statuses')->cascadeOnDelete();
            $table->string('priority')->default('normal'); // urgent, high, normal, low
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('recurrence')->nullable(); // daily, weekly, monthly
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('actual_hours', 6, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        Schema::create('task_watchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        Schema::create('task_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('task_comments')->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
        });

        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->default('#6366f1');
            $table->timestamps();
        });

        Schema::create('task_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'tag_id']);
        });

        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('task_list_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('text'); // text, number, dropdown, date
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('task_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['task_id', 'custom_field_id']);
        });

        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('depends_on_task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('type')->default('blocking'); // blocking, blocked_by
            $table->timestamps();
        });

        Schema::create('task_time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamps();
        });

        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, status_updated, priority_updated, assigned, etc.
            $table->text('description');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // assigned, mentioned, due_soon, status_changed, commented
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_time_entries');
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('task_custom_field_values');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('task_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_checklists');
        Schema::dropIfExists('task_watchers');
        Schema::dropIfExists('task_assignees');
        Schema::dropIfExists('tasks');
    }
};
