<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->enum('type', ['management', 'operational']); // Adicionando o campo type
            $table->timestamps();
        });

        Schema::create('permission_group_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['permission_group_id', 'permission_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_group_has_permissions');
        Schema::dropIfExists('permissions');
    }
};
