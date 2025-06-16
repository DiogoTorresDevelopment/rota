<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_carrocerias', function (Blueprint $table) {
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('carroceria_id')->constrained()->onDelete('cascade');
            $table->string('descricao');
            $table->string('chassi')->nullable();
            $table->string('placa');
            $table->decimal('peso_suportado', 10, 2);
        });
    }

    public function down()
    {
        Schema::table('delivery_carrocerias', function (Blueprint $table) {
            $table->dropForeign(['delivery_id']);
            $table->dropForeign(['carroceria_id']);
            $table->dropColumn([
                'delivery_id',
                'carroceria_id',
                'descricao',
                'chassi',
                'placa',
                'peso_suportado'
            ]);
        });
    }
};
