<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carroceria_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries');
            $table->foreignId('carroceria_id')->constrained('carrocerias');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carroceria_delivery');
    }
};
