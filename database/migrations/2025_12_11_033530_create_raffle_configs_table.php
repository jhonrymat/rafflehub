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
        Schema::create('raffle_configs', function (Blueprint $table) {
            $table->id();
            $table->string('prize_name'); // Ej: "Moto Honda 2024"
            $table->text('prize_description')->nullable();
            $table->string('prize_image')->nullable();
            $table->decimal('ticket_price', 10, 2)->default(50000); // $50,000 COP
            $table->date('raffle_date'); // Fecha del sorteo
            $table->date('sale_start_date')->nullable();
            $table->date('sale_end_date')->nullable();
            $table->string('lottery_method')->nullable(); // Ej: "Lotería de Bogotá"
            $table->string('winning_number', 2)->nullable(); // Número ganador
            $table->enum('status', ['active', 'finished', 'cancelled'])->default('active');
            $table->text('terms_and_conditions')->nullable();
            $table->string('contact_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_configs');
    }
};
