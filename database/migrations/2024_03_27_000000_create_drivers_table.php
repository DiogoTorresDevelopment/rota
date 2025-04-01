<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('phone');
            $table->string('email');
            $table->boolean('status')->default(true);
            
            // Endereço
            $table->string('cep');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('number');
            $table->string('district'); // bairro
            $table->timestamps();
        });

        // Tabela para documentos
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->string('type'); // CNH, Nota Fiscal, etc
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('drivers');
    }
}; 