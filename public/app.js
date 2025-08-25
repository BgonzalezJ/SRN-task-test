const api = {
    list: async () => (await fetch('/tasks')).json(),
    get: async (taskId) => {
        const res = (await fetch('/tasks/' + taskId));
        const response = await res.json();
        if (res.ok) {
            return await response.data;
        }
        alert(response.error);
        return null;
    },
    create: async (task) => {
        const res = await fetch('/tasks', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ task })
        });
        const response = await res.json();
        if (res.ok) {
            return await response.data;
        }
        alert(response.error);
        return null;
    },
    update: async (id, data) => {
        const res = await fetch(`/tasks/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const response = await res.json();
        if (res.ok) {
            return await response.data;
        }
        alert(response.error);
        return null;
    },
    remove: async (id) => {
        const res = await fetch(`/tasks/${id}`, { method: 'DELETE' });
        if (res.ok) {
            return true;
        }
        const response = await res.json();
        if (res.ok) {
            return true;
        }
        alert(response.error);
        return false;
    },
};

const modal = {
    modalTask: document.getElementById('modalTask'),
    inputs: {
        id: document.getElementById('taskId'),
        title: document.getElementById('taskTitle'),
        date: document.getElementById('taskDate'),
        completed: document.getElementById('taskCompleted'),
    },
    buttons: {
        close: document.getElementById('closeModalBtn'),
        delete: document.getElementById('deleteTaskBtn'),
        update: document.getElementById('updateTaskBtn'),
    },
    actions: {
        closeModal: () => {
            modal.modalTask.setAttribute('class', 'hide');
            modal.actions.setInfoModal(null);
        },
        setInfoModal: (element) => {
            modal.inputs.id.value = element?.id || '';
            modal.inputs.title.value = element?.title || '';
            modal.inputs.date.value = element?.date || '';
            modal.inputs.completed.checked = element?.completed || false;
        }
    }
};

modal.buttons.close.onclick = () => {
    modal.actions.closeModal();
};

modal.buttons.delete.onclick = async () => {
    if (await deleteTask(modal.inputs.id.value)) {
        modal.actions.closeModal();
    }
};

modal.buttons.update.onclick = async () => {
    const task = {
        id: modal.inputs.id.value,
        title: modal.inputs.title.value,
        completed: modal.inputs.completed.checked
    };
    if (await updateTask(task)) {
        modal.actions.closeModal();
    }
};


const listEl = document.getElementById('list');
const newTaskEl = document.getElementById('newTask');
const addBtn = document.getElementById('addBtn');

function createTaskListElementHtml(element) {
    const li = document.createElement('li');
    const divWrapper = document.createElement('div');
    const divWrapperBtns = document.createElement('div');

    li.setAttribute('id', `task-${element.id}`);
    li.dataset.completed = element.completed ? 1 : 0;

    const title = document.createElement('p');
    title.textContent = `Tarea #${element.id}: "${element.title}"`;
    title.setAttribute('id', `titleTask-${element.id}`);
    if (element.completed) {
        title.setAttribute('class', 'completed');
    }
    divWrapper.appendChild(title);

    const date = document.createElement('p');
    date.textContent = `Fecha de creación: ${element.date}`;
    divWrapper.appendChild(date);

    const updateBtn = document.createElement('button');
    updateBtn.setAttribute('id', `updateBtnTask-${element.id}`);
    updateBtn.textContent = element.completed ? 'Desmarcar' : 'Completar';
    updateBtn.onclick = async () => {
        const completed = li.dataset.completed === '1';
        element.completed = !completed;
        await updateTask(element);        
    };
    divWrapperBtns.appendChild(updateBtn);

    // Botón eliminar
    const deleteBtn = document.createElement('button');
    deleteBtn.setAttribute('id', `deleteBtnTask-${element.id}`);
    deleteBtn.textContent = 'Eliminar';
    deleteBtn.onclick = () => {
        deleteTask(element.id);
    };
    divWrapperBtns.appendChild(deleteBtn);

    const seeBtn = document.createElement('button');
    seeBtn.textContent = 'Ver tarea';
    seeBtn.onclick = async () => {
        const task = await api.get(element.id);
        modal.actions.setInfoModal(task);
        modalTask.setAttribute('class', '');
    };
    divWrapperBtns.appendChild(seeBtn);

    divWrapper.appendChild(divWrapperBtns);
    li.appendChild(divWrapper);

    return li;
}

function updateTaskListElement(element) {
    const li = document.getElementById(`task-${element.id}`);
    li.dataset.completed = element.completed ? 1 : 0;
    const title = document.getElementById(`titleTask-${element.id}`);
    title.textContent = `Tarea #${element.id}: "${element.title}"`;
    if (element.completed) {
        title.setAttribute('class', 'completed');
    } else {
        title.setAttribute('class', '');
    }
    const updateBtn = document.getElementById(`updateBtnTask-${element.id}`);
    updateBtn.textContent = element.completed ? 'Desmarcar' : 'Completar';
}


async function render() {
    const tasks = await api.list();
    listEl.innerHTML = '';
    tasks.forEach(element => {
        const li = createTaskListElementHtml(element);
        listEl.appendChild(li);
    });
}

async function addTask() {
    const newTask = await api.create(newTaskEl.value);
    if (newTask !== null) {
        const li = createTaskListElementHtml(newTask);
        listEl.prepend(li);
    }
    newTaskEl.value = '';
}

async function deleteTask(id) {
    if (await api.remove(id)) {
        const li = document.getElementById(`task-${id}`);
        li.remove();
        return true;
    }
    return false;
}

async function updateTask(task) {
    const { id, title, completed } = task;
    const updatedTask = await api.update(id, {task: title, completed});
    if (updatedTask !== null) {
        updateTaskListElement(updatedTask);
        return true;
    }
    return false;
}

render();

addBtn.onclick = async () => {await addTask()};