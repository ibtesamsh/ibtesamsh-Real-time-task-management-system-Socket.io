<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Upload CSV to Register Users</h2>

        <!-- Flash messages -->
        <?php if(session()->getFlashdata('error')): ?>
    <div class="text-red-500 mb-4 p-2 bg-red-100 border border-red-400 rounded relative">
        <button onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 p-2 text-red-700 hover:text-red-900 bg-transparent border-none">
            ×
        </button>
        <?php echo session()->getFlashdata('error'); ?>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    <div class="text-green-500 mb-4 p-2 bg-green-100 border border-green-400 rounded relative">
        <button onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 p-2 text-green-700 hover:text-green-900 bg-transparent border-none">
            ×
        </button>
        <?php echo session()->getFlashdata('success'); ?>
    </div>
<?php endif; ?>

        <form action="/user-upload/upload" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="csv_file" class="block text-gray-600">Choose CSV file:</label>
                <input type="file" name="csv_file" id="csv_file" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <button type="submit" class="w-full py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Upload</button>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600">Go back to <a href="/dashboard" class="text-blue-500 hover:underline">Dashboard</a></p>
            </div>
        </form>
    </div>

</body>
</html>
