<?php

namespace App\Models;

use App\Core\Model;

/**
 * Event Model - Calendar events with translations
 */
class Event extends Model
{
    protected string $table = 'events';

    /**
     * Get upcoming events
     */
    public function getUpcoming(string $language, int $limit = null): array
    {
        $sql = "
            SELECT e.*, et.title, et.description
            FROM events e
            JOIN event_translations et ON e.id = et.event_id
            WHERE e.published = 1
              AND e.start_date >= CURDATE()
              AND et.language = ?
            ORDER BY e.start_date ASC, e.start_time ASC
        ";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->query($sql, [$language, $limit]);
        }

        return $this->query($sql, [$language]);
    }

    /**
     * Get past events
     */
    public function getPast(string $language, int $limit = 10): array
    {
        $sql = "
            SELECT e.*, et.title
            FROM events e
            JOIN event_translations et ON e.id = et.event_id
            WHERE e.published = 1
              AND e.start_date < CURDATE()
              AND et.language = ?
            ORDER BY e.start_date DESC
            LIMIT ?
        ";

        return $this->query($sql, [$language, $limit]);
    }

    /**
     * Get events for specific month
     */
    public function getByMonth(string $year, string $month, string $language): array
    {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $sql = "
            SELECT e.*, et.title, et.description
            FROM events e
            JOIN event_translations et ON e.id = et.event_id
            WHERE e.published = 1
              AND e.start_date BETWEEN ? AND ?
              AND et.language = ?
            ORDER BY e.start_date ASC, e.start_time ASC
        ";

        return $this->query($sql, [$startDate, $endDate, $language]);
    }

    /**
     * Find event by slug
     */
    public function findBySlug(string $slug, string $language): ?object
    {
        $sql = "
            SELECT e.*, et.title, et.description
            FROM events e
            JOIN event_translations et ON e.id = et.event_id
            WHERE e.slug = ?
              AND et.language = ?
              AND e.published = 1
            LIMIT 1
        ";

        return $this->querySingle($sql, [$slug, $language]);
    }

    /**
     * Get event translations
     */
    public function getTranslations(int $eventId): array
    {
        $sql = "SELECT * FROM event_translations WHERE event_id = ?";
        return $this->query($sql, [$eventId]);
    }

    /**
     * Update event translation
     */
    public function updateTranslation(int $eventId, string $language, array $data): bool
    {
        $sql = "
            INSERT INTO event_translations (event_id, language, title, description)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description)
        ";

        return $this->execute($sql, [
            $eventId,
            $language,
            $data['title'] ?? '',
            $data['description'] ?? ''
        ]);
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(object $event): bool
    {
        return strtotime($event->start_date) >= strtotime('today');
    }

    /**
     * Check if event is today
     */
    public function isToday(object $event): bool
    {
        return $event->start_date === date('Y-m-d');
    }
}
