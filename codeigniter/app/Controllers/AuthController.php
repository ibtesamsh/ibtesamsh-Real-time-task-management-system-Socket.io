<?php
namespace App\Controllers;
use CodeIgniter\Controller;
class AuthController extends BaseController
{
    public function loginView()
    {
        // Render the login view
        return view('login');
    }

    public function login()
    {
        try {
            // Get email and password from POST request
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Prepare the payload for the API call
            $payload = [
                'email' => $email,
                'password' => $password
            ];

            // Send a POST request to the Node.js API
            $client = \Config\Services::curlrequest();
            $response = $client->post('http://localhost:4000/api/users/login', [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode($payload)
            ]);

            // Check if the response is successful
            if ($response->getStatusCode() === 200) {
                $responseBody = json_decode($response->getBody(), true);
                $token = $responseBody['token'];

                // Store the token in the session
                $this->session->set('token', $token);

                // Decode the JWT token to extract user details
                $decodedToken = json_decode(base64_decode(explode('.', $token)[1]));
                $userRole = $decodedToken->role ?? null;

                // Redirect based on user role
                if ($userRole === 'admin') {
                    return redirect()->to('/admin-dashboard');
                } elseif ($userRole === 'team_member') {
                    return redirect()->to('/team-dashboard');
                } else {
                    return redirect()->to('/login')->with('error', 'Unknown user role.');
                }
            } else {
                // Handle invalid credentials
                return redirect()->to('/login')->with('error', 'Invalid email or password.');
            }
        } catch (\Exception $e) {
            // Handle any unexpected errors
            log_message('error', $e->getMessage());
            return redirect()->to('/login')->with('error', 'An error occurred during login.');
        }
    }
}