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
            $table->string('userName')->after('name')->nullable();
            $table->string('authProvider')->after('userName')->nullable();
            $table->string('authId')->after('authProvider')->nullable();
            $table->string('providerToken')->after('authId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('userName');
            $table->dropColumn('authProvider');
            $table->dropColumn('authId');
            $table->dropColumn('providerToken');
        });
    }
};
