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
            stroke-width: 2;
            opacity: 0.8;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #dc3545;
        }
        .recent-reports {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .report-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .report-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <strong>Persecution Tracker</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="state.php">All Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">Admin Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Persecution Report Tracker</h1>
            <p class="lead">Documenting and tracking persecution incidents across India</p>
            <p class="mb-4">Click on any state to view detailed reports and statistics</p>
            
            <!-- Quick Stats -->
            <div class="row justify-content-center mt-5">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stat-number"><?php echo array_sum($counts_by_state); ?></div>
                        <div class="text-muted">Total Reports</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stat-number"><?php echo count($counts_by_state); ?></div>
                        <div class="text-muted">States Covered</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stat-number"><?php echo date('Y'); ?></div>
                        <div class="text-muted">Current Year</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Map Section -->
    <section class="map-container">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Interactive India Map</h2>
                    <p class="lead text-muted">Click on any state to view detailed incident reports</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="position-relative" id="india-map">
                        <!-- India SVG Map -->
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1200" xml:space="preserve" class="map-svg">
                            <defs>
                                <style>
                                    .state-path {
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                    }
                                    .state-path:hover {
                                        opacity: 0.8;
                                        stroke-width: 2;
                                    }
                                </style>
                            </defs>
                            <g>
                                <!-- Jammu and Kashmir -->
                                <path class="state-path" data-state="Jammu and Kashmir" title="Jammu and Kashmir" 
                                      fill="#e3f2fd" stroke="#1976d2" stroke-width="1" 
                                      d="M395,85 L390,88 L385,85 L380,88 L375,87 L370,90 L365,95 L360,98 L355,102 L350,100 L345,103 L340,105 L345,110 L350,115 L345,120 L340,125 L335,128 L330,125 L325,120 L320,115 L315,118 L310,120 L305,115 L300,110 L295,105 L290,100 L295,95 L300,90 L305,85 L310,82 L315,79 L320,76 L325,73 L330,70 L335,67 L340,64 L345,61 L350,58 L355,55 L360,52 L365,49 L370,46 L375,49 L380,52 L385,55 L390,58 L395,61 L400,64 L405,67 L410,70 L415,73 L420,76 L425,79 L430,82 L425,85 L420,88 L415,91 L410,88 L405,85 Z"/>
                                
                                <!-- Punjab -->
                                <path class="state-path" data-state="Punjab" title="Punjab" 
                                      fill="#fff3e0" stroke="#f57c00" stroke-width="1" 
                                      d="M340,125 L360,125 L380,135 L390,145 L385,155 L375,165 L365,170 L355,165 L345,160 L335,155 L330,145 L335,135 Z"/>
                                
                                <!-- Haryana -->
                                <path class="state-path" data-state="Haryana" title="Haryana" 
                                      fill="#fce4ec" stroke="#c2185b" stroke-width="1" 
                                      d="M390,145 L410,145 L425,155 L430,165 L425,175 L415,180 L405,175 L395,170 L385,165 L380,155 L385,145 Z"/>
                                
                                <!-- Delhi -->
                                <circle class="state-path" data-state="Delhi" title="Delhi" 
                                        fill="#ffebee" stroke="#d32f2f" stroke-width="2" 
                                        cx="420" cy="180" r="8"/>
                                
                                <!-- Rajasthan -->
                                <path class="state-path" data-state="Rajasthan" title="Rajasthan" 
                                      fill="#efebe9" stroke="#5d4037" stroke-width="1" 
                                      d="M280,170 L340,170 L380,180 L420,185 L430,200 L425,220 L420,240 L410,260 L400,280 L390,300 L380,320 L370,340 L350,350 L330,345 L310,340 L290,330 L270,320 L250,310 L240,290 L250,270 L260,250 L270,230 L275,210 L280,190 Z"/>
                                
                                <!-- Gujarat -->
                                <path class="state-path" data-state="Gujarat" title="Gujarat" 
                                      fill="#e1f5fe" stroke="#0277bd" stroke-width="1" 
                                      d="M200,320 L280,330 L320,350 L340,380 L330,420 L320,450 L300,480 L280,500 L260,510 L240,500 L220,490 L200,480 L180,470 L160,450 L150,430 L160,410 L170,390 L180,370 L190,350 L195,335 Z"/>
                                
                                <!-- Maharashtra -->
                                <path class="state-path" data-state="Maharashtra" title="Maharashtra" 
                                      fill="#e8f5e8" stroke="#388e3c" stroke-width="1" 
                                      d="M340,380 L420,390 L480,400 L520,420 L540,450 L530,480 L520,510 L500,540 L480,560 L460,570 L440,565 L420,560 L400,550 L380,540 L360,530 L350,510 L340,490 L335,470 L340,450 L345,430 L350,410 L345,395 Z"/>
                                
                                <!-- Madhya Pradesh -->
                                <path class="state-path" data-state="Madhya Pradesh" title="Madhya Pradesh" 
                                      fill="#fff8e1" stroke="#fbc02d" stroke-width="1" 
                                      d="M380,320 L480,330 L520,340 L560,350 L590,360 L610,380 L600,410 L590,440 L580,460 L570,480 L550,500 L530,490 L510,480 L490,470 L470,460 L450,450 L430,440 L410,430 L390,420 L370,410 L360,390 L370,370 L380,350 Z"/>
                                
                                <!-- Uttar Pradesh -->
                                <path class="state-path" data-state="Uttar Pradesh" title="Uttar Pradesh" 
                                      fill="#f3e5f5" stroke="#7b1fa2" stroke-width="1" 
                                      d="M430,180 L550,190 L600,200 L630,220 L640,250 L635,280 L625,310 L615,340 L600,360 L580,370 L560,360 L540,350 L520,340 L500,330 L480,320 L460,310 L440,300 L430,280 L425,260 L430,240 L435,220 L430,200 Z"/>
                                
                                <!-- Bihar -->
                                <path class="state-path" data-state="Bihar" title="Bihar" 
                                      fill="#e0f2f1" stroke="#00695c" stroke-width="1" 
                                      d="M640,250 L710,260 L740,280 L750,310 L745,340 L735,360 L720,380 L700,390 L680,385 L660,380 L650,360 L645,340 L650,320 L655,300 L650,280 L645,260 Z"/>
                                
                                <!-- West Bengal -->
                                <path class="state-path" data-state="West Bengal" title="West Bengal" 
                                      fill="#e8eaf6" stroke="#3f51b5" stroke-width="1" 
                                      d="M720,380 L780,390 L810,420 L820,450 L815,480 L800,500 L785,510 L770,505 L755,500 L740,490 L730,480 L720,470 L715,450 L720,430 L725,410 L730,395 Z"/>
                                
                                <!-- Jharkhand -->
                                <path class="state-path" data-state="Jharkhand" title="Jharkhand" 
                                      fill="#fff3e0" stroke="#ef6c00" stroke-width="1" 
                                      d="M615,340 L680,350 L720,370 L730,400 L725,430 L715,450 L700,460 L685,455 L670,450 L655,445 L640,440 L625,430 L615,420 L610,400 L615,380 L620,360 Z"/>
                                
                                <!-- Odisha -->
                                <path class="state-path" data-state="Odisha" title="Odisha" 
                                      fill="#f1f8e9" stroke="#689f38" stroke-width="1" 
                                      d="L685,455 L740,465 L770,485 L780,515 L775,545 L765,565 L750,580 L735,585 L720,580 L705,575 L690,570 L680,550 L675,530 L680,510 L685,490 L690,470 Z"/>
                                
                                <!-- Chhattisgarh -->
                                <path class="state-path" data-state="Chhattisgarh" title="Chhattisgarh" 
                                      fill="#fce4ec" stroke="#ad1457" stroke-width="1" 
                                      d="M580,460 L640,470 L680,485 L690,515 L685,545 L675,565 L660,580 L645,575 L630,570 L615,565 L600,555 L590,545 L580,535 L575,515 L580,495 L585,475 Z"/>
                                
                                <!-- Andhra Pradesh -->
                                <path class="state-path" data-state="Andhra Pradesh" title="Andhra Pradesh" 
                                      fill="#e1f5fe" stroke="#0288d1" stroke-width="1" 
                                      d="M520,540 L600,555 L650,575 L680,600 L690,630 L685,660 L675,685 L660,700 L645,695 L630,690 L615,685 L600,680 L585,675 L570,665 L555,655 L540,645 L530,625 L525,605 L520,585 L525,565 Z"/>
                                
                                <!-- Telangana -->
                                <path class="state-path" data-state="Telangana" title="Telangana" 
                                      fill="#fff8e1" stroke="#f9a825" stroke-width="1" 
                                      d="M530,480 L580,490 L610,510 L620,540 L615,565 L605,585 L590,600 L575,595 L560,590 L545,585 L535,570 L530,555 L530,535 L535,515 L540,495 Z"/>
                                
                                <!-- Karnataka -->
                                <path class="state-path" data-state="Karnataka" title="Karnataka" 
                                      fill="#f3e5f5" stroke="#8e24aa" stroke-width="1" 
                                      d="M460,570 L540,580 L580,600 L600,630 L590,660 L580,685 L565,705 L550,715 L535,710 L520,705 L505,700 L490,690 L475,680 L460,670 L450,650 L455,630 L460,610 L465,590 Z"/>
                                
                                <!-- Tamil Nadu -->
                                <path class="state-path" data-state="Tamil Nadu" title="Tamil Nadu" 
                                      fill="#e0f2f1" stroke="#00796b" stroke-width="1" 
                                      d="M550,715 L630,730 L670,750 L690,780 L695,810 L690,840 L680,865 L665,885 L650,895 L635,890 L620,885 L605,880 L590,875 L575,865 L560,855 L550,835 L545,815 L550,795 L555,775 L560,755 L555,735 Z"/>
                                
                                <!-- Kerala -->
                                <path class="state-path" data-state="Kerala" title="Kerala" 
                                      fill="#efebe9" stroke="#6d4c41" stroke-width="1" 
                                      d="M480,690 L520,705 L530,735 L525,765 L520,790 L515,815 L510,840 L500,860 L490,875 L480,885 L470,875 L465,860 L470,845 L475,830 L480,815 L485,800 L490,785 L485,770 L480,755 L485,740 L490,725 L485,710 Z"/>
                                
                                <!-- Goa -->
                                <path class="state-path" data-state="Goa" title="Goa" 
                                      fill="#e8f5e8" stroke="#2e7d32" stroke-width="1" 
                                      d="M400,580 L430,585 L440,605 L435,620 L425,630 L415,625 L405,620 L395,615 L390,605 L395,595 L400,590 Z"/>
                                
                                <!-- Assam -->
                                <path class="state-path" data-state="Assam" title="Assam" 
                                      fill="#fff3e0" stroke="#ff8f00" stroke-width="1" 
                                      d="M750,310 L850,320 L880,340 L890,370 L885,400 L875,420 L860,435 L845,430 L830,425 L815,420 L800,415 L785,405 L775,395 L765,385 L755,375 L750,365 L745,355 L750,345 L755,335 L750,325 Z"/>
                                
                                <!-- Meghalaya -->
                                <path class="state-path" data-state="Meghalaya" title="Meghalaya" 
                                      fill="#f1f8e9" stroke="#33691e" stroke-width="1" 
                                      d="M815,420 L850,425 L865,440 L860,455 L850,465 L835,470 L825,465 L815,460 L810,450 L815,440 L820,435 Z"/>
                                
                                <!-- Tripura -->
                                <path class="state-path" data-state="Tripura" title="Tripura" 
                                      fill="#fce4ec" stroke="#c2185b" stroke-width="1" 
                                      d="M860,455 L880,460 L885,475 L880,485 L870,490 L860,485 L855,475 L860,465 Z"/>
                                
                                <!-- Mizoram -->
                                <path class="state-path" data-state="Mizoram" title="Mizoram" 
                                      fill="#e8f5e8" stroke="#2e7d32" stroke-width="1" 
                                      d="M855,475 L875,480 L880,495 L875,510 L865,520 L855,515 L850,505 L855,495 L860,485 Z"/>
                                
                                <!-- Manipur -->
                                <path class="state-path" data-state="Manipur" title="Manipur" 
                                      fill="#f3e5f5" stroke="#8e24aa" stroke-width="1" 
                                      d="M875,420 L895,425 L900,440 L895,450 L885,455 L875,450 L870,440 L875,430 Z"/>
                                
                                <!-- Nagaland -->
                                <path class="state-path" data-state="Nagaland" title="Nagaland" 
                                      fill="#e1f5fe" stroke="#0277bd" stroke-width="1" 
                                      d="M885,400 L905,405 L910,420 L905,430 L895,435 L885,430 L880,420 L885,410 Z"/>
                                
                                <!-- Arunachal Pradesh -->
                                <path class="state-path" data-state="Arunachal Pradesh" title="Arunachal Pradesh" 
                                      fill="#e8eaf6" stroke="#3f51b5" stroke-width="1" 
                                      d="M890,370 L950,380 L980,400 L985,430 L980,450 L970,465 L955,470 L940,465 L925,460 L910,455 L900,445 L895,435 L900,425 L905,415 L900,405 L895,395 L900,385 L905,375 Z"/>
                                
                                <!-- Sikkim -->
                                <path class="state-path" data-state="Sikkim" title="Sikkim" 
                                      fill="#fff8e1" stroke="#f57c00" stroke-width="1" 
                                      d="M780,310 L800,315 L805,325 L800,335 L790,340 L780,335 L775,325 L780,320 Z"/>
                                
                                <!-- Uttarakhand -->
                                <path class="state-path" data-state="Uttarakhand" title="Uttarakhand" 
                                      fill="#e0f2f1" stroke="#00695c" stroke-width="1" 
                                      d="M430,165 L480,170 L520,180 L530,200 L525,220 L515,235 L500,245 L485,240 L470,235 L455,230 L440,225 L430,210 L425,195 L430,180 Z"/>
                                
                                <!-- Himachal Pradesh -->
                                <path class="state-path" data-state="Himachal Pradesh" title="Himachal Pradesh" 
                                      fill="#fce4ec" stroke="#ad1457" stroke-width="1" 
                                      d="M380,135 L430,140 L460,150 L480,165 L475,180 L465,190 L450,195 L435,190 L420,185 L405,180 L390,175 L380,165 L375,155 L380,145 Z"/>
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

    <!-- Recent Reports Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">Recent Reports</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="recent-reports">
                        <?php
                        try {
                            // Get recent reports
                            $stmt = $pdo->prepare("
                                SELECT id, title, short_message, state, created_at 
                                FROM posts 
                                WHERE status = 'published' 
                                ORDER BY created_at DESC 
                                LIMIT 5
                            ");
                            $stmt->execute();
                            $recent_reports = $stmt->fetchAll();

                            if ($recent_reports) {
                                foreach ($recent_reports as $report) {
                                    echo "<div class='report-item'>";
                                    echo "<h5><a href='details.php?id={$report['id']}' class='text-decoration-none'>" . htmlspecialchars($report['title']) . "</a></h5>";
                                    echo "<p class='text-muted mb-2'>" . htmlspecialchars(substr($report['short_message'], 0, 150)) . "...</p>";
                                    echo "<small class='text-muted'><i class='fas fa-map-marker-alt'></i> {$report['state']} | <i class='fas fa-calendar'></i> " . date('M j, Y', strtotime($report['created_at'])) . "</small>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div class='text-center text-muted'>";
                                echo "<p>No reports available at this time.</p>";
                                echo "</div>";
                            }
                        } catch (Exception $e) {
                            echo "<div class='alert alert-warning text-center'>";
                            echo "<p>Unable to load recent reports. Please try again later.</p>";
                            echo "</div>";
                        }
                        ?>
                        <div class="text-center mt-4">
                            <a href="state.php" class="btn btn-primary">View All Reports</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Persecution Report Tracker</h5>
                    <p class="mb-0">Documenting incidents across India for awareness and accountability.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // State post counts from PHP
            const stateCounts = <?php echo json_encode($counts_by_state); ?>;
            
            // Add click handlers to state paths
            $('.state-path').each(function() {
                const $path = $(this);
                const stateName = $path.data('state');
                const count = stateCounts[stateName] || 0;
                
                // Add title with count
                $path.attr('title', stateName + (count > 0 ? ' (' + count + ' reports)' : ' (No reports)'));
                
                // Add click handler
                $path.click(function() {
                    window.location.href = 'state.php?state=' + encodeURIComponent(stateName);
                });
                
                // Change opacity based on report count
                if (count > 0) {
                    $path.css('opacity', Math.min(0.5 + (count * 0.1), 1.0));
                } else {
                    $path.css('opacity', 0.3);
                }
            });
            
            console.log('Map initialized with', Object.keys(stateCounts).length, 'states having data');
        });
    </script>
</body>
</html>