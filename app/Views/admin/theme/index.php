<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Theme Settings') ?> - Admin</title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        .theme-settings {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .theme-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .theme-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .theme-actions {
            display: flex;
            gap: 10px;
        }

        .css-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .css-info strong {
            display: inline-block;
            min-width: 150px;
        }

        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s;
        }

        .tab-button:hover {
            color: #111827;
            background: #f9fafb;
        }

        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .setting-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }

        .setting-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #111827;
        }

        .setting-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .setting-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .setting-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .setting-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            flex-shrink: 0;
        }

        .color-input-wrapper {
            position: relative;
        }

        .color-picker {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-outline {
            background: white;
            color: #667eea;
            border: 1px solid #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
        }

        .setting-key {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .flash {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .flash.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .flash.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .flash.warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <a href="<?= adminUrl() ?>">← Zpět na Dashboard</a>
        </nav>

        <div class="theme-settings">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="flash <?= e($type) ?>">
                        <?= $message ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="theme-header">
                <h1>Theme Settings</h1>
                <div class="theme-actions">
                    <button type="button" class="btn btn-outline" onclick="window.open('<?= url('') ?>', '_blank')">
                        👁️ Preview
                    </button>
                    <a href="<?= adminUrl('theme/export') ?>" class="btn btn-outline">
                        💾 Export
                    </a>
                </div>
            </div>

            <?php if ($cssInfo['exists']): ?>
                <div class="css-info">
                    <div><strong>CSS Soubor:</strong> <?= e($cssInfo['path']) ?></div>
                    <div><strong>Velikost:</strong> <?= number_format($cssInfo['size'] / 1024, 2) ?> KB</div>
                    <div><strong>Naposledy změněn:</strong> <?= e($cssInfo['last_modified']) ?></div>
                </div>
            <?php else: ?>
                <div class="css-info" style="background: #fee2e2; color: #991b1b;">
                    <strong>⚠️ CSS soubor neexistuje!</strong> Po uložení nastavení bude vygenerován.
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= adminUrl('theme/update') ?>">
                <?= csrfField() ?>

                <div class="tabs">
                    <button type="button" class="tab-button <?= $activeTab === 'colors' ? 'active' : '' ?>" onclick="switchTab('colors')">
                        🎨 Barvy
                    </button>
                    <button type="button" class="tab-button <?= $activeTab === 'typography' ? 'active' : '' ?>" onclick="switchTab('typography')">
                        📝 Typografie
                    </button>
                    <button type="button" class="tab-button <?= $activeTab === 'spacing' ? 'active' : '' ?>" onclick="switchTab('spacing')">
                        📐 Spacing & Layout
                    </button>
                    <button type="button" class="tab-button <?= $activeTab === 'effects' ? 'active' : '' ?>" onclick="switchTab('effects')">
                        ✨ Efekty
                    </button>
                </div>

                <!-- COLORS TAB -->
                <div id="tab-colors" class="tab-content <?= $activeTab === 'colors' ? 'active' : '' ?>">
                    <h2>Barvy</h2>
                    <div class="settings-grid">
                        <?php foreach ($settings['colors'] as $setting): ?>
                            <div class="setting-item">
                                <label class="setting-label">
                                    <?= e($setting['label']) ?>
                                </label>
                                <?php if ($setting['description']): ?>
                                    <div class="setting-description">
                                        <?= e($setting['description']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="setting-input-group">
                                    <div class="color-input-wrapper">
                                        <div class="color-preview"
                                             style="background-color: <?= e($setting['setting_value']) ?>;"
                                             onclick="document.getElementById('picker-<?= e($setting['setting_key']) ?>').click()">
                                        </div>
                                        <input type="color"
                                               id="picker-<?= e($setting['setting_key']) ?>"
                                               class="color-picker"
                                               value="<?= e(strpos($setting['setting_value'], 'rgba') === false ? $setting['setting_value'] : '#000000') ?>"
                                               onchange="updateColorValue('<?= e($setting['setting_key']) ?>', this.value)">
                                    </div>
                                    <input type="text"
                                           class="setting-input"
                                           name="<?= e($setting['setting_key']) ?>"
                                           id="input-<?= e($setting['setting_key']) ?>"
                                           value="<?= e($setting['setting_value']) ?>"
                                           placeholder="#000000">
                                </div>
                                <div class="setting-key">--<?= str_replace('_', '-', e($setting['setting_key'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- TYPOGRAPHY TAB -->
                <div id="tab-typography" class="tab-content <?= $activeTab === 'typography' ? 'active' : '' ?>">
                    <h2>Typografie</h2>
                    <div class="settings-grid">
                        <?php foreach ($settings['typography'] as $setting): ?>
                            <div class="setting-item">
                                <label class="setting-label">
                                    <?= e($setting['label']) ?>
                                </label>
                                <?php if ($setting['description']): ?>
                                    <div class="setting-description">
                                        <?= e($setting['description']) ?>
                                    </div>
                                <?php endif; ?>
                                <input type="text"
                                       class="setting-input"
                                       name="<?= e($setting['setting_key']) ?>"
                                       value="<?= e($setting['setting_value']) ?>">
                                <div class="setting-key">--<?= str_replace('_', '-', e($setting['setting_key'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SPACING TAB -->
                <div id="tab-spacing" class="tab-content <?= $activeTab === 'spacing' ? 'active' : '' ?>">
                    <h2>Spacing & Layout</h2>
                    <div class="settings-grid">
                        <?php foreach ($settings['spacing'] as $setting): ?>
                            <div class="setting-item">
                                <label class="setting-label">
                                    <?= e($setting['label']) ?>
                                </label>
                                <?php if ($setting['description']): ?>
                                    <div class="setting-description">
                                        <?= e($setting['description']) ?>
                                    </div>
                                <?php endif; ?>
                                <input type="text"
                                       class="setting-input"
                                       name="<?= e($setting['setting_key']) ?>"
                                       value="<?= e($setting['setting_value']) ?>">
                                <div class="setting-key">--<?= str_replace('_', '-', e($setting['setting_key'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- EFFECTS TAB -->
                <div id="tab-effects" class="tab-content <?= $activeTab === 'effects' ? 'active' : '' ?>">
                    <h2>Efekty</h2>
                    <div class="settings-grid">
                        <?php foreach ($settings['effects'] as $setting): ?>
                            <div class="setting-item">
                                <label class="setting-label">
                                    <?= e($setting['label']) ?>
                                </label>
                                <?php if ($setting['description']): ?>
                                    <div class="setting-description">
                                        <?= e($setting['description']) ?>
                                    </div>
                                <?php endif; ?>
                                <input type="text"
                                       class="setting-input"
                                       name="<?= e($setting['setting_key']) ?>"
                                       value="<?= e($setting['setting_value']) ?>">
                                <div class="setting-key">--<?= str_replace('_', '-', e($setting['setting_key'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            💾 Uložit změny a regenerovat CSS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function updateColorValue(key, value) {
            document.getElementById('input-' + key).value = value;
            document.querySelector('#input-' + key).previousElementSibling.querySelector('.color-preview').style.backgroundColor = value;
        }

        // Update color preview when input changes
        document.querySelectorAll('.setting-input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{3,6}$/)) {
                    const preview = this.parentElement.querySelector('.color-preview');
                    if (preview) {
                        preview.style.backgroundColor = this.value;
                    }
                }
            });
        });
    </script>
</body>
</html>
