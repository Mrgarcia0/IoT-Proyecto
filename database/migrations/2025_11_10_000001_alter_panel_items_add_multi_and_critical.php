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
        Schema::table('panel_items', function (Blueprint $table) {
            $table->json('variables')->nullable()->after('variable_name');
            $table->double('critical_min')->nullable()->after('position');
            $table->double('critical_max')->nullable()->after('critical_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_items', function (Blueprint $table) {
            $table->dropColumn(['variables', 'critical_min', 'critical_max']);
        });
    }
};