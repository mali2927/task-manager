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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workspace_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // owner, admin, member, guest
            $table->string('job_title')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id']);
        });

        Schema::create('workspace_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->default('member');
            $table->string('token')->unique();
            $table->string('status')->default('pending'); // pending, accepted, declined, expired
            $table->foreignId('invited_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#6366f1');
            $table->string('icon')->default('users');
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // lead, member
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('folder');
            $table->string('color')->default('#8b5cf6');
            $table->boolean('is_private')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('space_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_override')->nullable(); // admin, member, guest
            $table->timestamps();

            $table->unique(['space_id', 'user_id']);
        });

        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->default('#94a3b8');
            $table->string('type')->default('todo'); // todo, in_progress, review, blocked, done
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#3b82f6');
            $table->string('icon')->default('briefcase');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('task_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#10b981');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_lists');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('space_members');
        Schema::dropIfExists('spaces');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('workspace_invites');
        Schema::dropIfExists('workspace_members');
        Schema::dropIfExists('workspaces');
    }
};
