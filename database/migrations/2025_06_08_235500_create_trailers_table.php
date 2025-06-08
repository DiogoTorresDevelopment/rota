<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('ano');
            $table->string('cor');
            $table->decimal('carga_suportada', 10, 2);
            $table->string('chassi')->unique();
            $table->string('placa')->unique();
            $table->decimal('quilometragem', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trailers');
    }
};
