<div class="admin-header-bar">
    <h1>Edit Media</h1>
    <a href="<?= adminUrl('media') ?>" class="btn btn-secondary">Back to Media Library</a>
</div>

<div class="media-edit-container">
    <div class="media-edit-grid">
        <!-- Image Preview -->
        <div class="preview-section">
            <h3>Preview</h3>
            <div class="image-preview">
                <img src="<?= e($media->filename) ?>" alt="<?= e($media->original_name) ?>">
            </div>
            <div class="image-details">
                <p><strong>Original name:</strong> <?= e($media->original_name) ?></p>
                <p><strong>Dimensions:</strong> <?= e($media->width) ?>×<?= e($media->height) ?> px</p>
                <p><strong>File size:</strong> <?= round($media->file_size / 1024, 1) ?> KB</p>
                <p><strong>Type:</strong> <?= e($media->mime_type) ?></p>
                <?php if (!empty($media->cloudinary_public_id)): ?>
                    <p><strong>Cloudinary ID:</strong> <code><?= e($media->cloudinary_public_id) ?></code></p>
                <?php endif; ?>
                <p><strong>Uploaded:</strong> <?= date('d.m.Y H:i', strtotime($media->created_at)) ?></p>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="form-section">
            <h3>Metadata</h3>
            <form method="POST" action="<?= adminUrl("media/{$media->id}/update") ?>" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::generateCSRFToken() ?>">

                <div class="form-tabs">
                    <button type="button" class="tab-btn active" data-tab="cs">Čeština (CS)</button>
                    <button type="button" class="tab-btn" data-tab="en">English (EN)</button>
                </div>

                <!-- Czech Tab -->
                <div class="tab-content active" id="tab-cs">
                    <div class="form-group">
                        <label for="caption_cs">Caption (Czech)</label>
                        <input type="text" id="caption_cs" name="caption_cs"
                               value="<?= e($translations['cs']->caption ?? '') ?>"
                               class="form-control" placeholder="Popisek obrázku">
                    </div>

                    <div class="form-group">
                        <label for="alt_cs">Alt Text (Czech)</label>
                        <input type="text" id="alt_cs" name="alt_cs"
                               value="<?= e($translations['cs']->alt_text ?? '') ?>"
                               class="form-control" placeholder="Alternativní text pro přístupnost">
                        <small>Alt text is important for accessibility and SEO</small>
                    </div>
                </div>

                <!-- English Tab -->
                <div class="tab-content" id="tab-en">
                    <div class="form-group">
                        <label for="caption_en">Caption (English)</label>
                        <input type="text" id="caption_en" name="caption_en"
                               value="<?= e($translations['en']->caption ?? '') ?>"
                               class="form-control" placeholder="Image caption">
                    </div>

                    <div class="form-group">
                        <label for="alt_en">Alt Text (English)</label>
                        <input type="text" id="alt_en" name="alt_en"
                               value="<?= e($translations['en']->alt_text ?? '') ?>"
                               class="form-control" placeholder="Alternative text for accessibility">
                        <small>Alt text is important for accessibility and SEO</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= adminUrl('media') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;

        // Update buttons
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById('tab-' + tabName).classList.add('active');
    });
});
</script>
