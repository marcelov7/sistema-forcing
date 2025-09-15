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
        Schema::create('forcing', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('status', ['forcado', 'retirado'])->default('forcado');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // usuário que criou
            $table->foreignId('liberador_id')->nullable()->constrained('users')->onDelete('set null'); // liberador responsável
            $table->timestamp('data_forcing')->useCurrent();
            $table->timestamp('data_retirada')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forcing');
    }
};
