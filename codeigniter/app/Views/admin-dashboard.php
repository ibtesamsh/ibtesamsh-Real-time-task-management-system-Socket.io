<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="http://localhost:4000/socket.io/socket.io.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    const socket = io("http://localhost:4000");
    const token = localStorage.getItem("token");
    const userId = sessionStorage.getItem("Id"); 

    if (!token || !userId) {
        alert("User is not authenticated. Please log in again.");
        window.location.href = "/login"; 
    }

    async function handleLogout() {
        const response = await fetch('http://localhost:4000/api/users/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.ok) {
            alert("Logged out successfully!");
            localStorage.removeItem("token");
            sessionStorage.clear();
            window.location.href = '/login';
        } else {
            alert("Failed to log out.");
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem("token");

        const taskForm = document.querySelector('#taskForm');
        if (taskForm) {
            taskForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const formData = new FormData(taskForm);

                const response = await fetch('http://localhost:4000/api/tasks', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        title: formData.get('title'),
                        description: formData.get('description'),
                        priority: formData.get('priority'),
                        assignee: formData.get('assignee'),
                        deadline: formData.get('deadline')
                    })
                });

                if (response.ok) {
                    alert('Task created successfully!');
                    loadTasks();
                } else {
                    const errorData = await response.json();
                    alert(`Failed to create task: ${errorData.message}`);
                }
            });
        } else {
            console.error("Task form not found");
        }

        async function loadTasks() {
            const response = await fetch('http://localhost:4000/api/tasks', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                const tasks = await response.json();
                const tasksDiv = document.getElementById('tasks');
                tasksDiv.innerHTML = tasks.map(task => `
                    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                        <h4 class="text-xl font-semibold">${task.title}</h4>
                        <p class="text-gray-600">${task.description}</p>
                        <p class="mt-2"><strong>Status:</strong> ${task.status}</p>
                    </div>
                `).join('');
            } else {
                console.error("Failed to fetch tasks.");
            }
        }

        loadTasks();

        socket.on("taskCreated", (task) => {
  alert(`New task created: ${task.title}`);
  loadTasks(); // Fetch and update task list
});

socket.on("taskStatusUpdated", (task) => {
  alert(`Task ${task.title} status updated to ${task.status}`);
  loadTasks(); // Fetch and update task list
});

socket.on("taskDeleted", (taskId) => {
  alert(`Task deleted: ${taskId}`);
  loadTasks(); // Fetch and update task list
});

socket.on("taskAssigned", (task) => {
  alert(`Task ${task.title} assigned to ${task.assignee.name}`);
  loadTasks(); // Fetch and update task list
});
    });
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Admin Dashboard</h2>
            <a href="javascript:void(0);" onclick="handleLogout()" class="text-blue-600 hover:underline">Logout</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4">Create Task</h3>
            <form id="taskForm" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label for="assignee" class="block text-sm font-medium text-gray-700">Assign To</label>
                    <input type="text" name="assignee" id="assignee" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input type="date" name="deadline" id="deadline" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Create Task</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-semibold mb-4">All Tasks</h3>
            <div id="tasks" class="space-y-4"></div>
        </div>
    </div>
</body>
</html> -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="http://localhost:4000/socket.io/socket.io.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        const socket = io("http://localhost:4000");
        const token = localStorage.getItem("token");
        const userId = sessionStorage.getItem("Id");

        if (!token || !userId) {
            alert("User is not authenticated. Please log in again.");
            window.location.href = "/login";
        }

        async function handleLogout() {
            const response = await fetch('http://localhost:4000/api/users/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                alert("Logged out successfully!");
                localStorage.removeItem("token");
                sessionStorage.clear();
                window.location.href = '/login';
            } else {
                alert("Failed to log out.");
            }
        }

        // Move deleteTask function outside the DOMContentLoaded event listener
        async function deleteTask(taskId) {
            const response = await fetch(`http://localhost:4000/api/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                alert('Task deleted successfully');
                loadTasks(); // Reload the task list after deletion
            } else {
                alert('Failed to delete task');
            }
        }

        // Move loadTasks function outside the DOMContentLoaded event listener
        async function loadTasks() {
            const response = await fetch('http://localhost:4000/api/tasks', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const taskTable = document.getElementById('tasksTable');

            if (response.ok) {
                const tasks = await response.json();
                taskTable.innerHTML = tasks.map(task => `
                    <tr class="border-b">
                        <td class="px-4 py-2">${task.title}</td>
                        <td class="px-4 py-2">${task.description}</td>
                        <td class="px-4 py-2">${task.priority}</td>
                        <td class="px-4 py-2">${new Date(task.deadline).toLocaleDateString()}</td>
                        <td class="px-4 py-2">${task.assignee ? task.assignee.name : 'Unassigned'}</td>
                        <td class="px-4 py-2">${task.status}</td>
                        <td class="px-4 py-2">
                            <button onclick="deleteTask('${task._id}')" class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                console.error("Failed to fetch tasks.");
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const taskForm = document.querySelector('#taskForm');
            const taskCreationSection = document.getElementById('taskCreationSection');
            const createTaskButton = document.getElementById('createTaskButton');
            const cancelTaskButton = document.getElementById('cancelTaskButton');

            // Show task creation form when "Create Task" button is clicked
            createTaskButton.addEventListener('click', () => {
                taskCreationSection.classList.remove('hidden');
            });

            // Hide task creation form and reset inputs when "Cancel" button is clicked
            cancelTaskButton.addEventListener('click', () => {
                taskCreationSection.classList.add('hidden');
                taskForm.reset();
            });

            if (taskForm) {
                taskForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const formData = new FormData(taskForm);

                    const response = await fetch('http://localhost:4000/api/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            title: formData.get('title'),
                            description: formData.get('description'),
                            priority: formData.get('priority'),
                            assignee: formData.get('assignee'),
                            deadline: formData.get('deadline')
                        })
                    });

                    if (response.ok) {
                        alert('Task created successfully!');
                        loadTasks(); // Reload task list after creation
                        taskForm.reset(); // Reset the form fields after task creation
                        taskCreationSection.classList.add('hidden'); // Hide the form
                    } else {
                        const errorData = await response.json();
                        alert(`Failed to create task: ${errorData.message}`);
                    }
                });
            }

            socket.on("taskCreated", (task) => {
                loadTasks(); 
            });

            socket.on("taskDeleted", () => {
                loadTasks();
            });

            socket.on("taskStatusUpdated", (task) => {
                alert(`Task ${task.title} status updated to ${task.status}`);
                loadTasks(); 
            });

            loadTasks(); 
        });
    </script>
</head>

<body class="bg-gray-100 font-sans text-gray-800">
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-center items-center mb-8">
            <h2 class="text-3xl font-bold">Admin Dashboard</h2>
            <div>
                <a href="javascript:void(0);" onclick="handleLogout()" class="relative left-[27rem] text-blue-600 hover:underline">Logout</a>
            </div>
        </div>

        <!-- Create Task Button -->
        <div class="mb-4">
            <button id="createTaskButton" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600">
                Create Task
            </button>
        </div>

        <!-- Task Creation Form -->
        <div id="taskCreationSection" class="bg-white p-6 rounded-lg shadow-lg mb-8 hidden">
            <h3 class="text-2xl font-semibold mb-4">Create Task</h3>
            <form id="taskForm" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" id="title" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label for="assignee" class="block text-sm font-medium text-gray-700">Assign To</label>
                    <input type="text" name="assignee" id="assignee" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input type="date" name="deadline" id="deadline" required class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex justify-between">
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Create Task</button>
                    <button type="button" id="cancelTaskButton" class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Tasks Table -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-2xl font-semibold mb-4">All Tasks</h3>
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Priority</th>
                        <th class="px-4 py-2 text-left">Deadline</th>
                        <th class="px-4 py-2 text-left">Assignee</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="tasksTable"></tbody>
            </table>
        </div>
    </div>
</body>

</html>

