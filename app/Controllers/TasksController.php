<?php
namespace App\Controllers;

use App\Models\TaskModel;
use CodeIgniter\HTTP\ResponseInterface;

class TasksController extends BaseController
{
    public function home()
    {
        return view('tasks');
    }

    public function index()
    {
        $model = new TaskModel();
        $data = $model->orderBy('id', 'desc')->findAll();
        $data = array_map(function ($el) {
            return [
                'title' => $el['title'],
                'id' => (int) $el['id'],
                'completed' => (bool) $el['completed'],
                'date' => date('Y-m-d H:i:s', strtotime($el['created_at'])),
            ];
        }, $data);
        return $this->response->setJSON($data);
    }

    public function show($id)
    {
        $model = new TaskModel();
        $task = $model->find($id);
        if (!$task) {
            return $this
                ->response
                ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['error' => 'Tarea no existe']);
        }
        $task = [
            'title' => $task['title'],
            'id' => $task['id'],
            'completed' => (bool) $task['completed'],
            'date' => date('Y-m-d H:i:s', strtotime($task['created_at'])),
        ];
        return $this->response->setJSON(['data' => $task]);
    }

    public function create()
    {
        $payload = $this->request->getJSON(true);

        $taskTitle = isset($payload['task']) ? trim($payload['task']) : '';
        
        if (empty($taskTitle)) {
            return $this
                ->response
                ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON(['error' => 'No se enviaron datos']);
        }

        if (strlen($taskTitle) < 3 || strlen($taskTitle) > 255) {
            return $this
                ->response
                ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON(['error' => 'La tarea debe tener al menos 3 carácteres (máximo 255)']);
        }

        $data = [
            'title' => $taskTitle,
        ];

        $model = new TaskModel();

        $taskExistsByTitle = $model->where('title', $data['title'])->first();

        if ($taskExistsByTitle !== null) {
            return $this
            ->response
            ->setStatusCode(ResponseInterface::HTTP_CONFLICT)
            ->setJSON(['error' => 'Ya existe una tarea con ese título']);
        }

        if (!$model->save($data)) {
            return $this
                ->response
                ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON(['error' => 'Error al guardar la tarea']);
        }

        $id = $model->getInsertID();
        $task = $model->find($id);

        $response = [
            'data' => [
                'id' => (int) $task['id'],
                'title' => $task['title'],
                'completed' => (bool) $task['completed'],
                'date' => date('Y-m-d H:i:s', strtotime($task['created_at'])),
            ]
        ];

        return $this
            ->response
            ->setStatusCode(ResponseInterface::HTTP_CREATED)
            ->setJSON($response);
    }

    public function update($id)
    {
        $payload = $this->request->getJSON(true);
        if ($payload === null || !is_array($payload) || empty($payload)) {
            return $this->response
            ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
            ->setJSON(['error' => 'No se enviaron datos']);
        }

        $taskTitle = isset($payload['task']) ? trim($payload['task']) : '';
        $taskCompleted = isset($payload['completed']) ? $payload['completed'] : null;

        if (empty($taskTitle) && is_null($taskCompleted)) {
            return $this->response
            ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
            ->setJSON(['error' => 'No se enviaron datos']);
        }

        $data = [];

        if (!empty($taskTitle)) {

            if (strlen($taskTitle) < 3 || strlen($taskTitle) > 255) {
                return $this
                    ->response
                    ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                    ->setJSON(['error' => 'La tarea debe tener al menos 3 carácteres (máximo 255)']);
            }

            $data['title'] = $taskTitle;
        }

        if (!is_null($taskCompleted)) {
            if (!is_bool($taskCompleted)) {
                return $this
                    ->response
                    ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                    ->setJSON(['error' => 'El campo completed debe ser de tipo boolean']);
            }
            $data['completed'] = $taskCompleted;
        }

        $model = new TaskModel();

        $task = $model->find($id);

        if (!$task) {
            return $this->response
            ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON(['error' => 'Tarea no existe']);
        }

        if (isset($data['title'])) {
            $taskExistsByTitle = $model->where('title', $data['title'])->first();
            if ($taskExistsByTitle !== null && $taskExistsByTitle['id'] !== $task['id']) {
                return $this
                ->response
                ->setStatusCode(ResponseInterface::HTTP_CONFLICT)
                ->setJSON(['error' => 'Ya existe una tarea con ese título']);
            }
        }

        if (!$model->update($id, $data)) {
            return $this->response->
            setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)->
            setJSON(['error' => 'No se pudo actualizar la tarea']);
        }

        $task = $model->find($id);

        $response = [
            'data' => [
                'id' => (int) $task['id'],
                'title' => $task['title'],
                'completed' => (bool) $task['completed'],
                'date' => date('Y-m-d H:i:s', strtotime($task['created_at'])),
            ]
        ];

        return $this->response->setJSON($response);
    }

    public function delete($id)
    {
        $model = new TaskModel();
        $task = $model->find($id);
        if (!$task) {
            return $this->response
            ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON(['error' => 'Tarea no existe']);
        }
        $model->delete($id);
        return $this->response->setJSON(['data' => ['delete' => true]]);
    }
}
