<?php
// models/Ticket.php
require_once __DIR__ . '/../config.php';

class Ticket {
    public $id, $title, $description, $status, $user_id, $department_id, $created_at;

    public static function create($title, $description, $user_id, $department_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO tickets (title, description, user_id, department_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $user_id, $department_id]);
        return self::findById($db->lastInsertId());
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function updateStatus($id, $status) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Add more methods as needed, e.g., assign to agent (you can add an 'agent_id' column if desired)
}
