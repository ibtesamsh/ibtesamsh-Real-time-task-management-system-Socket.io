<!-- <!DOCTYPE html>
<html>
<head>
    <title>Team Member Dashboard</title>
    <script src="http://localhost:4000/socket.io/socket.io.js"></script>
    <script>
    const socket = io("http://localhost:4000");

 
    const token = localStorage.getItem("token");
    const userId = sessionStorage.getItem("Id"); 

    
    if (!token || !userId) {
        alert("User is not authenticated. Please log in again.");
        window.location.href = "/login"; 
    }

   
    const getAuthHeaders = () => ({
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json",
    });

    async function loadMyTasks() {
        try {
            const response = await fetch(`http://localhost:4000/api/tasks?assignee=${userId}`, {
                headers: getAuthHeaders(),
            });
            if (!response.ok) {
                throw new Error("Failed to fetch tasks");
            }
            const tasks = await response.json();
            const tasksDiv = document.getElementById("myTasks");
            tasksDiv.innerHTML = tasks
                .map(
                    (task) => `
                <div>
                    <h4>${task.title}</h4>
                    <p>${task.description}</p>
                    <p>Status: ${task.status}</p>
                    <textarea data-task="${task._id}" placeholder="Add a comment"></textarea>
                    <button onclick="addComment('${task._id}')">Add Comment</button>
                </div>`
                )
                .join("");
        } catch (err) {
            console.error(err);
            alert("Error loading tasks: " + err.message);
        }
    }

   
    async function addComment(taskId) {
        const commentInput = document.querySelector(`[data-task="${taskId}"]`);
        try {
            const response = await fetch(`/api/tasks/${taskId}/comments`, {
                method: "POST",
                headers: getAuthHeaders(),
                body: JSON.stringify({ comment: commentInput.value, user: userId }),
            });
            if (!response.ok) {
                throw new Error("Failed to add comment");
            }
            alert("Comment added successfully!");
            loadMyTasks();
        } catch (err) {
            console.error(err);
            alert("Error adding comment: " + err.message);
        }
    }

   
    function sendMessage() {
        const chatInput = document.getElementById("chatInput");
        socket.emit("sendMessage", { message: chatInput.value, sender: userId });
        chatInput.value = "";
    }

    
    socket.on("taskStatusUpdated", (task) => {
        alert(`Task Updated: ${task.title} is now ${task.status}`);
    });

    socket.on("newComment", (comment) => {
        alert(`New Comment on Task: ${comment.comment}`);
    });

    socket.on("receiveMessage", (message) => {
        const chatDiv = document.getElementById("chat");
        chatDiv.innerHTML += `<p>${message.sender}: ${message.text}</p>`;
    });

   
    function logout() {
       
        localStorage.removeItem("token");
        sessionStorage.removeItem("Id");
        sessionStorage.removeItem("role");
        sessionStorage.removeItem("name");
        sessionStorage.removeItem("email");

       
        window.location.href = "/login";
    }

    
    loadMyTasks();

    </script>
</head>
<body>
    <h2>Team Member Dashboard</h2>
    Add a logout button that calls the logout function -->
    <!-- <a href="javascript:void(0);" onclick="logout()">Logout</a>

    <h3>My Tasks</h3>
    <div id="myTasks"></div>

    <h3>Chat</h3>
    <div id="chat"></div>
    <input id="chatInput" type="text" placeholder="Type a message">
    <button onclick="sendMessage()">Send Message</button>
</body>
</html> -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Member Dashboard</title>
    <script src="http://localhost:4000/socket.io/socket.io.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    const socket = io("http://localhost:4000");

    const token = localStorage.getItem("token");
    const userId = sessionStorage.getItem("Id");
    const userName = sessionStorage.getItem("name");

    if (!token || !userId) {
        alert("User is not authenticated. Please log in again.");
        window.location.href = "/login";
    }

    const getAuthHeaders = () => ({
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json",
    });

    async function loadMyTasks() {
        try {
            const response = await fetch(`http://localhost:4000/api/tasks?assignee=${userId}`, {
                headers: getAuthHeaders(),
            });
            if (!response.ok) {
                throw new Error("Failed to fetch tasks");
            }
            const tasks = await response.json();
            const tasksDiv = document.getElementById("myTasks");
            tasksDiv.innerHTML = tasks
                .map(
                    (task) => `
                <div class="bg-gray-100 p-4 mb-4 rounded shadow-md">
                    <h4 class="text-xl font-semibold">${task.title}</h4>
                    <p class="text-gray-700">${task.description}</p>
                    <p class="text-gray-700">${task.deadline}</p>
                    <p class="text-gray-700">${task.priority}</p>
                    <p>Status: 
                        <select onchange="updateTaskStatus('${task._id}', this.value)" class="mt-2 p-2 border rounded">
                            <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    </p>
                    <textarea data-task="${task._id}" placeholder="Add a comment" class="w-full mt-2 p-2 border rounded"></textarea>
                    <button onclick="addComment('${task._id}')" class="mt-2 bg-blue-500 text-white p-2 rounded">Add Comment</button>
                </div>`
                )
                .join("");
        } catch (err) {
            console.error(err);
            alert("Error loading tasks: " + err.message);
        }
    }

    async function addComment(taskId) {
        const commentInput = document.querySelector(`[data-task="${taskId}"]`);
        try {
            const response = await fetch(`/api/tasks/${taskId}/comments`, {
                method: "POST",
                headers: getAuthHeaders(),
                body: JSON.stringify({ comment: commentInput.value, user: userId }),
            });
            if (!response.ok) {
                throw new Error("Failed to add comment");
            }
            alert("Comment added successfully!");
            loadMyTasks();
        } catch (err) {
            console.error(err);
            alert("Error adding comment: " + err.message);
        }
    }

    async function updateTaskStatus(taskId, status) {
        try {
            const response = await fetch(`http://localhost:4000/api/tasks/${taskId}/status`, {
                method: "PUT",
                headers: getAuthHeaders(),
                body: JSON.stringify({ status }),
            });
            if (!response.ok) {
                throw new Error("Failed to update status");
            }
            const updatedTask = await response.json();
            alert(`Task status updated to ${updatedTask.task.status}`);
        } catch (err) {
            console.error(err);
            alert("Error updating task status: " + err.message);
        }
    }

    function sendMessage() {
        const chatInput = document.getElementById("chatInput");
        const message = chatInput.value.trim();
        if (message) {
            socket.emit("sendMessage", { message, sender: userName });
            chatInput.value = "";
        } else {
            alert("Message cannot be empty!");
        }
    }

    socket.on("taskStatusUpdated", (task) => {
        alert(`Task Updated: ${task.title} is now ${task.status}`);
    });

    socket.on("newComment", (comment) => {
        alert(`New Comment on Task: ${comment.comment}`);
    });

    socket.on("receiveMessage", (message) => {
        const chatDiv = document.getElementById("chat");
        const alignment = message.sender === userName ? "text-left" : "text-right";
        chatDiv.innerHTML += `<p class="${alignment} p-2"><strong>${message.sender}:</strong> ${message.text}</p>`;
        chatDiv.scrollTop = chatDiv.scrollHeight;
    });

    function logout() {
        localStorage.removeItem("token");
        sessionStorage.removeItem("Id");
        sessionStorage.removeItem("role");
        sessionStorage.removeItem("name");
        sessionStorage.removeItem("email");
        window.location.href = "/login";
    }

    loadMyTasks();
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-1/4 bg-blue-800 text-white p-6">
            <h2 class="text-xl font-semibold">Team Dashboard</h2>
            <ul class="mt-6">
                <li><a href="javascript:void(0);" onclick="loadMyTasks()" class="block py-2 px-4 hover:bg-blue-600">My Tasks</a></li>
                <li><a href="javascript:void(0);" onclick="loadChat()" class="block py-2 px-4 hover:bg-blue-600">Chat</a></li>
            </ul>
            <button onclick="logout()" class="mt-6 bg-red-500 text-white p-2 rounded w-full">Logout</button>
        </div>

        <!-- Main Content -->
        <div class="w-3/4 p-6">
            <h3 class="text-2xl font-semibold mb-4">Tasks</h3>
            <div id="myTasks"></div>

            <h3 class="text-2xl font-semibold mb-4 mt-10">Chat</h3>
            <div id="chat" class="bg-white p-4 rounded shadow-md h-64 overflow-y-auto"></div>
            <input id="chatInput" type="text" class="w-full p-2 mt-2 border rounded" placeholder="Type a message">
            <button onclick="sendMessage()" class="mt-2 bg-blue-500 text-white p-2 rounded w-full">Send</button>
        </div>
    </div>
</body>
</html>
