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
        Schema::table('terms_acceptances', function (Blueprint $table) {
            $table->string('procedure_version')->default('SMC-MAN-PR-014 V.4')->after('user_agent');
            $table->text('procedure_summary')->nullable()->after('procedure_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_acceptances', function (Blueprint $table) {
            $table->dropColumn(['procedure_version', 'procedure_summary']);
        });
    }
};
