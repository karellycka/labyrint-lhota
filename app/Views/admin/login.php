<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h1><?= SITE_NAME ?></h1>
            <p>Admin Login</p>
        </div>

        <form method="POST" action="<?= adminUrl('login') ?>" class="login-form">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your username"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    Login
                </button>
            </div>
        </form>

        <div class="login-footer">
            <p>Default credentials: admin / admin123</p>
            <p><small>⚠️ Change password after first login!</small></p>
        </div>
    </div>
</div>

<style>
    .admin-body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .login-container {
        width: 100%;
        max-width: 400px;
        padding: 20px;
    }

    .login-box {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        text-align: center;
    }

    .login-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        font-weight: 700;
    }

    .login-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .login-form {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-block {
        width: 100%;
        display: block;
    }

    .login-footer {
        padding: 20px 30px;
        background: #f8f9fa;
        text-align: center;
        border-top: 1px solid #e1e8ed;
    }

    .login-footer p {
        margin: 5px 0;
        font-size: 13px;
        color: #666;
    }

    .login-footer small {
        color: #e74c3c;
        font-weight: 600;
    }

    .alert {
        padding: 15px 20px;
        margin: 0 30px 20px 30px;
        border-radius: 6px;
        font-size: 14px;
    }

    .alert-error {
        background: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }

    .alert-success {
        background: #efe;
        color: #3c3;
        border: 1px solid #cfc;
    }
</style>
