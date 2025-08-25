<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>To-Do Tasks</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>To-Do Tasks</h1>
        <div class="row">
            <input id="newTask" name="newTask" type="text" placeholder="Nueva tarea..." />
            <button id="addBtn">Agregar</button>
        </div>


        <ul id="list"></ul>

        <div id="modalTask" class="hide">
            <div class="modal-wrapper">
                <input type="hidden" name="taskId" id="taskId" />
                <div>
                    <label for="taskTitle">Tarea</label>
                    <input type="text" name="taskTitle" id="taskTitle" />
                </div>
                <div>
                    <label for="taskDate">Fecha de creación</label>
                    <input type="text" name="taskDate" id="taskDate" readonly />
                </div>

                <div>
                    <label for="taskCompleted">Completada</label>
                    <input type="checkbox" name="taskCompleted" id="taskCompleted">
                </div>

                <div>
                    <button id="updateTaskBtn">Modificar</button>
                </div>

                <div>
                    <button id="deleteTaskBtn">Eliminar</button>
                </div>

                <div>
                    <button id="closeModalBtn">Cerrar</button>
                </div>
            </div>
        </div>

        <script src="/app.js"></script>
    </body>
</html>