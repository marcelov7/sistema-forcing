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
            $table->string('situacao_equipamento')->after('descricao')->nullable();
            $table->string('tag')->after('situacao_equipamento')->nullable();
            $table->text('descricao_equipamento')->after('tag')->nullable();
            $table->string('area')->after('descricao_equipamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropColumn([
                'situacao_equipamento',
                'tag',
                'descricao_equipamento',
                'area'
            ]);
        });
    }
};
