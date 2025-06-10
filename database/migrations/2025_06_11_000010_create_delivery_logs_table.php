<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action_type'); // created, updated, completed, cancelled, etc
            $table->string('entity_type'); // delivery, stop, photo, etc
            $table->unsignedBigInteger('entity_id')->nullable(); // ID da entidade afetada
            $table->json('old_data')->nullable(); // Dados antigos
            $table->json('new_data')->nullable(); // Dados novos
            $table->text('description')->nullable(); // Descrição da ação
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_logs');
    }
}; 