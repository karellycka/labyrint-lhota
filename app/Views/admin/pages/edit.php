<div class="admin-header-bar">
    <h1>Upravit stránku: <?= e($page->slug) ?></h1>
    <a href="<?= adminUrl('pages') ?>" class="btn btn-secondary">Zpět na seznam</a>
</div>

<div class="form-container">
    <form method="POST" action="<?= adminUrl("pages/{$page->id}/update") ?>" class="admin-form">
        <?= csrfField() ?>

        <div class="form-tabs">
            <button type="button" class="tab-btn active" data-tab="cs">Čeština (CS)</button>
            <button type="button" class="tab-btn" data-tab="en">Angličtina (EN)</button>
            <button type="button" class="tab-btn" data-tab="seo">SEO</button>
            <button type="button" class="tab-btn" data-tab="widgets">Widgety (<?= count($widgets) ?>)</button>
        </div>

        <!-- Czech Tab -->
        <div class="tab-content active" id="tab-cs">
            <div class="form-group">
                <label for="title_cs">Název stránky (Česky) *</label>
                <input type="text" id="title_cs" name="title_cs" required
                       value="<?= old('title_cs', $translations['cs']->title ?? '') ?>"
                       class="form-control" placeholder="Název stránky">
            </div>

            <div class="form-group">
                <label for="content_cs">Obsah (Česky) *</label>
                <textarea id="content_cs" name="content_cs" rows="15" required class="form-control editor"><?= old('content_cs', $translations['cs']->content ?? '') ?></textarea>
                <small>Můžete používat HTML tagy pro formátování</small>
            </div>
        </div>

        <!-- English Tab -->
        <div class="tab-content" id="tab-en">
            <div class="form-group">
                <label for="title_en">Page Title (English) *</label>
                <input type="text" id="title_en" name="title_en" required
                       value="<?= old('title_en', $translations['en']->title ?? '') ?>"
                       class="form-control" placeholder="Page title">
            </div>

            <div class="form-group">
                <label for="content_en">Content (English) *</label>
                <textarea id="content_en" name="content_en" rows="15" required class="form-control editor"><?= old('content_en', $translations['en']->content ?? '') ?></textarea>
                <small>You can use HTML tags for formatting</small>
            </div>
        </div>

        <!-- SEO Tab -->
        <div class="tab-content" id="tab-seo">
            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3" class="form-control"
                          placeholder="Krátký popis stránky pro vyhledávače (150-160 znaků)"><?= old('meta_description', $page->meta_description ?? '') ?></textarea>
                <small>Doporučená délka: 150-160 znaků</small>
            </div>

            <div class="form-group">
                <label for="meta_keywords">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords"
                       value="<?= old('meta_keywords', $page->meta_keywords ?? '') ?>"
                       class="form-control" placeholder="klíčová, slova, oddělená, čárkou">
                <small>Klíčová slova oddělená čárkou</small>
            </div>

            <div class="form-group">
                <label>Slug</label>
                <input type="text" value="<?= e($page->slug) ?>" class="form-control" disabled>
                <small>Slug stránky nelze měnit</small>
            </div>
        </div>

        <!-- Widgets Tab -->
        <div class="tab-content" id="tab-widgets">
            <?php include __DIR__ . '/widgets-tab.php'; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Uložit změny</button>
            <a href="<?= adminUrl('pages') ?>" class="btn btn-secondary">Zrušit</a>
        </div>
    </form>
</div>

<script>
// Tab switching functionality with URL hash persistence
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    function activateTab(tabName) {
        // Remove active class from all tabs
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));

        // Add active class to selected tab
        const targetBtn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
        const targetContent = document.getElementById('tab-' + tabName);

        if (targetBtn && targetContent) {
            targetBtn.classList.add('active');
            targetContent.classList.add('active');
        }
    }

    // Check URL hash on page load and restore active tab
    if (window.location.hash) {
        const tabName = window.location.hash.substring(1); // Remove #
        activateTab(tabName);
    }

    // Handle tab clicks
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            window.location.hash = tabName; // Update URL hash
            activateTab(tabName);
        });
    });
});
</script>
