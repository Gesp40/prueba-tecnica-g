<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pieza;
use App\Models\Registro;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistroControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function crear_registro_cambia_estado_pieza_a_fabricada()
    {
        // Arrange: Pieza pendiente
        $pieza = Pieza::factory()->create(['estado' => 'pendiente']);
        
        // Act: POST /registros
        $response = $this->actingAs($this->user)->post('/registros', [
            'pieza_id' => $pieza->id,
            'peso_real' => 10.500,
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('piezas', [
            'id' => $pieza->id,
            'estado' => 'fabricada'
        ]);
        $this->assertDatabaseHas('registros', [
            'pieza_id' => $pieza->id,
            'user_id' => $this->user->id,
            'peso_real' => 10.500,
            'diferencia' => $pieza->peso_teorico - 10.500
        ]);
    }

    /** @test */
    public function eliminar_ultimo_registro_devuelve_pieza_a_pendiente()
    {
        // Arrange: Pieza fabricada con UN registro
        $pieza = Pieza::factory()->create(['estado' => 'fabricada']);
        $registro = Registro::factory()->create([
            'pieza_id' => $pieza->id,
            'user_id' => $this->user->id
        ]);

        // Act: DELETE /registros/{registro}
        $response = $this->actingAs($this->user)
            ->delete("/registros/{$registro->id}");

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('piezas', [
            'id' => $pieza->id,
            'estado' => 'pendiente'
        ]);
        $this->assertDatabaseMissing('registros', ['id' => $registro->id]);
    }

    /** @test */
    public function actualizar_registro_recalcula_diferencia_sin_cambiar_estado()
    {
        // Arrange: Registro existente
        $pieza = Pieza::factory()->create([
            'estado' => 'fabricada',
            'peso_teorico' => 15.000
        ]);
        $registro = Registro::factory()->create([
            'pieza_id' => $pieza->id,
            'user_id' => $this->user->id,
            'peso_real' => 14.000,
            'diferencia' => 1.000
        ]);

        // Act: PUT /registros/{registro}
        $response = $this->actingAs($this->user)
            ->put("/registros/{$registro->id}", [
                'peso_real' => 15.500
            ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('piezas', [
            'id' => $pieza->id,
            'estado' => 'fabricada' // No cambia
        ]);
        $this->assertDatabaseHas('registros', [
            'id' => $registro->id,
            'peso_real' => 15.500,
            'diferencia' => -0.500 // Recalculado
        ]);
    }

    /** @test */
    public function api_reportes_pendientes_devuelve_conteo_correcto()
    {
        // Arrange: Proyecto con piezas mixtas
        $proyecto = Proyecto::factory()->create();
        $bloque = $proyecto->bloques()->create(['nombre' => 'B1']);
        
        // 3 pendientes, 2 fabricadas
        Pieza::factory()->count(3)->create([
            'bloque_id' => $bloque->id,
            'estado' => 'pendiente'
        ]);
        Pieza::factory()->count(2)->create([
            'bloque_id' => $bloque->id,
            'estado' => 'fabricada'
        ]);

        // Act: GET /api/reportes/pendientes
        $response = $this->actingAs($this->user)
            ->getJson('/api/reportes/pendientes');

        // Assert
        $response->assertOk()
            ->assertJsonStructure(['data' => [
                '*' => ['proyecto', 'pendientes']
            ]])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.pendientes', 3);
    }

    /** @test */
    public function api_reportes_estadisticas_devuelve_formato_correcto()
    {
        // Arrange: Dos proyectos con piezas mixtas
        $proyectos = Proyecto::factory()->count(2)->create();
        
        foreach ($proyectos as $i => $proyecto) {
            $bloque = $proyecto->bloques()->create(['nombre' => "B{$i}"]);
            Pieza::factory()->count(2)->create([
                'bloque_id' => $bloque->id,
                'estado' => 'pendiente'
            ]);
            Pieza::factory()->count(3)->create([
                'bloque_id' => $bloque->id,
                'estado' => 'fabricada'
            ]);
        }

        // Act: GET /api/reportes/estadisticas
        $response = $this->actingAs($this->user)
            ->getJson('/api/reportes/estadisticas');

        // Assert
        $response->assertOk()
            ->assertJsonStructure(['data' => [
                'labels',
                'datasets' => [
                    '*' => ['label', 'data']
                ]
            ]]);

        $data = $response->json('data');
        
        // Verifica consistencia de datos
        $this->assertCount(2, $data['labels']); // 2 proyectos
        $this->assertCount(2, $data['datasets']); // pendientes y fabricadas
        $this->assertEquals(2, $data['datasets'][0]['data'][0]); // 2 pendientes
        $this->assertEquals(3, $data['datasets'][1]['data'][0]); // 3 fabricadas
    }
}