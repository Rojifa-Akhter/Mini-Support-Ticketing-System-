<?php
// models/User.php
require_once __DIR__ . '/../config.php';

class User {
    public $id, $name, $email, $password_hash, $role;

    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        if ($data) {
            return self::mapData($data);
        }
        return null;
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if ($data) {
            return self::mapData($data);
        }
        return null;
    }

    public static function create($name, $email, $password, $role) {
        $db = Database::getInstance()->getConnection();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password_hash, $role]);
        return self::findById($db->lastInsertId());
    }

    private static function mapData($data) {
        $user = new User();
        $user->id = $data['id'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->role = $data['role'];
        return $user;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password_hash);
    }
}
