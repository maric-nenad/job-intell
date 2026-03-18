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
        Schema::table('applications', function (Blueprint $table) {
            $table->dateTime('interview_time')->nullable()->after('screening_contact');
            $table->string('interview_contact')->nullable()->after('interview_time');
            $table->json('interview_interviewers')->nullable()->after('interview_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interview_time', 'interview_contact', 'interview_interviewers']);
        });
    }
};
