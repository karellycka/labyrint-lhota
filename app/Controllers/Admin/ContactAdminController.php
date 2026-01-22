<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;

/**
 * Admin Contact Controller - Manage contact form submissions
 */
class ContactAdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }
    }

    /**
     * List all contact submissions
     */
    public function index(): void
    {
        $submissions = $this->db->query("
            SELECT *
            FROM contact_submissions
            ORDER BY created_at DESC
        ");

        $this->view('admin/contact/index', [
            'title' => 'Contact Submissions',
            'submissions' => $submissions
        ], 'admin');
    }

    /**
     * View single submission
     */
    public function view(int $id): void
    {
        $submission = $this->db->querySingle("
            SELECT * FROM contact_submissions WHERE id = ?
        ", [$id]);

        if (!$submission) {
            Session::flash('error', 'Submission not found');
            redirect(adminUrl('contact'));
        }

        // Mark as read
        if (!$submission->is_read) {
            $this->db->execute("
                UPDATE contact_submissions SET is_read = 1 WHERE id = ?
            ", [$id]);
        }

        $this->view('admin/contact/view', [
            'title' => 'Contact Submission',
            'submission' => $submission
        ], 'admin');
    }

    /**
     * Mark as read
     */
    public function markRead(int $id): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('contact'));
        }

        try {
            $this->db->execute("
                UPDATE contact_submissions SET is_read = 1 WHERE id = ?
            ", [$id]);

            Session::flash('success', 'Marked as read');
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        redirect(adminUrl('contact'));
    }
}
