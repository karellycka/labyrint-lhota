<div class="admin-header-bar">
    <h1>Stránky</h1>
    <a href="<?= adminUrl('pages/create') ?>" class="btn btn-primary">+ Vytvořit stránku</a>
</div>

<?php if (empty($pages)): ?>
    <div class="empty-state">
        <p>Zatím nemáte žádné stránky.</p>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Slug</th>
                    <th>Název (CS)</th>
                    <th>Název (EN)</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                <tr>
                    <td><?= e($page->id) ?></td>
                    <td><code><?= e($page->slug) ?></code></td>
                    <td><?= e($page->title_cs ?? '-') ?></td>
                    <td><?= e($page->title_en ?? '-') ?></td>
                    <td class="actions">
                        <a href="<?= adminUrl("pages/{$page->id}/edit") ?>" class="btn btn-sm btn-secondary">Upravit</a>
                        <?php if ($page->slug !== 'home'): ?>
                        <form method="POST" action="<?= adminUrl("pages/{$page->id}/delete") ?>" style="display: inline;" onsubmit="return confirm('Opravdu chcete smazat tuto stránku?');">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Smazat</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
