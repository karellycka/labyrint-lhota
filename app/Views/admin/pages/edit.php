<div class="admin-header-bar">
    <h1>Upravit stránku: <?= e($page->slug) ?></h1>
    <a href="<?= adminUrl('pages') ?>" class="btn btn-secondary">Zpět na seznam</a>
</div>

<div class="form-container">
    <form method="POST" action="<?= adminUrl("pages/{$page->id}/update") ?>" class="admin-form">
        <?= csrfField() ?>

        <div class="form-tabs">
            <button type="button" class="tab-btn active" data-tab="basic">Základní nastavení</button>
            <button type="button" class="tab-btn" data-tab="widgets">Widgety (<?= count($widgets) ?>)</button>
            <button type="button" class="tab-btn" data-tab="seo">SEO</button>
        </div>

        <!-- Basic Settings Tab -->
        <div class="tab-content active" id="tab-basic">
            <div class="form-group">
                <label for="title_cs">Název stránky (Česky) *</label>
                <input type="text" id="title_cs" name="title_cs" required
                       value="<?= old('title_cs', $translations['cs']->title ?? '') ?>"
                       class="form-control" placeholder="Název stránky">
            </div>

            <div class="form-group">
                <label for="slug">Slug (URL)</label>
                <input type="text" id="slug" name="slug" value="<?= e($page->slug) ?>" class="form-control" disabled>
                <small>Slug stránky nelze měnit</small>
            </div>

            <div class="form-group">
                <label for="has_english_version">
                    <input type="checkbox" id="has_english_version" name="has_english_version" value="1"
                           <?= old('has_english_version', !empty($translations['en']->title) ? '1' : '0') ? 'checked' : '' ?>>
                    Vytvořit stránku v angličtině
                </label>
                <small>Zaškrtněte pro vytvoření anglické verze stránky</small>
            </div>

            <div id="english-fields" style="display: none;">
                <div class="form-group">
                    <label for="title_en">Název stránky (Anglicky)</label>
                    <input type="text" id="title_en" name="title_en"
                           value="<?= old('title_en', $translations['en']->title ?? '') ?>"
                           class="form-control" placeholder="Page title">
                </div>

                <div class="form-group">
                    <label for="slug_en">Slug EN (URL)</label>
                    <input type="text" id="slug_en" name="slug_en"
                           value="<?= old('slug_en', $page->slug_en ?? '') ?>"
                           class="form-control" placeholder="page-slug"
                           pattern="[a-z0-9\-]+"
                           title="Pouze malá písmena, čísla a pomlčky">
                    <small>URL adresa anglické stránky (např. "our-vision" → /en/our-vision)</small>
                </div>
            </div>

            <div class="form-group">
                <label for="published">
                    <input type="checkbox" id="published" name="published" value="1"
                           <?= old('published', $page->published ?? 0) ? 'checked' : '' ?>>
                    Publikováno
                </label>
                <small>Zaškrtněte pro okamžité zveřejnění stránky</small>
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

    // Show/hide English fields based on checkbox
    const hasEnglishCheckbox = document.getElementById('has_english_version');
    const englishFields = document.getElementById('english-fields');

    function toggleEnglishFields() {
        if (hasEnglishCheckbox.checked) {
            englishFields.style.display = 'block';
        } else {
            englishFields.style.display = 'none';
        }
    }

    // Initial state
    toggleEnglishFields();

    hasEnglishCheckbox.addEventListener('change', toggleEnglishFields);

    // Auto-generate slug EN from English title
    const titleEn = document.getElementById('title_en');
    const slugEnInput = document.getElementById('slug_en');

    if (titleEn && slugEnInput) {
        titleEn.addEventListener('input', function() {
            if (!slugEnInput.value || slugEnInput.dataset.autoGenerated === 'true') {
                const slug = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove diacritics
                    .replace(/[^a-z0-9\s\-]/g, '') // Remove special chars
                    .trim()
                    .replace(/\s+/g, '-') // Spaces to hyphens
                    .replace(/-+/g, '-'); // Multiple hyphens to single

                slugEnInput.value = slug;
                slugEnInput.dataset.autoGenerated = 'true';
            }
        });

        slugEnInput.addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
    }
});
</script>
