<?php
// controllers/AuthController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/TokenStore.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';

class AuthController {
    private $tokenStore;

    public function __construct() {
        $this->tokenStore = new TokenStore();
    }

    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['name'], $input['email'], $input['password'], $input['role'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Missing fields'], 400);
        }

        if (!in_array($input['role'], ['admin', 'agent'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Invalid role'], 400);
        }

        if (User::findByEmail($input['email'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Email already registered'], 400);
        }

        $user = User::create($input['name'], $input['email'], $input['password'], $input['role']);
        ResponseHelper::json(['status'=>true,'message' => 'User registered', 'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role]]);
    }

    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['email'], $input['password'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Email or password missing'], 400);
        }

        $user = User::findByEmail($input['email']);
        if (!$user || !$user->verifyPassword($input['password'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Invalid credentials'], 401);
        }

        // Generate token
        $token = bin2hex(random_bytes(16));
        $this->tokenStore->addToken($token, $user->id);

        ResponseHelper::json(['status'=>true,'message' => 'Login successful', 'token' => $token]);
    }

    public function logout() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            ResponseHelper::json(['status'=>false,'error' => 'Authorization header missing'], 401);
        }

        $token = trim(str_replace('Bearer', '', $headers['Authorization']));
        $this->tokenStore->removeToken($token);
        ResponseHelper::json(['status'=>true,'message' => 'Logout successful']);
    }
}
