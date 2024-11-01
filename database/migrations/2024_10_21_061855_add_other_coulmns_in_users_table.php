<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->after('email_verified_at', function (Blueprint $table) {
                $table->char('phone', 40)->nullable();
                $table->char('phone_country_code', 5)->nullable();
                $table->timestamp('phone_verified_at')->nullable();
                $table->char('country_code', 3)->nullable();
            });
            $table->unique(['phone_country_code', 'phone']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone_country_code', 'phone']);
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('phone');
            $table->dropColumn('phone_country_code');
            $table->dropColumn('country_code');
            $table->dropSoftDeletes();
        });
    }
};
