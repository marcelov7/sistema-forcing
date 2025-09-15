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
        // Esta migration é específica para ambientes SQLite
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('forcing', function (Blueprint $table) {
                // Para SQLite, verificar se as colunas existem antes de adicionar
                $columns = Schema::getColumnListing('forcing');
                
                if (!in_array('data_liberacao', $columns)) {
                    $table->timestamp('data_liberacao')->nullable();
                }
                
                if (!in_array('liberado_por', $columns)) {
                    $table->unsignedBigInteger('liberado_por')->nullable();
                }
                
                if (!in_array('observacoes_liberacao', $columns)) {
                    $table->text('observacoes_liberacao')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('forcing', function (Blueprint $table) {
                $columns = Schema::getColumnListing('forcing');
                
                if (in_array('observacoes_liberacao', $columns)) {
                    $table->dropColumn('observacoes_liberacao');
                }
                if (in_array('liberado_por', $columns)) {
                    $table->dropColumn('liberado_por');
                }
                if (in_array('data_liberacao', $columns)) {
                    $table->dropColumn('data_liberacao');
                }
            });
        }
    }
};