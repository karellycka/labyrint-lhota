<div class="admin-header-bar">
    <h1><?= $quote ? 'Upravit citaci' : 'Nová citace' ?></h1>
    <a href="<?= adminUrl('quotes') ?>" class="btn btn-secondary">Zpět na seznam</a>
</div>

<div class="form-container">
    <form method="POST" action="<?= $quote ? adminUrl("quotes/{$quote->id}/update") : adminUrl('quotes/store') ?>" class="admin-form">
        <?= csrfField() ?>

        <div class="form-tabs">
            <button type="button" class="tab-btn active" data-tab="cs">Čeština (CS)</button>
            <button type="button" class="tab-btn" data-tab="en">Angličtina (EN)</button>
            <button type="button" class="tab-btn" data-tab="settings">Nastavení</button>
        </div>

        <!-- Czech Tab -->
        <div class="tab-content active" id="tab-cs">
            <div class="form-group">
                <label for="text_cs">Text citace (Česky) *</label>
                <textarea id="text_cs" name="text_cs" rows="4" required class="form-control"><?= old('text_cs', $translations['cs']->text ?? '') ?></textarea>
                <small>Zadejte text citace v češtině</small>
            </div>

            <div class="form-group">
                <label for="author_cs">Autor (Česky) *</label>
                <input type="text" id="author_cs" name="author_cs" required value="<?= old('author_cs', $translations['cs']->author ?? '') ?>" class="form-control" placeholder="Jméno autora">
            </div>

            <div class="form-group">
                <label for="role_cs">Role/Pozice (Česky)</label>
                <input type="text" id="role_cs" name="role_cs" value="<?= old('role_cs', $translations['cs']->role ?? '') ?>" class="form-control" placeholder="např. ředitelka Labyrintu">
                <small>Volitelné - např. "ředitelka školy", "učitelka"</small>
            </div>
        </div>

        <!-- English Tab -->
        <div class="tab-content" id="tab-en">
            <div class="form-group">
                <label for="text_en">Quote Text (English) *</label>
                <textarea id="text_en" name="text_en" rows="4" required class="form-control"><?= old('text_en', $translations['en']->text ?? '') ?></textarea>
                <small>Enter the quote text in English</small>
            </div>

            <div class="form-group">
                <label for="author_en">Author (English) *</label>
                <input type="text" id="author_en" name="author_en" required value="<?= old('author_en', $translations['en']->author ?? '') ?>" class="form-control" placeholder="Author name">
            </div>

            <div class="form-group">
                <label for="role_en">Role/Position (English)</label>
                <input type="text" id="role_en" name="role_en" value="<?= old('role_en', $translations['en']->role ?? '') ?>" class="form-control" placeholder="e.g., Director of Labyrinth">
                <small>Optional - e.g., "School Director", "Teacher"</small>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-content" id="tab-settings">
            <div class="form-group">
                <label for="display_order">Pořadí zobrazení</label>
                <input type="number" id="display_order" name="display_order" value="<?= old('display_order', $quote->display_order ?? 0) ?>" class="form-control" min="0">
                <small>Nižší číslo = vyšší priorita. Při stejném pořadí se zobrazí nejnovější.</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" <?= old('is_active', $quote->is_active ?? 1) ? 'checked' : '' ?>>
                    Aktivní (zobrazovat na webu)
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $quote ? 'Uložit změny' : 'Vytvořit citaci' ?></button>
            <a href="<?= adminUrl('quotes') ?>" class="btn btn-secondary">Zrušit</a>
        </div>
    </form>
</div>

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

.form-container {
    background: white;
    border-radius: var(--border-radius, 8px);
    box-shadow: var(--shadow);
    padding: 30px;
}

.admin-form {
    max-width: 100%;
}

.form-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e1e8ed;
}

.tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 15px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all 0.3s;
}

.tab-btn:hover {
    color: #333;
}

.tab-btn.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e8ed;
    border-radius: var(--border-radius, 8px);
    font-size: 15px;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

.form-group small {
    display: block;
    margin-top: 5px;
    font-size: 13px;
    color: #666;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e1e8ed;
    display: flex;
    gap: 10px;
}
</style>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');

            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        });
    });
});
</script>
