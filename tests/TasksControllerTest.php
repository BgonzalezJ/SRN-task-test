<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class TasksControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Conectamos a la DB de test (SQLite en memoria)
        $this->db = \Config\Database::connect('tests');

        // Ejecutamos migraciones
        $migrations = \Config\Services::migrations();
        $migrations->latest();
    }

    /** @test */
    public function create_task_successfully()
    {
        $data = ['task' => 'Mi primera tarea'];
         $result = $this->withBody(json_encode($data))
                   ->withHeaders(['Content-Type' => 'application/json'])
                   ->post('/tasks');

        $result->assertStatus(201);

        $resultData =  json_decode($result->getJSON(true));
        $resultData = (array) $resultData->data;

        $this->assertArrayHasKey('id', $resultData);
        $this->assertIsInt($resultData['id']);
        $this->assertEquals('Mi primera tarea', $resultData['title']);
        $this->assertEquals(false, $resultData['completed']);

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $resultData['date']);
        $this->assertInstanceOf(\DateTime::class, $date);
    }

    /** @test */
    public function error_create_task_already_exists()
    {
        $data = ['task' => 'tarea que no puede ser duplicada'];
        $this->withBody(json_encode($data))
                   ->withHeaders(['Content-Type' => 'application/json'])
                   ->post('/tasks');
        $result = $this->withBody(json_encode($data))
                   ->withHeaders(['Content-Type' => 'application/json'])
                   ->post('/tasks');

        $result->assertStatus(409);
        $result->assertJSONFragment(['error' => 'Ya existe una tarea con ese título']);
    }

    /** @test */
    public function error_get_task_that_no_exists()
    {
        $result = $this->get('/tasks/1000');
        $result->assertStatus(404);
        $result->assertJSONFragment(['error' => 'Tarea no existe']);
    }
}
