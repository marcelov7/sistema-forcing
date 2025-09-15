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
            // Atualizar enum para incluir status "liberado"
            $table->dropColumn('status');
        });
        
        Schema::table('forcing', function (Blueprint $table) {
            $table->enum('status', ['pendente', 'liberado', 'forcado'])->default('pendente')->after('descricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('forcing', function (Blueprint $table) {
            $table->enum('status', ['forcado', 'retirado'])->default('forcado')->after('descricao');
        });
    }
};
