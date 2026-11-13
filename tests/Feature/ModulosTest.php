<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModulosTest extends TestCase
{
    public function test_las_pantallas_de_empleados_y_planificacion_responden(): void
    {
        $rutas = [
            '/empleados',
            '/empleados/create',
            '/empleados/1/edit',
            '/vehiculos/planificacion',
            '/lavados/planificacion',
        ];

        foreach ($rutas as $ruta) {
            $this->get($ruta)->assertStatus(200);
        }
    }
}
