<?php
session_start();
require_once 'db.php';

// Get post counts for map display
$post_counts = get_post_counts_by_state($pdo);
$counts_by_state = [];
foreach ($post_counts as $count) {
    $counts_by_state[$count['state']] = $count['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persecution Report Tracker - India</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/map-style.css" rel="stylesheet" type="text/css"/>
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .map-container {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .state-count {
            position: absolute;
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            pointer-events: none;
            z-index: 10;
        }
        .map-svg {
            width: 100%;
            max-width: 800px;
            height: auto;
        }
        .state-path {
            cursor: pointer;
            transition: all 0.3s ease;
            stroke-width: 1;
        }
        .state-path:hover {
            opacity: 0.8;
            stroke-width: 2;
            filter: brightness(1.1);
        }
        .stats-section {
            padding: 60px 0;
        }
        .stat-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        #state-counts {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <strong>Persecution Tracker</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#map">Interactive Map</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stats">Statistics</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin/">Admin Login</a></li>
                            <li><a class="dropdown-item" href="suadmin/">Super Admin</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">India Persecution Report Tracker</h1>
            <p class="lead mb-4">Track and monitor persecution incidents across Indian states</p>
            <a href="#map" class="btn btn-light btn-lg">Explore Interactive Map</a>
        </div>
    </section>

    <!-- Interactive Map Section -->
    <section id="map" class="map-container">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Interactive India Map</h2>
                    <p class="lead">Click on any state to view reported incidents</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="position-relative" id="india-map">
                        <!-- India SVG Map -->
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 560 758" xml:space="preserve" class="map-svg">
                            <g>
                                <!-- Existing detailed SVG paths with interactive features -->
                                <path class="state-path" data-state="Jammu and Kashmir" title="Jammu and Kashmir" vector-effect="non-scaling-stroke" fill="#e3f2fd" stroke="#1976d2" stroke-width="1" d="M223.332,101.241L220.643,101.684L219.464,100.523L218.564,100.455L216.515,101.229L214.492,101.051L211.442,103.213L210.456,105.08L206.702,105.806L206.402,106.747L202.836,108.934L200.78,107.756L199.233,107.767L198.886,109.325L200.954,110.802L201.966,112.28L201.549,114.007L202.257,115.245L200.921,117.01L199.139,119.34L197.611,119.62L196.713,121.141L191.947,122.952L191.216,124.855L190.928,123.963L190.34,123.77L190.458,123.183L188.961,123.595L186.539,123.254L183.223,121.477L183.041,120.976L181.758,121.233L180.273,119.523L179.071,119.208L177.573,120.367L173.809,119.1L170.23,119.395L168.761,116.099L169.649,114.871L168.967,113.292L170.193,110.122L168.754,110.561L168.028,112.264L165.867,112.708L164.387,113.484L160.784,113.169L159.044,112.119L156.434,111.069L154.446,109.495L151.34,108.552L148.73,106.98L144.754,106.98L143.884,105.409L142.517,104.362L140.777,103.107L141.523,100.495L140.405,98.615L140.032,95.382L141.399,94.027L140.405,90.697L141.026,89.449L141.15,87.891L139.287,86.333L139.411,84.88L140.653,83.636L139.659,80.424L137.795,78.872L137.919,75.874L137.547,73.458L136.305,70.879L136.801,68.305L136.801,66.554L138.416,65.115L141.15,64.498L143.263,63.779L143.387,61.828L145.002,59.468L146.742,59.264L150.967,59.161L152.458,58.239L153.452,55.167L154.57,53.224L154.57,52.01L155.75,52.358L156.807,52.607L158.176,52.793L159.171,53.416L159.482,53.913L160.104,54.037L160.353,53.664L161.099,53.54L161.597,53.664L162.343,53.478L163.214,53.104L164.022,52.42L164.582,51.736L165.266,51.425L166.013,51.86L166.51,52.482L166.883,53.042L167.381,54.037L167.816,55.095L168.065,56.401L168.19,57.147L168.625,57.396L169.185,57.396L170.056,57.334L170.553,57.521L171.362,58.018L171.735,58.702L172.606,59.324L173.726,59.76L174.907,60.071L176.027,60.257L176.587,60.506L177.208,60.63L177.458,60.133L178.266,59.013L178.639,58.64L179.137,58.827L179.51,59.138L179.821,58.951L179.759,58.329L180.07,57.894L180.816,57.894L181.376,58.267L182.185,59.138L183.242,59.635L183.864,60.195L184.548,61.439L184.362,62.683L183.802,63.927L183.802,64.549L184.548,65.109L185.419,65.42L186.601,66.229L187.099,66.788L187.099,67.659L186.352,68.157L186.29,68.779L187.036,68.841L187.596,68.903L187.783,69.649L188.218,70.271L187.845,71.142L187.845,72.013L188.28,72.386L188.964,72.884L189.562,73.319L190.743,73.755L191.925,74.376L193.107,74.999L193.667,74.999L194.538,74.874L195.16,75.31L195.408,76.429L195.533,76.865L196.03,77.487L196.714,78.295L197.647,79.104L198.394,79.415L198.394,80.373L199.265,81.057L199.638,81.617L200.446,81.617L201.441,81.368L202.437,81.244L203.743,81.244L204.8,81.368L205.671,82.176L206.729,83.109L207.537,84.104L207.973,85.038L208.781,86.033L209.465,86.53L210.398,86.966L210.896,87.899L210.834,88.77L210.46,89.765L210.025,90.263L209.279,90.822L209.217,91.444L209.279,92.19L209.901,92.066L210.771,91.755L211.643,91.568L211.456,91.071L211.456,90.573L212.202,90.573L212.762,90.822L213.26,91.568L213.819,92.253L215.063,92.501L215.748,92.688L216.494,92.999L218.173,93.994L218.546,94.927L219.044,95.238L219.791,95.301L220.288,95.674L220.661,96.669L221.408,97.913L222.092,97.851L222.589,98.473L223.336,100.152L223.644,100.383z"/>
                                
                                <path class="state-path" data-state="West Bengal" title="West Bengal" vector-effect="non-scaling-stroke" fill="#e8f5e8" stroke="#388e3c" stroke-width="1" d="M538.114,402.543l0.683,1.604l-1.204,0.393l0.391,-0.644l-0.644,-0.693L538.114,402.543z"/>
                                
                                <path class="state-path" data-state="Uttarakhand" title="Uttarakhand" vector-effect="non-scaling-stroke" fill="#fff3e0" stroke="#f57c00" stroke-width="1" d="M287.766,148.968L289.159,150.458L288.788,151.571L289.805,152.18L290.121,153.765z"/>
                                
                                <path class="state-path" data-state="Uttar Pradesh" title="Uttar Pradesh" vector-effect="non-scaling-stroke" fill="#fce4ec" stroke="#c2185b" stroke-width="1" d="M245.112,173.779L246.972,173.612L248.974,175.497L254.743,177.896z"/>
                                
                                <path class="state-path" data-state="Tripura" title="Tripura" vector-effect="non-scaling-stroke" fill="#f3e5f5" stroke="#7b1fa2" stroke-width="1" d="M627.338,327.524L628.535,328.016L628.535,328.016L628.538,329.158z"/>
                                
                                <path class="state-path" data-state="Tamil Nadu" title="Tamil Nadu" vector-effect="non-scaling-stroke" fill="#fff8e1" stroke="#fbc02d" stroke-width="1" d="M290.839,724.712l0.595,0.441l-0.538,0.762l0.298,0.538z"/>
                                
                                <path class="state-path" data-state="Telangana" title="Telangana" vector-effect="non-scaling-stroke" fill="#e0f2f1" stroke="#00695c" stroke-width="1" d="M244.77,490.418L245.213,489.723L243.974,488.905L244.907,487.112z"/>
                                
                                <path class="state-path" data-state="Sikkim" title="Sikkim" vector-effect="non-scaling-stroke" fill="#e8eaf6" stroke="#3f51b5" stroke-width="1" d="M534.803,234.042L535.365,234.696L537.569,234.716L539.708,236.331z"/>
                                
                                <path class="state-path" data-state="Rajasthan" title="Rajasthan" vector-effect="non-scaling-stroke" fill="#efebe9" stroke="#5d4037" stroke-width="1" d="M151.092,179.253L151.101,181.184L149.223,183.127L149.128,185.134z"/>
                                
                                <path class="state-path" data-state="Punjab" title="Punjab" vector-effect="non-scaling-stroke" fill="#e1f5fe" stroke="#0277bd" stroke-width="1" d="M200.922,117.01L200.358,118.795L202.48,121.176L200.172,122.157z"/>
                                
                                <path class="state-path" data-state="Odisha" title="Odisha" vector-effect="non-scaling-stroke" fill="#f1f8e9" stroke="#689f38" stroke-width="1" d="M488.414,425.58l3.171,0.018l0.248,1.444l-1.765,0.134z"/>
                                
                                <path class="state-path" data-state="Nagaland" title="Nagaland" vector-effect="non-scaling-stroke" fill="#fce4ec" stroke="#ad1457" stroke-width="1" d="M706.028,261.65L707.03,265.626L705.74,266.306L706.371,266.675z"/>
                                
                                <path class="state-path" data-state="Mizoram" title="Mizoram" vector-effect="non-scaling-stroke" fill="#e8f5e8" stroke="#2e7d32" stroke-width="1" d="M630.251,334.613L633.538,334.576L633.91,337.21L635.066,337.499z"/>
                                
                                <path class="state-path" data-state="Madhya Pradesh" title="Madhya Pradesh" vector-effect="non-scaling-stroke" fill="#fff3e0" stroke="#ef6c00" stroke-width="1" d="M262.018,267.057L263.353,267.453L263.907,266.326L265.718,266.057z"/>
                                
                                <path class="state-path" data-state="Manipur" title="Manipur" vector-effect="non-scaling-stroke" fill="#f3e5f5" stroke="#8e24aa" stroke-width="1" d="M661.028,306.968L661.1,307.759L661.99,307.849L661.841,308.672z"/>
                                
                                <path class="state-path" data-state="Meghalaya" title="Meghalaya" vector-effect="non-scaling-stroke" fill="#e0f2f1" stroke="#00796b" stroke-width="1" d="M566.821,300.827L570.669,299.312L567.409,295.84L568.479,295.59z"/>
                                
                                <path class="state-path" data-state="Maharashtra" title="Maharashtra" vector-effect="non-scaling-stroke" fill="#e1f5fe" stroke="#0288d1" stroke-width="1" d="M325.976,410.969L324.32,411.124L321.506,409.219L320.533,409.097z"/>
                                
                                <path class="state-path" data-state="Kerala" title="Kerala" vector-effect="non-scaling-stroke" fill="#f1f8e9" stroke="#558b2f" stroke-width="1" d="M210.842,665.575l1.359,1.697l2.002,0.759z"/>
                                
                                <path class="state-path" data-state="Karnataka" title="Karnataka" vector-effect="non-scaling-stroke" fill="#fff8e1" stroke="#f9a825" stroke-width="1" d="M262.302,635.204l0.171,0.978l-0.977,0.885l1.949,-0.275z"/>
                                
                                <path class="state-path" data-state="Gujarat" title="Gujarat" vector-effect="non-scaling-stroke" fill="#fce4ec" stroke="#c2185b" stroke-width="1" d="M76.353,323.401L72.127,316.764L70.789,311.288L67.447,308.127z"/>
                                
                                <path class="state-path" data-state="Goa" title="Goa" vector-effect="non-scaling-stroke" fill="#e8f5e8" stroke="#388e3c" stroke-width="1" d="M240.842,565.575l1.359,1.697l2.002,0.759l-3.361,-2.456z"/>
                                
                                <path class="state-path" data-state="Chhattisgarh" title="Chhattisgarh" vector-effect="non-scaling-stroke" fill="#f3e5f5" stroke="#7b1fa2" stroke-width="1" d="M413.44,379.858l3.462,1.306l0.458,1.829l1.088,0.762z"/>
                                
                                <path class="state-path" data-state="Bihar" title="Bihar" vector-effect="non-scaling-stroke" fill="#e0f2f1" stroke="#00695c" stroke-width="1" d="M490.283,289.027l2.821,0.634l0.929,1.136l1.848,0.165z"/>
                                
                                <path class="state-path" data-state="Assam" title="Assam" vector-effect="non-scaling-stroke" fill="#fff3e0" stroke="#f57c00" stroke-width="1" d="M566.429,302.609l0.655,1.964l-0.671,1.37z"/>
                                
                                <path class="state-path" data-state="Arunachal Pradesh" title="Arunachal Pradesh" vector-effect="non-scaling-stroke" fill="#e8eaf6" stroke="#3f51b5" stroke-width="1" d="M633.631,314.221L631.572,313.689L631.59,313.008z"/>
                                
                                <path class="state-path" data-state="Andhra Pradesh" title="Andhra Pradesh" vector-effect="non-scaling-stroke" fill="#efebe9" stroke="#5d4037" stroke-width="1" d="M315.808,479.268L317.75,482.705L321.453,481.616z"/>
                                
                                <path class="state-path" data-state="Jharkhand" title="Jharkhand" vector-effect="non-scaling-stroke" fill="#f1f8e9" stroke="#689f38" stroke-width="1" d="M488.031,380.812l1.441,1.323l2.105,0.852z"/>
                                
                                <path class="state-path" data-state="Haryana" title="Haryana" vector-effect="non-scaling-stroke" fill="#fce4ec" stroke="#ad1457" stroke-width="1" d="M224.377,160.659L224.356,161.439L225.891,162.588z"/>
                                
                                <path class="state-path" data-state="Himachal Pradesh" title="Himachal Pradesh" vector-effect="non-scaling-stroke" fill="#e1f5fe" stroke="#0277bd" stroke-width="1" d="M225.902,164.386L224.986,164.315L225.064,163.812z"/>
                                
                                <!-- Union Territories -->
                                <path class="state-path" data-state="Delhi" title="Delhi" vector-effect="non-scaling-stroke" fill="#ffebee" stroke="#d32f2f" stroke-width="1" d="M235.278,213.974L235.145,213.373L236.176,212.998z"/>
                                
                                <path class="state-path" data-state="Puducherry" title="Puducherry" vector-effect="non-scaling-stroke" fill="#f3e5f5" stroke="#8e24aa" stroke-width="1" d="M304.926,685.886l-0.991,-0.009l-0.114,-1.395z"/>
                            </g>
                        </svg>
                        
                        <!-- State count badges positioned dynamically -->
                        <div id="state-counts"></div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <strong>How to use:</strong> Click on any state to view detailed incident reports for that region. Hover to see the number of reports.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="stats-section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>National Statistics</h2>
                    <p class="lead">Overview of reported incidents across India</p>
                </div>
            </div>
            <div class="row">
                <?php
                // Get total statistics
                $total_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
                $total_states = $pdo->query("SELECT COUNT(DISTINCT state) FROM posts WHERE status = 'published' AND state IS NOT NULL AND state != ''")->fetchColumn();
                $total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
                $recent_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published' AND DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
                ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_posts; ?></div>
                        <div>Total Reports</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_states; ?></div>
                        <div>States Covered</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_categories; ?></div>
                        <div>Categories</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $recent_posts; ?></div>
                        <div>This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Reports Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Recent Reports</h2>
                    <p class="lead">Latest persecution incidents reported</p>
                </div>
            </div>
            <div class="row">
                <?php
                // Get recent published posts
                $stmt = $pdo->query("
                    SELECT p.*, c.name as category_name 
                    FROM posts p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'published' 
                    ORDER BY p.created_at DESC 
                    LIMIT 6
                ");
                $recent_posts_data = $stmt->fetchAll();
                
                foreach ($recent_posts_data as $post):
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if ($post['featured_image_path']): ?>
                            <img src="<?php echo htmlspecialchars($post['featured_image_path']); ?>" class="card-img-top" alt="Report Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo truncate_text(htmlspecialchars($post['short_message']), 100); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($post['state']); ?>
                                <?php if ($post['category_name']): ?>
                                    | <i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['category_name']); ?>
                                <?php endif; ?>
                                <br>
                                <i class="fas fa-calendar"></i> <?php echo format_date($post['created_at']); ?>
                            </small>
                        </div>
                        <div class="card-footer">
                            <a href="details.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($recent_posts_data)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No reports available at the moment.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Persecution Tracker</h5>
                    <p>Monitoring and documenting persecution incidents across India</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Persecution Tracker. All rights reserved.</p>
                    <p>
                        <a href="#" class="text-light me-3">Privacy Policy</a>
                        <a href="#" class="text-light">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // State counts data from PHP
            const stateCounts = <?php echo json_encode($counts_by_state); ?>;
            
            // Add click handlers for states
            $('.state-path').click(function() {
                const state = $(this).data('state');
                window.location.href = 'state.php?state=' + encodeURIComponent(state);
            });
            
            // Add hover effects and tooltips
            $('.state-path').hover(
                function() {
                    const state = $(this).data('state');
                    const count = stateCounts[state] || 0;
                    $(this).attr('title', state + ': ' + count + ' reports');
                    
                    // Change color on hover
                    $(this).css('fill', '#ff6b6b');
                },
                function() {
                    // Reset color
                    $(this).css('fill', '');
                }
            );
            
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 70
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>