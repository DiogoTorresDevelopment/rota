<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Adiciona soft deletes nas tabelas principais
        $tables = [
            'users',
            'drivers',
            'trucks',
            'routes',
            'route_stops',
            'route_addresses',
            'deliveries',
            'permissions',
            'permission_groups',
            'driver_documents'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                if (!Schema::hasColumn($table, 'deleted_at')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->softDeletes();
                    });
                }
            }
        }
    }

    public function down()
    {
        // Remove soft deletes das tabelas
        $tables = [
            'users',
            'drivers',
            'trucks',
            'routes',
            'route_stops',
            'route_addresses',
            'deliveries',
            'permissions',
            'permission_groups',
            'driver_documents'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
}; 