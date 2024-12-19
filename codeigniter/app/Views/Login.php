<!-- <!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form id="loginForm">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="/register">Register here</a></p>
    <script>
        document.querySelector('#loginForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const response = await fetch('http://localhost:4000/api/users/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                })
            });
            if (response.ok) {
                const user = await response.json();
                alert(`Welcome ${user.name}`);
                window.location.href = user.role === 'admin' ? '/admin-dashboard' : '/team-dashboard';
            } else {
                alert('Invalid credentials');
            }
        });
    </script>
</body>
</html> -->


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script> <!-- JWT Decode Library -->
<!-- </head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <form id="loginForm" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex justify-between items-center">
                <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Login
                </button>
            </div>
        </form>
        <p class="mt-4 text-center text-sm">
            Don't have an account? <a href="/signup" class="text-blue-500 hover:underline">Register here</a>
        </p>
    </div>

    <script>
        document.querySelector('#loginForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const response = await fetch('http://localhost:4000/api/users/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                })
            });
            if (response.ok) {
                const data = await response.json();
                const token = data.token; 
                const decodedToken = jwt_decode(token); 

               
                const userName = decodedToken.name;
                const userRole = decodedToken.role;
                const userId = decodedToken.id;
                const userEmail = decodedToken.email;
                

                alert(`Welcome ${userName}`);
                // Redirect based on user role
                window.location.href = userRole === 'admin' ? '/admin-dashboard' : '/team-dashboard';
            } else {
                alert('Invalid credentials');
            }
        });
    </script>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script> <!-- JWT Decode Library -->
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <form id="loginForm" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required 
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex justify-between items-center">
                <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Login
                </button>
            </div>
        </form>
        <p class="mt-4 text-center text-sm">
            Don't have an account? <a href="/signup" class="text-blue-500 hover:underline">Register here</a>
        </p>
    </div>

    <script>
        document.querySelector('#loginForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const response = await fetch('http://localhost:4000/api/users/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password')
                })
            });

            if (response.ok) {
                const data = await response.json();
                const token = data.token; 
                const decodedToken = jwt_decode(token); 

                const userName = decodedToken.name;
                const userRole = decodedToken.role;
                const userId = decodedToken._id;
                const userEmail = decodedToken.email;

                alert(`Welcome ${userName}`);

                // Store the token in localStorage for future use (if needed)
                localStorage.setItem('token', token);
                

                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('Id', userId);
                sessionStorage.setItem('role', userRole);
                sessionStorage.setItem('name', userName);
                sessionStorage.setItem('email', userEmail);

                // Redirect based on user role directly from here
                if (userRole === 'admin') {
                    // Redirect to admin dashboard directly
                    window.location.href = '/admin-dashboard';  // Adjust this path to your actual URL structure
                } else if (userRole === 'team_member') {
                    // Redirect to team dashboard directly
                    window.location.href = '/team-dashboard';  // Adjust this path to your actual URL structure
                } else {
                    alert('Unknown user role');
                }
            } else {
                alert('Invalid credentials');
            }
        });
    </script>
</body>
</html> 




