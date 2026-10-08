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
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('department')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('assigned_role')->default('member'); // admin, member, guest
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
