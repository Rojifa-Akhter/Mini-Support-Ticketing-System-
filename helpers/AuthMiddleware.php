<?php
// helpers/AuthMiddleware.php
require_once 'TokenStore.php';
require_once __DIR__ . '/../models/User.php';
require_once 'ResponseHelper.php';

class AuthMiddleware {
    private $tokenStore;
    public $user;

    public function __construct() {
        $this->tokenStore = new TokenStore();
    }

    public function handle() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            ResponseHelper::json(['error' => 'Authorization header missing'], 401);
        }

        $token = trim(str_replace('Bearer', '', $headers['Authorization']));
        if (!$token) {
            ResponseHelper::json(['error' => 'Token missing'], 401);
        }

        $userId = $this->tokenStore->getUserId($token);
        if (!$userId) {
            ResponseHelper::json(['error' => 'Invalid token'], 401);
        }

        // Load user
        $user = User::findById($userId);
        if (!$user) {
            ResponseHelper::json(['error' => 'User not found'], 401);
        }

        $this->user = $user;
        return true;
    }
}
