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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->decimal('amount', 19);
            $table->unsignedBigInteger('author_id');
            $table->timestamps();
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->index('author_id');

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
