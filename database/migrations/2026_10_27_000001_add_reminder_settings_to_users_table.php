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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('reminders_enabled')->default(true)->after('password');
            $table->string('reminder_time', 5)->default('18:00')->after('reminders_enabled');
            $table->date('last_reminder_on')->nullable()->after('reminder_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reminders_enabled', 'reminder_time', 'last_reminder_on']);
        });
    }
};
