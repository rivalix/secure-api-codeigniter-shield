<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class AuthController extends ResourceController
{
    use ResponseTrait;

    // Register endpoint
    public function register()
    {
        $rules = [
            "username" => "required|is_unique[users.username]",
            "email" => "required|is_unique[auth_identities.secret]",
            "password" => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => $this->validator->getErrors(),
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userObjet = new UserModel();
        $user = new User([
            "username" => $this->request->getVar("username"),
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ]);

        $userObjet->save($user);

        $response = [
            "status" => ResponseInterface::HTTP_OK,
            "message" => "User created successfully",
            "error" => false,
            "data" => []
        ];
        return $this->respond($response, ResponseInterface::HTTP_OK);
    }

    // Login endpoint
    public function login()
    {
        // Handle user login and also generate token
        $rules = [
            "email" => "required|valid_email",
            "password" => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => $this->validator->getErrors(),
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $credentials = [
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ];

        $loginAttempt = auth()->attempt($credentials);

        if (!$loginAttempt->isOK()) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Invalid credentials",
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userObject = new UserModel();
        $user_data = $userObject->findById(auth()->id());
        $token = $user_data->generateAccessToken("Th1sIsMyS3cur3Ap1");
        $auth_token = $token->raw_token;

        $response = [
            "status" => ResponseInterface::HTTP_OK,
            "message" => "User logged in",
            "error" => false,
            "data" => [
                "token" => $auth_token,
            ]
        ];

        return $this->respond($response, ResponseInterface::HTTP_OK);
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
