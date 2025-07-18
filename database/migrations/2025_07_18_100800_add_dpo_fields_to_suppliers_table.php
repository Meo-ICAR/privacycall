<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('data_protection_officer')->nullable();
            $table->string('dpo_contact_email')->nullable();
            $table->string('dpo_contact_phone')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['data_protection_officer', 'dpo_contact_email', 'dpo_contact_phone']);
        });
    }
};
