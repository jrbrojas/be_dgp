<?php

namespace App\Services;

class PermissionService
{
    public static $permissionsByRole = [
        'admin' => [
            'User' => ['ver usuarios', 'crear usuario', 'editar usuario', 'archivar usuario'],
            'Escenario' => ['ver escenarios', 'ver escenario', 'crear escenario', 'editar escenario', 'archivar escenario', 'descargar escenario'],
            'Role' => ['ver roles', 'crear rol', 'editar rol', 'archivar rol'],
        ],
        'usuario' => [
            'Escenario' => ['ver escenarios', 'ver escenario', 'crear escenario', 'editar escenario', 'archivar escenario', 'descargar escenario'],
        ],
    ];
}
