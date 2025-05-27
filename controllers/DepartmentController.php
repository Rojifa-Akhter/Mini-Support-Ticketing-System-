<?php
// controllers/DepartmentController.php
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';

class DepartmentController
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    private function authorizeAdmin()
    {
        if ($this->user->role !== 'admin') {
            ResponseHelper::json(['status' => false, 'error' => 'Only admins allowed'], 403);
        }
    }

    public function index()
    {
        $departments = Department::all();
        ResponseHelper::json(['status' => true, 'data' => $departments]);
    }

    public function create()
    {
        $this->authorizeAdmin();
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['name'])) {
            ResponseHelper::json(['status' => false, 'error' => 'Name required'], 400);
        }

        $department = Department::create($input['name']);
        ResponseHelper::json(['status' => true, 'message' => 'Department created', 'department' => $department]);
    }

    public function update($id)
    {
        $this->authorizeAdmin();
        $input = json_decode(file_get_contents('php://input'), true);
        if (! isset($input['name'])) {
            ResponseHelper::json(['status' => false, 'error' => 'Name required'], 400);
        }
        $success = Department::update($id, $input['name']);
        if ($success) {
            ResponseHelper::json(['status' => true, 'message' => 'Department updated']);
        } else {
            ResponseHelper::json(['status' => false, 'error' => 'Update failed : ID not Found'], 500);
        }
    }

    public function delete($id)
    {
        $this->authorizeAdmin();
        $success = Department::delete($id);
        if ($success) {
            ResponseHelper::json(['status' => true, 'message' => 'Department deleted']);
        } else {
            ResponseHelper::json(['status' => false, 'error' => 'Delete failed: ID not found'], 404);
        }
    }
}
