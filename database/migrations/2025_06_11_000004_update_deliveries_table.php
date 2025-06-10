<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Remove as chaves estrangeiras existentes
            $table->dropForeign(['route_id']);
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['truck_id']);
            
            // Remove as colunas que serão substituídas
            $table->dropColumn(['route_id', 'driver_id', 'truck_id']);
            
            // Adiciona as novas colunas para referência
            $table->unsignedBigInteger('original_route_id')->nullable();
            $table->unsignedBigInteger('original_driver_id')->nullable();
            $table->unsignedBigInteger('original_truck_id')->nullable();
            
            // Adiciona as chaves estrangeiras para referência
            $table->foreign('original_route_id')->references('id')->on('routes');
            $table->foreign('original_driver_id')->references('id')->on('drivers');
            $table->foreign('original_truck_id')->references('id')->on('trucks');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Remove as novas colunas e chaves estrangeiras
            $table->dropForeign(['original_route_id']);
            $table->dropForeign(['original_driver_id']);
            $table->dropForeign(['original_truck_id']);
            $table->dropColumn(['original_route_id', 'original_driver_id', 'original_truck_id']);
            
            // Restaura as colunas originais
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('truck_id');
            
            // Restaura as chaves estrangeiras originais
            $table->foreign('route_id')->references('id')->on('routes');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->foreign('truck_id')->references('id')->on('trucks');
        });
    }
}; 