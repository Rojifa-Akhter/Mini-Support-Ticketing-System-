<?php
// controllers/TicketController.php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/TicketNote.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';

class TicketController
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['title'], $input['description'], $input['department_id'])) {
            ResponseHelper::json(['error' => 'Missing fields'], 400);
        }

        // Create ticket
        $ticket = Ticket::create($input['title'], $input['description'], $this->user->id, $input['department_id']);
        ResponseHelper::json(['message' => 'Ticket created', 'ticket' => $ticket]);
    }

    public function addNote($ticket_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['note'])) {
            ResponseHelper::json(['error' => 'Note is required'], 400);
        }

        // Verify ticket exists
        $ticket = Ticket::findById($ticket_id);
        if (! $ticket) {
            ResponseHelper::json(['error' => 'Ticket not found'], 404);
        }

        // Add note
        $success = TicketNote::addNote($ticket_id, $this->user->id, $input['note']);
        if ($success) {
            ResponseHelper::json(['message' => 'Note added']);
        } else {
            ResponseHelper::json(['error' => 'Failed to add note'], 500);
        }
    }

    public function updateStatus($ticket_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['status'])) {
            ResponseHelper::json(['error' => 'Status is required'], 400);
        }
        $allowed = ['open', 'in_progress', 'closed'];
        if (! in_array($input['status'], $allowed)) {
            ResponseHelper::json(['error' => 'Invalid status'], 400);
        }

        $ticket = Ticket::findById($ticket_id);
        if (! $ticket) {
            ResponseHelper::json(['error' => 'Ticket not found'], 404);
        }

        // Here you can add permission checks (e.g., only agents can update status)

        $success = Ticket::updateStatus($ticket_id, $input['status']);
        if ($success) {
            ResponseHelper::json(['message' => 'Status updated']);
        } else {
            ResponseHelper::json(['error' => 'Failed to update status'], 500);
        }
    }
}
