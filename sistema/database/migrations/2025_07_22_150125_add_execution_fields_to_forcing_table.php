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
            $table->enum('local_execucao', ['supervisorio', 'plc', 'local'])->nullable()->after('data_retirada');
            $table->foreignId('executante_id')->nullable()->constrained('users')->onDelete('set null')->after('local_execucao');
            $table->timestamp('data_execucao')->nullable()->after('executante_id');
            $table->text('observacoes_execucao')->nullable()->after('data_execucao');
            $table->enum('status_execucao', ['pendente', 'executado'])->default('pendente')->after('observacoes_execucao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forcing', function (Blueprint $table) {
            $table->dropConstrainedForeignId('executante_id');
            $table->dropColumn(['local_execucao', 'data_execucao', 'observacoes_execucao', 'status_execucao']);
        });
    }
};
