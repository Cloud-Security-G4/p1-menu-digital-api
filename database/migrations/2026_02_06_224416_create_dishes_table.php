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
        Schema::create('dishes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('category_id');
            $table->string('name', 100);
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('offer_price', 10, 2)->nullable();

            $table->string('image_url')->nullable();

            $table->boolean('available')->default(true);
            $table->boolean('featured')->default(false);

            $table->json('tags')->nullable();
            $table->integer('position')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
