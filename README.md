# SRN Task Test

Este repositorio contiene la implementación del TO DO que se pide en el test

## Instalación

### Clonación repositorio

Se debe clonar el repositorio BgonzalezJ/SRN-task-test.git

```bash 
$ git@github.com:BgonzalezJ/SRN-task-test.git SRN-task-test
$ cd SRN-task-test
```

### Levantar proyecto con docker

El proyecto cuenta con un Dockerfile y un docker-compose.yml que contiene lo necesario para levantar el proyecto (PHP 8, DB Mysql, nginx)

```bash 
$ docker compose build
$ docker compose up -d
```

### Configuración .env

Se debe copiar el archivo .env.example y reemplazar los valores en caso de ser necesario. Para el efecto del test dentro del .env.example ya vienen los valores de la base de datos.

### Migración de tabla tasks

Se debe ejecutar php spark migrate dentro del contenedor de Docker

```bash 
$ docker compose exec app php spark migrate  
```

## Endpoints tasks

## Endpoints

| Endpoint         | Método | Campos a enviar (body)                     | Headers              | Respuestas posibles |
|-----------------|--------|-------------------------------------------|--------------------|-------------------|
| /tasks          | GET    | -                                         | Content-Type: application/json | 200 OK → `{ "data": [ { "id": 1, "title": "Mi tarea", "completed": false, "date": "2025-08-25T03:00:00Z" } ] }`<br>404 Not Found → `{ "error": "Ocurrió un error" }` |
| /tasks/{id}     | GET    | -                                         | Content-Type: application/json | 200 OK → `{ "data": { "id": 1, "title": "Mi tarea", "completed": false, "date": "2025-08-25T03:00:00Z" } }`<br>404 Not Found → `{ "error": "Ocurrió un error" }` |
| /tasks          | POST   | `task` (string, obligatorio)             | Content-Type: application/json | 201 Created → `{ "data": { "id": 1, "title": "Mi tarea", "completed": false, "date": "2025-08-25T03:00:00Z" } }`<br>409 Conflict → `{ "error": "Ocurrió un error" }` |
| /tasks/{id}     | PUT    | `task` (string, opcional)<br>`completed` (boolean, opcional) | Content-Type: application/json | 200 OK → `{ "data": { "id": 1, "title": "Mi tarea actualizada", "completed": true, "date": "2025-08-25T03:00:00Z" } }`<br>404 Not Found → `{ "error": "Ocurrió un error" }`<br>409 Conflict → `{ "error": "Ocurrió un error" }` |
| /tasks/{id}     | DELETE | -                                         | Content-Type: application/json | 200 OK → `{ "data": { "delete": true } }`<br>404 Not Found → `{ "error": "Ocurrió un error" }` |


