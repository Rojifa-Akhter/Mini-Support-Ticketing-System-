<?php
// models/TicketNote.php
require_once __DIR__ . '/../config.php';

class TicketNote {
    public static function addNote($ticket_id, $user_id, $note) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
        return $stmt->execute([$ticket_id, $user_id, $note]);
    }

    public static function getNotesByTicket($ticket_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM ticket_notes WHERE ticket_id = ? ORDER BY created_at ASC");
        $stmt->execute([$ticket_id]);
        return $stmt->fetchAll();
    }
}
