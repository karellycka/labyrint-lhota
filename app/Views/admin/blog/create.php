<div class="admin-header-bar">
    <h1>Create Blog Post</h1>
    <a href="<?= adminUrl('blog') ?>" class="btn btn-secondary">Back to List</a>
</div>

<div class="form-container">
    <form method="POST" action="<?= adminUrl('blog/store') ?>" class="admin-form">
        <?= csrfField() ?>

        <div class="form-tabs">
            <button type="button" class="tab-btn active" data-tab="cs">Czech (CS)</button>
            <button type="button" class="tab-btn" data-tab="en">English (EN)</button>
            <button type="button" class="tab-btn" data-tab="meta">Metadata</button>
        </div>

        <!-- Czech Tab -->
        <div class="tab-content active" id="tab-cs">
            <div class="form-group">
                <label for="title_cs">Title (Czech) *</label>
                <input type="text" id="title_cs" name="title_cs" required value="<?= old('title_cs') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="excerpt_cs">Excerpt (Czech)</label>
                <textarea id="excerpt_cs" name="excerpt_cs" rows="3" class="form-control"><?= old('excerpt_cs') ?></textarea>
                <small>Short summary for listing pages</small>
            </div>

            <div class="form-group">
                <label for="content_cs">Content (Czech) *</label>
                <div id="editor_cs" style="height: 400px;"></div>
                <textarea id="content_cs" name="content_cs" required style="display:none;"><?= old('content_cs') ?></textarea>
            </div>
        </div>

        <!-- English Tab -->
        <div class="tab-content" id="tab-en">
            <div class="form-group">
                <label for="title_en">Title (English) *</label>
                <input type="text" id="title_en" name="title_en" required value="<?= old('title_en') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="excerpt_en">Excerpt (English)</label>
                <textarea id="excerpt_en" name="excerpt_en" rows="3" class="form-control"><?= old('excerpt_en') ?></textarea>
                <small>Short summary for listing pages</small>
            </div>

            <div class="form-group">
                <label for="content_en">Content (English) *</label>
                <div id="editor_en" style="height: 400px;"></div>
                <textarea id="content_en" name="content_en" required style="display:none;"><?= old('content_en') ?></textarea>
            </div>
        </div>

        <!-- Metadata Tab -->
        <div class="tab-content" id="tab-meta">
            <div class="form-group">
                <label for="featured_image">Featured Image URL</label>
                <input type="text" id="featured_image" name="featured_image" value="<?= old('featured_image') ?>" class="form-control" placeholder="/uploads/image.jpg">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Description (SEO)</label>
                <textarea id="meta_description" name="meta_description" rows="2" class="form-control"><?= old('meta_description') ?></textarea>
                <small>Recommended: 150-160 characters</small>
            </div>

            <div class="form-group">
                <label for="meta_keywords">Meta Keywords (SEO)</label>
                <input type="text" id="meta_keywords" name="meta_keywords" value="<?= old('meta_keywords') ?>" class="form-control" placeholder="keyword1, keyword2, keyword3">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="published" value="1" <?= old('published') ? 'checked' : '' ?>>
                    Publish immediately
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Post</button>
            <a href="<?= adminUrl('blog') ?>" class="btn btn-secondary">Cancel</a>
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
    max-width: 900px;
}

.admin-form {
    width: 100%;
}

.form-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e1e8ed;
}

.tab-btn {
    padding: 12px 24px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    color: #666;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.3s;
}

.tab-btn:hover {
    color: #667eea;
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
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e8ed;
    border-radius: var(--border-radius, 8px);
    font-size: 15px;
    font-family: inherit;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 13px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: normal !important;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #e1e8ed;
    display: flex;
    gap: 15px;
}
</style>

<!-- Quill WYSIWYG Editor (Open Source, No Limits) -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Tab switching
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            // Remove active class from all
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active class to current
            this.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        });
    });

    // Initialize Quill editors
    const quillCS = new Quill('#editor_cs', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        },
        placeholder: 'Napište obsah článku...'
    });

    const quillEN = new Quill('#editor_en', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        },
        placeholder: 'Write article content...'
    });

    // Load initial content if exists
    const contentCS = document.getElementById('content_cs').value;
    const contentEN = document.getElementById('content_en').value;
    if (contentCS) quillCS.root.innerHTML = contentCS;
    if (contentEN) quillEN.root.innerHTML = contentEN;

    // Sync Quill content to hidden textarea on submit
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('content_cs').value = quillCS.root.innerHTML;
        document.getElementById('content_en').value = quillEN.root.innerHTML;
    });
});
</script>

<style>
/* Quill editor styling */
.ql-container {
    font-size: 16px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.ql-editor {
    min-height: 300px;
}

.ql-toolbar {
    background: #f8f9fa;
    border-radius: var(--border-radius, 8px) var(--border-radius, 8px) 0 0;
}

.ql-container {
    border-radius: 0 0 var(--border-radius, 8px) var(--border-radius, 8px);
}
</style>
