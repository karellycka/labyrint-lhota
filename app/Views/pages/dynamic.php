<?php
/**
 * Dynamic Page Template
 * Renders pages composed of widgets from database
 */

use App\Services\WidgetRenderer;

// Initialize widget renderer
$widgetRenderer = new WidgetRenderer();
?>

<?php if (!empty($widgets)): ?>
    <?php foreach ($widgets as $widget): ?>
        <?= $widgetRenderer->render($widget) ?>
    <?php endforeach; ?>
<?php else: ?>
    <!-- Empty page - no widgets -->
    <section class="section">
        <div class="container">
            <div class="empty-page" style="text-align: center; padding: 80px 20px;">
                <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
                    <p style="color: #999; font-size: 18px;">
                        Tato stránka zatím nemá žádný obsah.<br>
                        <small>Přidejte widgety v administraci: <a href="<?= adminUrl("pages/{$page->id}/edit") ?>">Upravit stránku</a></small>
                    </p>
                <?php else: ?>
                    <p style="color: #999; font-size: 18px;">
                        Tato stránka je momentálně ve výstavbě.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
