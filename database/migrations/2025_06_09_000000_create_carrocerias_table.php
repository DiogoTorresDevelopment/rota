<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carrocerias', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('chassi')->unique();
            $table->string('placa', 8)->unique();
            $table->decimal('peso_suportado', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carrocerias');
    }
};
