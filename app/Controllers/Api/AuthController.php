<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Models\UserModel;

class AuthController extends ResourceController
{
    // Register endpoint
    public function register()
    {
        // Add users into application
    }

    // Login endpoint
    public function login()
    {
        // Handle user login and also generate token
    }

    // Profile endpoint
    public function profile()
    {
        // Get logged is user info
    }

    // Logout endpoint
    public function logout()
    {
        // Handle user logout, destroy token
    }
}
