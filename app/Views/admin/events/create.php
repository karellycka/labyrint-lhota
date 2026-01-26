<div class="admin-header-bar">
    <h1>Create Event</h1>
    <a href="<?= adminUrl('events') ?>" class="btn btn-secondary">Back to List</a>
</div>

<div class="form-container">
    <form method="POST" action="<?= adminUrl('events/store') ?>" class="admin-form">
        <?= csrfField() ?>

        <div class="form-tabs">
            <button type="button" class="tab-btn active" data-tab="basic">Basic Info</button>
            <button type="button" class="tab-btn" data-tab="cs">Czech (CS)</button>
            <button type="button" class="tab-btn" data-tab="en">English (EN)</button>
        </div>

        <!-- Basic Info Tab -->
        <div class="tab-content active" id="tab-basic">
            <div class="form-group">
                <label for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" required value="<?= old('start_date') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?= old('end_date') ?>" class="form-control">
                <small>Optional - leave blank for single-day events</small>
            </div>

            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" value="<?= old('start_time') ?>" class="form-control">
                <small>Optional - leave blank if time is not specified</small>
            </div>

            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time" value="<?= old('end_time') ?>" class="form-control">
                <small>Optional</small>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?= old('location') ?>" class="form-control" placeholder="Prague, Czech Republic">
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image URL</label>
                <input type="text" id="featured_image" name="featured_image" value="<?= old('featured_image') ?>" class="form-control" placeholder="/uploads/event.jpg">
            </div>
        </div>

        <!-- Czech Tab -->
        <div class="tab-content" id="tab-cs">
            <div class="form-group">
                <label for="title_cs">Title (Czech) *</label>
                <input type="text" id="title_cs" name="title_cs" required value="<?= old('title_cs') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="description_cs">Description (Czech)</label>
                <textarea id="description_cs" name="description_cs" rows="10" class="form-control"><?= old('description_cs') ?></textarea>
            </div>
        </div>

        <!-- English Tab -->
        <div class="tab-content" id="tab-en">
            <div class="form-group">
                <label for="title_en">Title (English) *</label>
                <input type="text" id="title_en" name="title_en" required value="<?= old('title_en') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="description_en">Description (English)</label>
                <textarea id="description_en" name="description_en" rows="10" class="form-control"><?= old('description_en') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Event</button>
            <a href="<?= adminUrl('events') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const targetTab = this.dataset.tab;

        // Remove active from all tabs
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        // Add active to clicked tab
        this.classList.add('active');
        document.getElementById('tab-' + targetTab).classList.add('active');
    });
});
</script>
