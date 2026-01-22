<div class="page-header">
    <h1>Dashboard</h1>
    <div>
        <a href="<?= url('', 'cs') ?>" class="btn btn-primary" target="_blank">View Site</a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Blog Posts</h3>
        <div class="stat"><?= $stats['total_posts'] ?></div>
        <p><?= $stats['published_posts'] ?> published</p>
    </div>

    <div class="dashboard-card">
        <h3>Events</h3>
        <div class="stat"><?= $stats['total_events'] ?></div>
        <p><?= $stats['upcoming_events'] ?> upcoming</p>
    </div>

    <div class="dashboard-card">
        <h3>Media Files</h3>
        <div class="stat"><?= $stats['total_media'] ?></div>
        <p>Total uploads</p>
    </div>

    <div class="dashboard-card">
        <h3>Pages</h3>
        <div class="stat"><?= $stats['total_pages'] ?></div>
        <p>Static pages</p>
    </div>
</div>

<!-- Recent Posts -->
<div style="margin-bottom: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Recent Blog Posts</h2>
        <a href="<?= url('admin/blog', 'cs') ?>" class="btn btn-primary btn-sm">View All</a>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentPosts)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                        No blog posts yet. <a href="<?= url('admin/blog/create', 'cs') ?>">Create your first post</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentPosts as $post): ?>
                    <tr>
                        <td><strong><?= e($post->title) ?></strong></td>
                        <td><?= e($post->author_name) ?></td>
                        <td>
                            <?php if ($post->published): ?>
                                <span class="badge badge-success">Published</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($post->created_at, 'd.m.Y H:i') ?></td>
                        <td>
                            <a href="<?= url('admin/blog/' . $post->id . '/edit', 'cs') ?>" class="btn btn-primary btn-sm">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Upcoming Events -->
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Upcoming Events</h2>
        <a href="<?= url('admin/events', 'cs') ?>" class="btn btn-primary btn-sm">View All</a>
    </div>

    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($upcomingEvents)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                        No upcoming events. <a href="<?= url('admin/events/create', 'cs') ?>">Create a new event</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                    <tr>
                        <td><strong><?= e($event->title) ?></strong></td>
                        <td><?= formatDate($event->start_date) ?></td>
                        <td>
                            <?php if ($event->start_time): ?>
                                <?= date('H:i', strtotime($event->start_time)) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= e($event->location ?? '-') ?></td>
                        <td>
                            <a href="<?= url('admin/events/' . $event->id . '/edit', 'cs') ?>" class="btn btn-primary btn-sm">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
