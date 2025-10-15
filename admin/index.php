<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}

require_once '../db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE username = ? AND role = 'admin' AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $user['name'];
            
            // Update last login
            $update_stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .login-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .login-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.9em;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .alert-danger {
            border-radius: 8px;
            border: none;
            background: #fee;
            color: #c33;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #28a745;
            text-decoration: none;
            font-size: 0.9em;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .admin-notice {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
        .admin-notice small {
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <div class="login-header">
                        <h3><i class="fas fa-user-shield"></i> Admin Portal</h3>
                        <p>Persecution Tracker System</p>
                    </div>
                    <div class="login-body">
                        <div class="admin-notice">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Welcome to the Admin Portal. Login to manage persecution reports and content.
                            </small>
                        </div>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       placeholder="Username"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                       required>
                                <label for="username"><i class="fas fa-user"></i> Username</label>
                            </div>
                            
                            <div class="form-floating">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Password"
                                       required>
                                <label for="password"><i class="fas fa-lock"></i> Password</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt"></i> Login to Admin Portal
                            </button>
                        </form>
                        
                        <div class="back-link">
                            <a href="../index.php">
                                <i class="fas fa-arrow-left"></i> Back to Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Add enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>