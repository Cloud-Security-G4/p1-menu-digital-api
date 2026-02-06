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
        Schema::create('platos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('categoria_id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();

            $table->decimal('precio', 10, 2);
            $table->decimal('precio_oferta', 10, 2)->nullable();

            $table->string('imagen_url')->nullable();

            $table->boolean('disponible')->default(true);
            $table->boolean('destacado')->default(false);

            $table->json('etiquetas')->nullable();
            $table->integer('posicion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};
