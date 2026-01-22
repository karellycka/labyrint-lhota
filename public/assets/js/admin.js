/**
 * Admin JavaScript
 */

(function() {
    'use strict';

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Confirm delete actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete') ||
            e.target.classList.contains('btn-danger')) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    field.style.borderColor = '#dc3545';
                } else {
                    field.classList.remove('error');
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });

    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align: right; font-size: 12px; color: #666; margin-top: 5px;';
        textarea.parentNode.appendChild(counter);

        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length} / ${maxLength}`;
            if (remaining < 50) {
                counter.style.color = '#dc3545';
            } else {
                counter.style.color = '#666';
            }
        };

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // AJAX helper with CSRF
    window.adminAjax = function(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        const defaults = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (csrfToken) {
            defaults.headers['X-CSRF-Token'] = csrfToken.content;
        }

        const config = Object.assign({}, defaults, options);

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('Admin AJAX Error:', error);
                throw error;
            });
    };

    // Show success message
    window.showSuccess = function(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success';
        alert.textContent = message;

        const content = document.querySelector('.admin-content');
        if (content) {
            content.insertBefore(alert, content.firstChild);

            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    };

    // Show error message
    window.showError = function(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-error';
        alert.textContent = message;

        const content = document.querySelector('.admin-content');
        if (content) {
            content.insertBefore(alert, content.firstChild);

            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    };

    // Image preview for file uploads
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = input.parentNode.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview';
                        preview.style.cssText = 'max-width: 200px; margin-top: 10px; border-radius: 5px;';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });

    console.log('Admin JS loaded');
})();
