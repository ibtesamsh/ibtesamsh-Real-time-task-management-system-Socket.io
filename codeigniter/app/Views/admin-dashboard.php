<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="http://localhost:4000/socket.io/socket.io.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // const socket = io("http://localhost:4000");
    const socket = io("http://localhost:4000", {
  transports: ['websocket'],
  query: { token: localStorage.getItem("token") }
});
    const token = localStorage.getItem("token");
    const userId = sessionStorage.getItem("Id"); 

    
    if (!token || !userId) {
        alert("User is not authenticated. Please log in again.");
        window.location.href = "/login"; 
    }
    // const token = localStorage.getItem("token");
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

        socket.on('taskCreated', (task) => {
            alert(`New Task Created: ${task.title}`);
            loadTasks();
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

        socket.on('taskCreated', (task) => {
            alert(`New Task Created: ${task.title}`);
            loadTasks();
        });

        socket.on('taskUpdated', (task) => {
            alert(`Task Updated: ${task.title}`);
            loadTasks();
        });

        socket.on('taskDeleted', (taskId) => {
            alert(`Task Deleted: ${taskId}`);
            loadTasks();
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
</html>

