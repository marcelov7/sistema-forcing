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
        Schema::table('forcing', function (Blueprint $table) {
            $table->unsignedBigInteger('solicitado_retirada_por')->nullable()->after('observacoes_solicitacao');
            $table->foreign('solicitado_retirada_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropForeign(['solicitado_retirada_por']);
            $table->dropColumn('solicitado_retirada_por');
        });
    }
};
