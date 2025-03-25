document.addEventListener('DOMContentLoaded', function () {
    const taskInput = document.getElementById('task-input');
    const addTaskButton = document.getElementById('add-task');
    const taskList = document.getElementById('task-list');

    loadTasks();

    // ajout d'une tâche
    addTaskButton.addEventListener('click', function () {
        addTask();
    });

    // avec la touche Entrée
    taskInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            addTask();
        }
    });

    // charger les tâches depuis l'API
    function loadTasks() {
        fetch('/api/tasks.php')
            .then(response => response.json())
            .then(tasks => {
                taskList.innerHTML = '';
                tasks.forEach(task => {
                    renderTask(task);
                });
            })
            .catch(error => console.error('Erreur lors du chargement des tâches:', error));
    }

    // ajout d'une nouvelle tâche
    function addTask() {
        const title = taskInput.value.trim();

        if (title) {
            fetch('/api/tasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ title })
            })
                .then(response => response.json())
                .then(task => {
                    renderTask(task);
                    taskInput.value = '';
                })
                .catch(error => console.error('Erreur lors de l\'ajout de la tâche:', error));
        }
    }

    // marquer une tâche comme terminée ou non
    function toggleTask(id, completed) {
        fetch(`/api/tasks.php?id=${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ completed })
        })
            .then(response => response.json())
            .then(() => loadTasks())
            .catch(error => console.error('Erreur lors de la mise à jour de la tâche:', error));
    }

    // supprimer une tâche
    function deleteTask(id) {
        fetch(`/api/tasks.php?id=${id}`, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(() => loadTasks())
            .catch(error => console.error('Erreur lors de la suppression de la tâche:', error));
    }

    // afficher une tâche dans la liste
    function renderTask(task) {
        const li = document.createElement('li');

        const taskDiv = document.createElement('div');
        taskDiv.className = 'task';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = task.completed === true || task.completed === 't';
        checkbox.addEventListener('change', function () {
            toggleTask(task.id, this.checked);
        });

        const span = document.createElement('span');
        span.textContent = task.title;
        if (task.completed === true || task.completed === 't') {
            span.className = 'completed';
        }

        taskDiv.appendChild(checkbox);
        taskDiv.appendChild(span);

        const deleteButton = document.createElement('button');
        deleteButton.className = 'delete-btn';
        deleteButton.textContent = 'Supprimer';
        deleteButton.addEventListener('click', function () {
            deleteTask(task.id);
        });

        li.appendChild(taskDiv);
        li.appendChild(deleteButton);

        taskList.appendChild(li);
    }
});