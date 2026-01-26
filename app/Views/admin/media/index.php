<div class="admin-header-bar">
    <h1>Media Library</h1>
    <button id="uploadBtn" class="btn btn-primary">+ Upload Media</button>
</div>

<!-- Upload Form (hidden by default) -->
<div id="uploadForm" style="display: none; margin-bottom: 2rem; padding: 1.5rem; background: #f5f5f5; border-radius: 8px;">
    <h3 style="margin-top: 0;">Upload New Media</h3>
    <form id="mediaUploadForm" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::generateCSRFToken() ?>">
        <div style="margin-bottom: 1rem;">
            <input type="file" name="file" id="fileInput" accept="image/jpeg,image/png,image/gif,image/webp" required style="padding: 0.5rem;">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <button type="button" id="cancelUpload" class="btn btn-secondary">Cancel</button>
        </div>
        <div id="uploadProgress" style="display: none; margin-top: 1rem;">
            <div style="background: #e0e0e0; height: 20px; border-radius: 4px; overflow: hidden;">
                <div id="progressBarFill" style="width: 0%; height: 100%; background: #667eea; transition: width 0.3s;"></div>
            </div>
            <p id="uploadStatus" style="margin-top: 0.5rem; font-size: 0.9rem;"></p>
        </div>
    </form>
</div>

<?php if (empty($media)): ?>
    <div class="empty-state">
        <p>No media files yet. Upload your first image!</p>
    </div>
<?php else: ?>
    <div class="media-grid">
        <?php foreach ($media as $item): ?>
        <div class="media-item">
            <div class="media-preview">
                <img src="<?= e($item->filename) ?>" alt="<?= e($item->alt_cs ?? $item->original_name) ?>">
            </div>
            <div class="media-info">
                <h4 title="<?= e($item->original_name) ?>">
                    <?= e($item->title_cs ?? $item->original_name) ?>
                </h4>
                <div class="media-meta">
                    <?= e($item->width) ?>×<?= e($item->height) ?> px
                    | <?= round($item->file_size / 1024, 1) ?> KB
                </div>
                <?php if (!empty($item->cloudinary_public_id)): ?>
                    <div class="media-badge">
                        <span class="badge badge-cloudinary">Cloudinary</span>
                    </div>
                <?php endif; ?>
                <div class="media-actions">
                    <a href="<?= adminUrl("media/{$item->id}/edit") ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <button onclick="copyUrl('<?= e($item->filename) ?>')" class="btn btn-sm btn-secondary">Copy URL</button>
                    <form method="POST" action="<?= adminUrl("media/{$item->id}/delete") ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this media file?');">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::generateCSRFToken() ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
// Upload form toggle
document.getElementById('uploadBtn').addEventListener('click', function() {
    document.getElementById('uploadForm').style.display = 'block';
});

document.getElementById('cancelUpload').addEventListener('click', function() {
    document.getElementById('uploadForm').style.display = 'none';
    document.getElementById('mediaUploadForm').reset();
    document.getElementById('uploadProgress').style.display = 'none';
});

// Upload handling
document.getElementById('mediaUploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBarFill');
    const statusText = document.getElementById('uploadStatus');

    progressDiv.style.display = 'block';
    statusText.textContent = 'Uploading...';
    statusText.style.color = '#333';

    try {
        const response = await fetch('<?= adminUrl('media/upload') ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (response.ok && result.success) {
            statusText.textContent = 'Upload successful! Reloading...';
            statusText.style.color = '#28a745';
            progressBar.style.width = '100%';
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(result.error || 'Upload failed');
        }
    } catch (error) {
        statusText.textContent = 'Error: ' + error.message;
        statusText.style.color = '#dc3545';
    }
});

// Copy URL to clipboard
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('URL copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = url;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('URL copied to clipboard!');
    });
}
</script>
