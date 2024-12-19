<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Edit User</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h1 class="text-3xl font-semibold text-center text-gray-700 mb-6">Edit User</h1>

        <?php if (isset($success)): ?>
            <div class="mb-4 text-green-600"><?= $success; ?></div>
        <?php endif; ?>

        <?php if (isset($errors)): ?>
            <div class="mb-4 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Form to Edit User -->
        <form action="<?= base_url('/edit/' . $user->id) ?>" method="post">
            <!-- Name -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Name</label>
                <input type="text" name="username" id="username" value="<?= old('username', $user->username); ?>"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email', $user->email); ?>"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">New Password (Leave blank to keep current password)</label>
                <input type="password" name="password" id="password" placeholder="Enter your new password"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Submit Button -->
            <div class="mb-4">
                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update User
                </button>
            </div>
        </form>

        <!-- Back to Dashboard -->
        <div class="text-center mt-4">
            <a href="/dashboard" class="text-blue-500 hover:underline">Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
 