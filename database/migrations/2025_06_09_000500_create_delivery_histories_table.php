<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries');
            $table->foreignId('delivery_stop_id')->nullable()->constrained('delivery_stops');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('truck_id')->nullable()->constrained('trucks');
            $table->json('carroceria_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_histories');
    }
};
