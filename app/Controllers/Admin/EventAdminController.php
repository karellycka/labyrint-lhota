<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Event;

/**
 * Admin Event Controller - CRUD for events
 */
class EventAdminController extends Controller
{
    private Event $eventModel;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $this->eventModel = new Event();
    }

    /**
     * List all events
     */
    public function index(): void
    {
        $events = $this->eventModel->query("
            SELECT
                e.*,
                et_cs.title as title_cs,
                et_en.title as title_en
            FROM events e
            LEFT JOIN event_translations et_cs ON e.id = et_cs.event_id AND et_cs.language = 'cs'
            LEFT JOIN event_translations et_en ON e.id = et_en.event_id AND et_en.language = 'en'
            ORDER BY e.event_date DESC
        ");

        $this->view('admin/events/index', [
            'title' => 'Events',
            'events' => $events
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/events/create', [
            'title' => 'Create Event'
        ], 'admin');
    }

    /**
     * Store new event
     */
    public function store(): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('events/create'));
        }

        try {
            $this->db->beginTransaction();

            $eventId = $this->eventModel->insert([
                'slug' => generateSlug($_POST['title_cs'], 'cs'),
                'event_date' => $_POST['event_date'],
                'event_time' => $_POST['event_time'] ?? null,
                'location' => $_POST['location'] ?? null,
                'image' => $_POST['image'] ?? null
            ]);

            // Insert translations
            foreach (['cs', 'en'] as $lang) {
                $this->eventModel->execute("
                    INSERT INTO event_translations (event_id, language, title, description)
                    VALUES (?, ?, ?, ?)
                ", [
                    $eventId,
                    $lang,
                    $_POST["title_{$lang}"],
                    $_POST["description_{$lang}"] ?? null
                ]);
            }

            $this->db->commit();
            Session::flash('success', 'Event created successfully');
            redirect(adminUrl('events'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            redirect(adminUrl('events/create'));
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $event = $this->eventModel->find($id);
        if (!$event) {
            Session::flash('error', 'Event not found');
            redirect(adminUrl('events'));
        }

        $translations = $this->eventModel->query("
            SELECT language, title, description
            FROM event_translations WHERE event_id = ?
        ", [$id]);

        $translationsByLang = [];
        foreach ($translations as $trans) {
            $translationsByLang[$trans->language] = $trans;
        }

        $this->view('admin/events/edit', [
            'title' => 'Edit Event',
            'event' => $event,
            'translations' => $translationsByLang
        ], 'admin');
    }

    /**
     * Update event
     */
    public function update(int $id): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl("events/{$id}/edit"));
        }

        try {
            $this->db->beginTransaction();

            $this->eventModel->update($id, [
                'event_date' => $_POST['event_date'],
                'event_time' => $_POST['event_time'] ?? null,
                'location' => $_POST['location'] ?? null,
                'image' => $_POST['image'] ?? null
            ]);

            foreach (['cs', 'en'] as $lang) {
                $this->eventModel->execute("
                    UPDATE event_translations
                    SET title = ?, description = ?
                    WHERE event_id = ? AND language = ?
                ", [
                    $_POST["title_{$lang}"],
                    $_POST["description_{$lang}"] ?? null,
                    $id,
                    $lang
                ]);
            }

            $this->db->commit();
            Session::flash('success', 'Event updated successfully');
            redirect(adminUrl('events'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            redirect(adminUrl("events/{$id}/edit"));
        }
    }

    /**
     * Delete event
     */
    public function delete(int $id): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('events'));
        }

        try {
            $this->eventModel->delete($id);
            Session::flash('success', 'Event deleted successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        redirect(adminUrl('events'));
    }
}
