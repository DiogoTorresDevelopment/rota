<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('cpf');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('status')->default(true);
            $table->string('cep');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('number');
            $table->string('district');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_drivers');
    }
}; 