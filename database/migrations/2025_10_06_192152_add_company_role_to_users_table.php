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
            $table->string('role', 255)->default('company')->after('email');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('role');
            
            // Add index for better performance
            $table->index('role');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['company_id']);
            $table->dropColumn(['role', 'company_id']);
        });
    }
};
