<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->cascadeOnDelete();

            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();

            $table->boolean('concluido')->default(false);
            $table->timestamp('concluido_em')->nullable();

            $table->timestamps();
            $table->softDeletes();


            $table->index(['categoria_id', 'concluido']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
