<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | Persecution Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .error-content {
            text-align: center;
            color: white;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        .error-description {
            font-size: 1.1rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .btn-home {
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        .search-suggestions {
            margin-top: 40px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .suggestion-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            display: block;
            padding: 8px 0;
            transition: color 0.3s ease;
        }
        .suggestion-link:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="error-content">
                        <div class="error-code">404</div>
                        <div class="error-message">Page Not Found</div>
                        <div class="error-description">
                            The page you're looking for doesn't exist or has been moved.
                        </div>
                        
                        <a href="index.php" class="btn-home">
                            <i class="fas fa-home"></i> Return to Home
                        </a>
                        
                        <div class="search-suggestions">
                            <h5 class="mb-3">
                                <i class="fas fa-search"></i> You might be looking for:
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="index.php" class="suggestion-link">
                                        <i class="fas fa-home"></i> Homepage
                                    </a>
                                    <a href="state.php" class="suggestion-link">
                                        <i class="fas fa-map"></i> Browse by State
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="index.php#recent-reports" class="suggestion-link">
                                        <i class="fas fa-clock"></i> Recent Reports
                                    </a>
                                    <a href="index.php#statistics" class="suggestion-link">
                                        <i class="fas fa-chart-bar"></i> Statistics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>