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
        if (auth()->loggedIn()) {
            auth()->logout();
        }

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
        if (auth("tokens")->loggedIn()) {
            $userId = auth()->id();
            $userObject = new UserModel();
            $userData = $userObject->findById($userId);

            return $this->respond($this->genericResponse(
                ResponseInterface::HTTP_OK,
                "User profile",
                false,
                [
                    "user" => $userData
                ]
            ), ResponseInterface::HTTP_OK);
        }
    }

    // Logout endpoint
    public function logout()
    {
        // Handle user logout, destroy token
        if (auth()->loggedIn()) {
            auth()->logout();
            auth()->user()->revokeAllAccessTokens();

            return $this->respond($this->genericResponse(
                ResponseInterface::HTTP_OK,
                "User logged out successfully",
                false,
                []
            ), ResponseInterface::HTTP_OK);
        }
    }

    public function invalidRequest()
    {
        return $this->respond($this->genericResponse(
            ResponseInterface::HTTP_FORBIDDEN,
            "Invalid Request, please login",
            true,
            []
        ), ResponseInterface::HTTP_FORBIDDEN);
    }

    public function genericResponse(int $status, string | array $message, bool $error, array $data): array
    {
        return [
            "status" => $status,
            "message" => $message,
            "error" => $error,
            "data" => $data,
        ];
    }
}
