<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->string('company_valuation')->nullable()->after('company_rating_source');
            $table->string('company_employees')->nullable()->after('company_valuation');
            $table->text('company_owners')->nullable()->after('company_employees');
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn(['company_valuation', 'company_employees', 'company_owners']);
        });
    }
};
