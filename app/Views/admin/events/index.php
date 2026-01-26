<div class="admin-header-bar">
    <h1>Events</h1>
    <a href="<?= adminUrl('events/create') ?>" class="btn btn-primary">Create New Event</a>
</div>

<?php if (empty($events)): ?>
    <div class="empty-state">
        <p>No events yet. Create your first event!</p>
        <a href="<?= adminUrl('events/create') ?>" class="btn btn-primary">Create Event</a>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title (CS)</th>
                    <th>Title (EN)</th>
                    <th>Event Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= e($event->id) ?></td>
                    <td><?= e($event->title_cs) ?></td>
                    <td><?= e($event->title_en) ?></td>
                    <td>
                        <?= formatDate($event->start_date) ?>
                        <?php if ($event->start_time): ?>
                            <br><small><?= e($event->start_time) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= e($event->location ?? '-') ?></td>
                    <td class="actions">
                        <a href="<?= adminUrl("events/{$event->id}/edit") ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="<?= adminUrl("events/{$event->id}/delete") ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <?= csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
