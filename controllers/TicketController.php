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
            ResponseHelper::json(['status' => false, 'error' => 'Missing fields'], 400);
        }

        // Create ticket
        $ticket = Ticket::create($input['title'], $input['description'], $this->user->id, $input['department_id']);
        ResponseHelper::json(['status' => true, 'message' => 'Ticket created', 'ticket' => $ticket]);
    }

    public function addNote($ticket_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['note'])) {
            ResponseHelper::json(['status' => false, 'error' => 'Note is required'], 400);
        }

        // Verify ticket exists
        $ticket = Ticket::findById($ticket_id);
        if (! $ticket) {
            ResponseHelper::json(['status' => false, 'error' => 'Ticket not found'], 404);
        }

        // Add note
        $success = TicketNote::addNote($ticket_id, $this->user->id, $input['note']);
        if ($success) {
            ResponseHelper::json(['status' => true, 'message' => 'Note added']);
        } else {
            ResponseHelper::json(['status' => false, 'error' => 'Failed to add note'], 500);
        }
    }
    public function assignAgent($ticket_id)
    {
        if ($this->user->role !== 'agent') {
            ResponseHelper::json(['status' => false, 'error' => 'Only agents can assign tickets'], 403);
        }

        $ticket = Ticket::findById($ticket_id);
        if (! $ticket) {
            ResponseHelper::json(['status' => false, 'error' => 'Ticket not found'], 404);
        }

        $success = Ticket::assignToAgent($ticket_id, $this->user->id);
        if ($success) {
            ResponseHelper::json(['status' => true, 'message' => 'Ticket assigned']);
        } else {
            ResponseHelper::json(['status' => false, 'error' => 'Failed to assign ticket'], 500);
        }
    }

    public function updateStatus($ticket_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['status'])) {
            ResponseHelper::json(['status' => false, 'error' => 'Status is required'], 400);
        }
        $allowed = ['open', 'in_progress', 'closed'];
        if (! in_array($input['status'], $allowed)) {
            ResponseHelper::json(['status' => false, 'error' => 'Invalid status'], 400);
        }

        $ticket = Ticket::findById($ticket_id);
        if (! $ticket) {
            ResponseHelper::json(['status' => false, 'error' => 'Ticket not found'], 401);
        }

        // only agents can update status)

        $success = Ticket::updateStatus($ticket_id, $input['status']);
        if ($success) {
            ResponseHelper::json(['status' => true, 'message' => 'Status updated']);
        } else {
            ResponseHelper::json(['status' => false, 'error' => 'Failed to update status'], 500);
        }
    }
}
