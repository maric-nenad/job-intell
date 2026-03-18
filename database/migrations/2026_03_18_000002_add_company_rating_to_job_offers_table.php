<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->decimal('company_rating', 3, 1)->nullable()->after('notes');
            $table->string('company_rating_source')->nullable()->after('company_rating');
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn(['company_rating', 'company_rating_source']);
        });
    }
};
