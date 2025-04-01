<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->enum('type', ['origin', 'destination']);
            $table->string('name')->nullable(); // Nome do local
            $table->time('schedule')->nullable(); // Horário previsto
            $table->string('cep', 9);
            $table->string('state', 2);
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_addresses');
    }
}; 