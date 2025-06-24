<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MariaDB/MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'manager', 'employee', 'customer') DEFAULT 'employee'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without superadmin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'employee', 'customer') DEFAULT 'employee'");
    }
};
