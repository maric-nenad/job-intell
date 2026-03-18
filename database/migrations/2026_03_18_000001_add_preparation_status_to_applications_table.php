<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        match (DB::getDriverName()) {
            'pgsql' => DB::statement("
                ALTER TABLE applications
                    DROP CONSTRAINT IF EXISTS applications_status_check,
                    ADD CONSTRAINT applications_status_check
                        CHECK (status IN ('saved','preparation','applied','screening','interview','offer','rejected','withdrawn'))
            "),
            'mysql', 'mariadb' => DB::statement("
                ALTER TABLE applications
                    MODIFY COLUMN status ENUM('saved','preparation','applied','screening','interview','offer','rejected','withdrawn') DEFAULT 'saved'
            "),
            default => null,
        };
    }

    public function down(): void
    {
        match (DB::getDriverName()) {
            'pgsql' => DB::statement("
                ALTER TABLE applications
                    DROP CONSTRAINT IF EXISTS applications_status_check,
                    ADD CONSTRAINT applications_status_check
                        CHECK (status IN ('saved','applied','screening','interview','offer','rejected','withdrawn'))
            "),
            'mysql', 'mariadb' => DB::statement("
                ALTER TABLE applications
                    MODIFY COLUMN status ENUM('saved','applied','screening','interview','offer','rejected','withdrawn') DEFAULT 'saved'
            "),
            default => null,
        };
    }
};
