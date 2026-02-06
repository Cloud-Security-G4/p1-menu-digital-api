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
        Schema::create('categorias', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('restaurante_id');
            $table->string('nombre', 50);
            $table->text('descripcion')->nullable();
            $table->integer('posicion')->nullable();
            $table->boolean('activa')->default(true);

            $table->timestamps();

            $table->foreign('restaurante_id')
                ->references('id')
                ->on('restaurantes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
