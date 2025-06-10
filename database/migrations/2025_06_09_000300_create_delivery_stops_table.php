<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries');
            $table->foreignId('route_stop_id')->constrained('route_stops');
            $table->integer('order');
            $table->enum('status', ['pending','completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_stops');
    }
};
