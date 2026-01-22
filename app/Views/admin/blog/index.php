<div class="admin-header-bar">
    <h1>Blog Posts</h1>
    <a href="<?= adminUrl('blog/create') ?>" class="btn btn-primary">Create New Post</a>
</div>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <p>No blog posts yet. Create your first post!</p>
        <a href="<?= adminUrl('blog/create') ?>" class="btn btn-primary">Create Post</a>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title (CS)</th>
                    <th>Title (EN)</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= e($post->id) ?></td>
                    <td><?= e($post->title_cs) ?></td>
                    <td><?= e($post->title_en) ?></td>
                    <td><?= e($post->author_name) ?></td>
                    <td>
                        <span class="badge <?= $post->published ? 'badge-success' : 'badge-warning' ?>">
                            <?= $post->published ? 'Published' : 'Draft' ?>
                        </span>
                    </td>
                    <td><?= e($post->views) ?></td>
                    <td><?= formatDateTime($post->created_at) ?></td>
                    <td class="actions">
                        <a href="<?= adminUrl("blog/{$post->id}/edit") ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="<?= adminUrl("blog/{$post->id}/delete") ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
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

<style>
.admin-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e1e8ed;
}

.admin-header-bar h1 {
    margin: 0;
    font-size: 28px;
    color: #333;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: var(--border-radius, 8px);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: #f8f9fa;
    border-radius: var(--border-radius, 8px);
    margin: 20px 0;
}

.empty-state p {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

.admin-table-container {
    background: white;
    border-radius: var(--border-radius, 8px);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: #f8f9fa;
}

.admin-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e1e8ed;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #e1e8ed;
}

.admin-table tbody tr:hover {
    background: #f8f9fa;
}

.admin-table .actions {
    white-space: nowrap;
}

.badge {
    padding: 4px 10px;
    border-radius: var(--border-radius, 8px);
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}
</style>
