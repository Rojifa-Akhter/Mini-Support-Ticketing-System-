<?php
// models/Department.php
require_once __DIR__ . '/../config.php';

class Department {
    public $id, $name;

    public static function all() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM departments");
        return $stmt->fetchAll();
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($name) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->execute([$name]);
        return self::findById($db->lastInsertId());
    }

    public static function update($id, $name) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE departments SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public static function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
