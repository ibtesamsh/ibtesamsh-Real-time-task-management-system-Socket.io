<?php

namespace App\Controllers;

use App\Models\UserModel;
use GuzzleHttp\Client;
use \Firebase\JWT\JWT;


class Home extends BaseController

{
    public function __construct()
    {
        helper('form');
        helper('url');
        session();
    }

    public function index(): string
    {
        return view('login');
    }
    //------------------------------------dashboard-------------------------------------
    public function dashboard()
    {
        // Check if the user is logged in by checking the session
        $user_id = session()->get('user_id');
        $token = session()->get('token');
        if (!$user_id && !$token) {
            return redirect()->to('/login')->with('error', 'Please log in to access the dashboard.');
        }

        // Pagination logic
        $user_model = new UserModel();

        // Get the current page from query parameters (default to page 1)
        $page = $this->request->getVar('page') ?? 1;

        // Set the number of users per page
        $users_per_page = 5;

        // Get the total number of users
        $total_users = $user_model->countAll();

        // Calculate the offset for the query
        $offset = ($page - 1) * $users_per_page;

        // Fetch users for the current page
        $users = $user_model->findAll($users_per_page, $offset);

        // Calculate total pages
        $total_pages = ceil($total_users / $users_per_page);

        try {
            $client = new Client();
            $response = $client->get('http://localhost:3000/dashboard', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->session->get('token')
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $mongo_users = json_decode($response->getBody()->getContents());

                // Check if the response contains 'Users'
                foreach ($users as $i => $user) {
                    foreach ($mongo_users as $mongo_user) {
                        // Match users by email
                        if ($mongo_user->email === $user->email) {
                            // Assign mongoId to the user
                            $users[$i]->mongoId = $mongo_user->_id;
                        }
                    }
                }

                return view('dashboard', [
                    'users' => $users,
                    'current_page' => $page,
                    'total_pages' => $total_pages
                ]);
            } else {
                log_message('error', 'Node.js API returned status code: ' . $response->getStatusCode());
                $mongo_users = [];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Unable to get data from Mongo: ' . $e->getMessage());
            echo "Unable to get data from Mongo";
            throw $e;
        }
    }

    


    // -----------------------------Signup------------------------------------
    public function signup()
    {
        if (isset($_POST['username'])) {
            $user_model = new UserModel();
            $data = [

                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT)
            ];

            // Save the user to MySQL database
            $result = $user_model->save($data);

            // Now communicate with the Node.js server to add the user to MongoDB
            if ($result) {
                $this->syncUserWithMongoDB($data); // Sync with MongoDB
                return redirect()->to('/login')->with('success', 'Registration successful! Please log in.');
            } else {
                return redirect()->back()->with('error', 'Failed to register. Please try again.');
            }
        }
        return view('signup');
    }

    private function syncUserWithMongoDB($userData)
    {
        $client = new Client(); // Guzzle client

        try {
            $response = $client->post('http://localhost:3000/register', [
                'json' => [

                    'name' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => $this->request->getPost('password')
                ]
            ]);

            // Check response from the Node.js API
            if ($response->getStatusCode() == 200) {
                // Success in syncing with MongoDB
                log_message('info', 'User successfully synchronized with MongoDB.');
            } else {
                // Handle error
                log_message('error', 'Failed to sync with MongoDB.');
            }
        } catch (\Exception $e) {
            // Handle exception (e.g., Node.js server is down)
            log_message('error', 'Error syncing with MongoDB: ' . $e->getMessage());
        }
    }
    // ---------------------------------------login-------------------------------------
    public function login()
    {
        if (isset($_POST['email'])) {
            $user_model = new UserModel();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user = $user_model->where('email', $email)->first();

            if ($user) {
                if (password_verify($password, $user->password)) {
                    // User is logged in successfully, store user ID in session
                    $this->session->set('user_id', $user->id);

                    // Optionally, call Node.js API for additional login validation or actions
                    $this->validateLoginWithNodeJS($email, $password);

                    return redirect()->to('/dashboard')->with('success', 'Login successful!');
                } else {
                    return redirect()->back()->with('error', 'Invalid password. Please try again.');
                }
            } else {
                return redirect()->to('/signup')->with('error', 'Email not found. Please register.');
            }
        }
        return view('login');
    }

    private function validateLoginWithNodeJS($email, $password)
    {
        $client = new Client(); // Guzzle client

        try {
            $response = $client->post('http://localhost:3000/login', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);


            if ($response->getStatusCode() == 200) {
                $token = json_decode($response->getBody()->getContents());
                $this->session->set("token", $token->token);
                // Login validated by Node.js (optional, you can skip this)
                log_message('info', 'User successfully validated with Node.js.');
            } else {
                log_message('error', 'Login validation failed with Node.js.');
            }
        } catch (\Exception $e) {
            // Handle exception (e.g., Node.js server is down)
            log_message('error', 'Error validating login with Node.js: ' . $e->getMessage());
        }
    }
    // ---------------------------------logout-------------------------------
    public function logout()
    {
        session()->remove('user_id');  // Remove the user ID from session
        session()->remove('token');
        // session()->destroy();  // Destroy the session
        return redirect()->to('/login')->with('success', 'You have been logged out!');
    }
    // -----------------------------delete------------------------------------

    public function deleteUser($id, $mongoId)
    {
        $user_model = new UserModel();

        // Delete the user from the relational database (MySQL, etc.)
        $result = $user_model->delete($id);

        if ($result) {
            try {
                // Send DELETE request to your Node.js backend to delete from MongoDB
                $client = new Client();
                $response = $client->delete("http://localhost:3000/delete/$mongoId", [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->session->get('token'),
                    ],

                ]);

                // Check if the MongoDB delete was successful
                $responseData = json_decode($response->getBody()->getContents(), true);
                if (isset($responseData['message']) && $responseData['message'] == 'User deleted successfully') {
                    // Success
                    return redirect()->to('/dashboard')->with('success', 'User deleted successfully!');
                } else {
                    // If MongoDB deletion failed, you can either handle that or inform the user.
                    return redirect()->to('/dashboard')->with('error', 'Error deleting user from MongoDB.');
                }
            } catch (\Exception $e) {
                // Handle error
                return redirect()->to('/dashboard')->with('error', 'Error deleting user from MongoDB: ' . $e->getMessage());
            }
        } else {
            // If deleting from MySQL failed
            return redirect()->to('/dashboard')->with('error', 'Error deleting user from database.');
        }
    }

    // ------------------------------------------------------ update------------------------------
    public function update()
    {
        // Get the JSON data from the request body
        $data = $this->request->getJSON(true); // Get the raw POST data as an associative array

        // Log the incoming data for debugging (Optional)
        log_message('debug', 'Update data: ' . print_r($data, true));

        // Ensure that all required data is available
        if (!isset($data['id'], $data['mongoId'], $data['name'], $data['email'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields']);
        }

        $id = $data['id']; // The user ID from MySQL
        $mongoId = $data['mongoId']; // The user ID from MongoDB
        $name = $data['name']; // New username
        $email = $data['email']; // New email address

        // Proceed with the database and MongoDB update logic
        $user_model = new UserModel();
        $updatedData = [
            'username' => $name,
            'email' => $email
        ];

        // MySQL update
        $result = $user_model->update($id, $updatedData);
        if (!$result) {
            log_message('error', 'Failed to update MySQL user data for ID: ' . $id);
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update MySQL user data!']);
        }

        // MongoDB update via API
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('http://localhost:3000/edit', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                ],
                'json' => [
                    'id' => $mongoId,
                    'name' => $name,
                    'email' => $email
                ]
            ]);

            $responseBody = $response->getBody();
            $data = json_decode($responseBody, true);

            log_message('debug', 'MongoDB response: ' . print_r($data, true));

            if ($data['message'] == 'User updated successfully') {
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
            } else {
                log_message('error', 'MongoDB update failed for mongoId: ' . $mongoId);
                return $this->response->setJSON(['success' => false, 'message' => 'MongoDB update failed']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating MongoDB: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Unable to update MongoDB data']);
        }
    }
    //---------------------------------------upload bulk user data-----------------------------------------------//

    public function index1()
    {
        $user_id = session()->get('user_id');
        $token = session()->get('token');
        if (!$user_id && !$token) {
            return redirect()->to('/login')->with('error', 'Please log in to access the user-upload.');
        }
        return view('upload_form');
    }




    public function upload()
    {
        $file = $this->request->getFile('csv_file');
        $invalidRows = []; // Array to store invalid rows
        $validUserData = []; // Array to store valid user data for MySQL and MongoDB

        // Check if file is uploaded
        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->getTempName();
            // Get valid user data and invalid rows after processing the CSV
            list($validUserData, $invalidRows) = $this->processCSV($filePath);
        } else {
            return redirect()->to('/user-upload')->with('error', 'File upload failed.');
        }

        // After processing, if there are any invalid rows, create a CSV for them
        if (!empty($invalidRows)) {
            // Generate and download CSV for invalid rows
            return $this->generateInvalidCSV($invalidRows); // Create CSV and trigger download
        } else {
            // Insert valid data into MySQL in bulk
            

            return redirect()->to('/dashboard')->with('success', 'Users successfully registered and synced with MongoDB.');
        }
    }





    private function processCSV($filePath)
    {
        $csvFile = fopen($filePath, 'r');
        $userModel = new UserModel();
        $invalidRows = []; // Array to store invalid rows
        $validUserData = []; // Array to collect valid users for batch insert

        // Skip the header row if there is one
        $header = fgetcsv($csvFile);

        // Read each row from CSV
        while (($row = fgetcsv($csvFile)) !== false) {

            $username = $row[0];
            $email = $row[1];
            $password = $row[2];
            

            // If any required field is empty, mark this row as invalid
            if (empty($username) || empty($email) || empty($password)) {
                $invalidRows[] = $row; // Add the whole row to invalid rows
                continue; // Skip processing this row for database insertion
            }

            // Prepare data for MySQL (hash password for MySQL)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $userDataForMySQL = [
                'username' => $username,
                'email' => $email,
                'password' => $passwordHash, // Store hashed password in MySQL
            ];

            // Check if the user already exists in MySQL based on email
            $existingUser = $userModel->where('email', $email)->first();

            if ($existingUser) {
                log_message('info', 'User already exists in MySQL: ' . $email);
            } else {
                // Add to valid user data array for batch insert into MySQL
                $validUserData['mysql'][] = $userDataForMySQL;
            }

            // Prepare data for MongoDB (send raw password to MongoDB)
            $userDataForMongoDB = [
                'name' => $username,
                'email' => $email,
                'password' => $password,  // Send the raw password to MongoDB
            ];

            // Add user data to MongoDB valid array
            $validUserData['mongo'][] = $userDataForMongoDB;
        }
        if (!empty($validUserData['mysql'])) {
            $userModel = new UserModel();
            $userModel->insertBatch($validUserData['mysql']); // Perform batch insert into MySQL
        }
        if (!empty($validUserData['mongo'])) {
            $this->syncMongoDBInBulk($validUserData['mongo']); // Send to MongoDB
        }

        fclose($csvFile);
        return [$validUserData, $invalidRows]; // Return valid data for both MySQL and MongoDB, and invalid rows
    }






    private function generateInvalidCSV($invalidRows)
    {
        $filename = 'invalid_rows_' . time() . '.csv';
        $filePath = WRITEPATH . 'uploads/' . $filename;

        // Open a new file to write invalid rows
        $file = fopen($filePath, 'w');

        // Write header if you want to include it
        fputcsv($file, ['Username', 'Email', 'Password']);

        // Write each invalid row to the CSV
        foreach ($invalidRows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        // Force download of the CSV file
        return $this->response->download($filePath, null)->setFileName($filename);
    }



    private function syncMongoDBInBulk($validUserData)
    {
        $client = new Client(); // Guzzle client

        try {
            // Log the data to be sent
            log_message('info', 'Sending data to MongoDB API: ' . json_encode($validUserData));

            // Send a single request with an array of users to MongoDB API (raw password, no hashing)
            $response = $client->post('http://localhost:3000/bulk-register', [
                'json' => $validUserData // Send valid user data as an array of user objects
            ]);

            // Check response from the MongoDB API
            if ($response->getStatusCode() == 200) {
                log_message('info', 'Users successfully synchronized with MongoDB.');
            } else {
                log_message('error', 'Failed to sync with MongoDB. Response: ' . $response->getBody());
            }
        } catch (\Exception $e) {
            log_message('error', 'Error syncing with MongoDB: ' . $e->getMessage());
        }
    }
    public function setSessionData()
{
    $session = session();
    $input = $this->request->getJSON();

    // Decode the token to get user info
    $token = $input->token;  // Get token from the request body
    if ($token) {
        // Assuming JWT decoding is done on the backend and the token is valid
        $decodedToken = JWT::decode($token, getenv('JWT_SECRET'));  // Adjust decoding as per your setup

        // Store user info in session
        $session->set('isLoggedIn', true);
        $session->set('role', $decodedToken->role);
        $session->set('name', $decodedToken->name);
        $session->set('email', $decodedToken->email);

        return $this->response->setStatusCode(200)->setBody(json_encode(['message' => 'Session set successfully']));
    }

    return $this->response->setStatusCode(400)->setBody(json_encode(['message' => 'Invalid token']));
}
    public function admin()
    {
        // Check if the user is logged in and has the 'admin' role
        // $session = session();
        // if ( $session->get('role') !== 'admin') {
        //     return redirect()->to('/login');  // Redirect if not logged in or not an admin
        // }

        return view('/admin-dashboard');  // Render admin dashboard view
    }

    // Team Dashboard
    public function team()
    {
        // Check if the user is logged in and has the 'team_member' role
        // $session = session();
        // if (!$session->get('isLoggedIn') || $session->get('role') !== 'team_member') {
        //     return redirect()->to('/login');  // Redirect if not logged in or not a team member
        // }

        return view('team-dashboard');  // Render team dashboard view
    }

}
