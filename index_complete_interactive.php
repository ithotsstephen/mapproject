<?php
session_start();
require_once 'db.php';

// Get post counts for map display
$post_counts = get_post_counts_by_state($pdo);
$counts_by_state = [];
foreach ($post_counts as $count) {
    $counts_by_state[$count['state']] = $count['count'];
}

// Get total statistics
try {
    $total_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
    $total_states = $pdo->query("SELECT COUNT(DISTINCT state) FROM posts WHERE status = 'published' AND state IS NOT NULL AND state != ''")->fetchColumn();
} catch (Exception $e) {
    $total_posts = 0;
    $total_states = 0;
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
        .map-svg {
            width: 100%;
            max-width: 800px;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }
        .state-path {
            cursor: pointer;
            transition: all 0.3s ease;
            stroke-width: 1;
        }
        .state-path:hover {
            stroke-width: 2;
            opacity: 0.8;
            filter: brightness(1.1);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
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
        .map-legend {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin: 5px 15px 5px 0;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            margin-right: 8px;
            border: 1px solid #ccc;
        }
        /* Tooltip styles */
        .map-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            pointer-events: none;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
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
                    <div class="stats-card">
                        <div class="stat-number"><?php echo $total_posts; ?></div>
                        <div class="text-muted">Total Incidents</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card">
                        <div class="stat-number"><?php echo $total_states; ?></div>
                        <div class="text-muted">States Covered</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card">
                        <div class="stat-number"><?php echo count($counts_by_state); ?></div>
                        <div class="text-muted">Active States</div>
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
                        <!-- India SVG Map with Complete Paths -->
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1200" xml:space="preserve" class="map-svg">
                            <defs>
                                <style>
                                    .state-path {
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                        stroke-width: 1.5;
                                        stroke: #333;
                                    }
                                    .state-path:hover {
                                        stroke-width: 3;
                                        filter: brightness(1.2);
                                    }
                                    .state-label {
                                        font-family: Arial, sans-serif;
                                        font-size: 12px;
                                        fill: #333;
                                        text-anchor: middle;
                                        pointer-events: none;
                                    }
                                </style>
                            </defs>
                            <g id="india-states">
                                <!-- Jammu and Kashmir -->
                                <path class="state-path" data-state="Jammu and Kashmir" 
                                      fill="#e3f2fd" 
                                      d="M395,85 L420,88 L445,92 L465,98 L480,105 L490,115 L485,125 L475,135 L465,140 L450,145 L435,148 L420,150 L405,148 L390,145 L380,140 L375,135 L372,125 L375,115 L382,105 L390,95 L395,85 Z"/>
                                
                                <!-- Punjab -->
                                <path class="state-path" data-state="Punjab" 
                                      fill="#fff3e0" 
                                      d="M372,125 L395,128 L415,135 L425,145 L420,155 L410,165 L395,170 L380,168 L370,160 L365,150 L368,140 L372,130 Z"/>
                                
                                <!-- Haryana -->
                                <path class="state-path" data-state="Haryana" 
                                      fill="#fce4ec" 
                                      d="M425,145 L445,148 L460,155 L465,165 L460,175 L450,182 L435,185 L420,182 L410,175 L415,165 L420,155 L425,145 Z"/>
                                
                                <!-- Delhi -->
                                <circle class="state-path" data-state="Delhi" 
                                        fill="#ffebee" r="12" cx="450" cy="180"/>
                                
                                <!-- Uttarakhand -->
                                <path class="state-path" data-state="Uttarakhand" 
                                      fill="#e0f2f1" 
                                      d="M465,165 L485,168 L500,175 L510,185 L505,195 L495,202 L480,205 L470,200 L465,190 L465,180 L465,170 Z"/>
                                
                                <!-- Uttar Pradesh -->
                                <path class="state-path" data-state="Uttar Pradesh" 
                                      fill="#f3e5f5" 
                                      d="M460,175 L510,185 L550,195 L590,205 L620,215 L640,225 L635,245 L625,265 L615,285 L600,300 L580,310 L560,315 L540,312 L520,308 L500,302 L480,295 L465,285 L455,275 L450,265 L455,255 L460,245 L465,235 L465,215 L460,195 Z"/>
                                
                                <!-- Rajasthan -->
                                <path class="state-path" data-state="Rajasthan" 
                                      fill="#efebe9" 
                                      d="M280,195 L360,205 L420,215 L460,225 L480,235 L495,255 L505,275 L510,295 L505,315 L495,335 L485,355 L470,375 L450,390 L430,400 L410,405 L390,400 L370,395 L350,385 L330,375 L310,360 L295,345 L285,325 L280,305 L275,285 L270,265 L265,245 L270,225 L275,205 Z"/>
                                
                                <!-- Gujarat -->
                                <path class="state-path" data-state="Gujarat" 
                                      fill="#e1f5fe" 
                                      d="M200,355 L280,365 L320,375 L360,390 L380,415 L390,440 L385,465 L375,485 L360,500 L345,512 L325,520 L305,525 L285,522 L265,515 L245,505 L225,490 L210,475 L200,455 L195,435 L200,415 L205,395 L210,375 Z"/>
                                
                                <!-- Madhya Pradesh -->
                                <path class="state-path" data-state="Madhya Pradesh" 
                                      fill="#fff8e1" 
                                      d="M430,400 L520,410 L590,420 L640,435 L670,455 L690,480 L700,505 L695,530 L685,550 L670,565 L650,575 L625,580 L600,575 L575,570 L550,560 L525,548 L500,535 L480,520 L465,505 L455,485 L450,465 L455,445 L465,425 L475,410 Z"/>
                                
                                <!-- Maharashtra -->
                                <path class="state-path" data-state="Maharashtra" 
                                      fill="#e8f5e8" 
                                      d="M360,500 L460,515 L540,530 L590,545 L620,565 L640,590 L650,615 L645,640 L635,660 L620,675 L600,685 L580,690 L560,685 L540,680 L520,670 L500,660 L480,645 L465,630 L455,615 L450,595 L455,575 L465,555 L475,535 L485,520 Z"/>
                                
                                <!-- Goa -->
                                <path class="state-path" data-state="Goa" 
                                      fill="#fff3e0" 
                                      d="M415,650 L440,655 L445,670 L440,685 L430,695 L415,700 L405,695 L400,685 L405,670 L410,660 Z"/>
                                
                                <!-- Karnataka -->
                                <path class="state-path" data-state="Karnataka" 
                                      fill="#f1f8e9" 
                                      d="M480,645 L580,660 L620,675 L650,695 L670,720 L680,745 L675,770 L665,790 L650,805 L630,815 L610,820 L590,815 L570,810 L550,800 L530,785 L515,770 L505,750 L500,730 L505,710 L515,690 L525,675 Z"/>
                                
                                <!-- Kerala -->
                                <path class="state-path" data-state="Kerala" 
                                      fill="#e0f2f1" 
                                      d="M500,730 L525,745 L535,770 L540,795 L545,820 L540,845 L535,870 L525,890 L515,905 L505,915 L495,920 L485,915 L480,905 L485,890 L490,875 L495,860 L500,845 L505,830 L500,815 L495,800 L490,785 L495,770 L500,755 Z"/>
                                
                                <!-- Tamil Nadu -->
                                <path class="state-path" data-state="Tamil Nadu" 
                                      fill="#fff8e1" 
                                      d="L650,695 L750,715 L800,735 L825,760 L840,785 L845,810 L840,835 L830,860 L815,880 L795,895 L770,905 L745,910 L720,905 L695,900 L675,890 L660,875 L650,860 L645,840 L650,820 L660,805 L675,795 L690,785 L705,775 L720,765 L735,755 L720,745 L705,735 L690,725 L675,715 Z"/>
                                
                                <!-- Andhra Pradesh -->
                                <path class="state-path" data-state="Andhra Pradesh" 
                                      fill="#fce4ec" 
                                      d="M620,675 L720,690 L760,710 L780,735 L790,760 L785,785 L775,805 L760,820 L740,830 L720,835 L700,830 L680,825 L665,815 L655,800 L650,785 L655,770 L665,755 L680,745 L695,735 L710,725 L695,715 L680,705 L665,695 Z"/>
                                
                                <!-- Telangana -->
                                <path class="state-path" data-state="Telangana" 
                                      fill="#e1f5fe" 
                                      d="L590,545 L650,560 L690,575 L710,595 L720,615 L715,635 L705,650 L690,660 L675,665 L660,660 L645,655 L635,645 L625,635 L620,620 L615,605 L610,590 L605,575 Z"/>
                                
                                <!-- Odisha -->
                                <path class="state-path" data-state="Odisha" 
                                      fill="#f3e5f5" 
                                      d="L700,505 L760,515 L800,530 L825,550 L840,575 L835,600 L825,620 L810,635 L795,645 L780,650 L765,645 L750,640 L735,630 L725,615 L720,600 L715,585 L710,570 L705,555 L700,540 Z"/>
                                
                                <!-- Chhattisgarh -->
                                <path class="state-path" data-state="Chhattisgarh" 
                                      fill="#fff3e0" 
                                      d="L650,575 L720,585 L760,595 L780,610 L790,630 L785,650 L775,665 L760,675 L745,680 L730,675 L715,670 L705,660 L695,645 L690,630 L685,615 L680,600 L675,585 Z"/>
                                
                                <!-- Jharkhand -->
                                <path class="state-path" data-state="Jharkhand" 
                                      fill="#e8f5e8" 
                                      d="L615,285 L680,295 L720,310 L740,330 L750,350 L745,370 L735,385 L720,395 L705,400 L690,395 L675,390 L665,380 L655,365 L650,350 L645,335 L640,320 L635,305 L630,290 Z"/>
                                
                                <!-- West Bengal -->
                                <path class="state-path" data-state="West Bengal" 
                                      fill="#e0f2f1" 
                                      d="L750,350 L820,365 L850,385 L870,410 L875,435 L870,460 L860,480 L845,495 L830,505 L815,510 L800,505 L785,500 L770,490 L760,475 L755,460 L750,445 L745,430 L740,415 L735,400 L730,385 Z"/>
                                
                                <!-- Bihar -->
                                <path class="state-path" data-state="Bihar" 
                                      fill="#fce4ec" 
                                      d="L640,225 L720,235 L780,250 L810,270 L830,295 L825,320 L815,340 L800,355 L785,365 L770,370 L755,365 L740,360 L730,350 L720,340 L710,325 L700,310 L695,295 L690,280 L685,265 L680,250 L675,235 Z"/>
                                
                                <!-- Assam -->
                                <path class="state-path" data-state="Assam" 
                                      fill="#fff8e1" 
                                      d="L830,295 L920,310 L960,330 L980,355 L975,380 L965,400 L950,415 L930,425 L910,430 L890,425 L875,420 L860,410 L850,395 L845,380 L840,365 L835,350 L830,335 L825,320 Z"/>
                                
                                <!-- Arunachal Pradesh -->
                                <path class="state-path" data-state="Arunachal Pradesh" 
                                      fill="#e8eaf6" 
                                      d="L980,355 L1050,375 L1080,400 L1090,430 L1085,460 L1075,485 L1060,500 L1040,510 L1020,515 L1000,510 L985,500 L975,485 L970,470 L975,455 L980,440 L985,425 L990,410 L995,395 L1000,380 Z"/>
                                
                                <!-- Nagaland -->
                                <path class="state-path" data-state="Nagaland" 
                                      fill="#f1f8e9" 
                                      d="L975,380 L1010,390 L1020,410 L1015,425 L1005,435 L995,440 L985,435 L980,425 L975,415 L970,405 L965,395 Z"/>
                                
                                <!-- Manipur -->
                                <path class="state-path" data-state="Manipur" 
                                      fill="#fce4ec" 
                                      d="L965,400 L990,410 L1000,425 L995,440 L985,450 L975,455 L970,445 L965,435 L960,425 L955,415 Z"/>
                                
                                <!-- Mizoram -->
                                <path class="state-path" data-state="Mizoram" 
                                      fill="#e0f2f1" 
                                      d="L950,415 L975,425 L980,440 L975,455 L965,465 L955,470 L950,460 L945,450 L940,440 L935,430 Z"/>
                                
                                <!-- Tripura -->
                                <path class="state-path" data-state="Tripura" 
                                      fill="#fff3e0" 
                                      d="L870,460 L895,470 L900,485 L895,495 L885,500 L875,495 L870,485 L865,475 Z"/>
                                
                                <!-- Meghalaya -->
                                <path class="state-path" data-state="Meghalaya" 
                                      fill="#f3e5f5" 
                                      d="L845,495 L875,505 L885,520 L880,535 L870,545 L860,550 L850,545 L845,535 L840,525 L835,515 Z"/>
                                
                                <!-- Sikkim -->
                                <path class="state-path" data-state="Sikkim" 
                                      fill="#e8f5e8" 
                                      d="L800,270 L820,275 L825,290 L820,305 L810,315 L800,320 L795,305 L790,290 L785,275 Z"/>
                                
                                <!-- Himachal Pradesh -->
                                <path class="state-path" data-state="Himachal Pradesh" 
                                      fill="#fff8e1" 
                                      d="L425,145 L485,155 L525,165 L550,180 L545,200 L535,215 L520,225 L505,230 L490,225 L475,220 L460,210 L450,195 L445,180 L440,165 Z"/>
                            </g>
                            
                            <!-- State Labels -->
                            <g id="state-labels">
                                <text class="state-label" x="425" y="120">JK</text>
                                <text class="state-label" x="385" y="150">PB</text>
                                <text class="state-label" x="445" y="170">HR</text>
                                <text class="state-label" x="485" y="185">UK</text>
                                <text class="state-label" x="550" y="250">UP</text>
                                <text class="state-label" x="380" y="300">RJ</text>
                                <text class="state-label" x="300" y="450">GJ</text>
                                <text class="state-label" x="565" y="480">MP</text>
                                <text class="state-label" x="545" y="615">MH</text>
                                <text class="state-label" x="425" y="675">GOA</text>
                                <text class="state-label" x="585" y="730">KA</text>
                                <text class="state-label" x="520" y="835">KL</text>
                                <text class="state-label" x="760" y="820">TN</text>
                                <text class="state-label" x="720" y="765">AP</text>
                                <text class="state-label" x="665" y="610">TG</text>
                                <text class="state-label" x="770" y="590">OD</text>
                                <text class="state-label" x="720" y="635">CG</text>
                                <text class="state-label" x="690" y="345">JH</text>
                                <text class="state-label" x="815" y="450">WB</text>
                                <text class="state-label" x="740" y="285">BR</text>
                                <text class="state-label" x="900" y="370">AS</text>
                                <text class="state-label" x="1040" y="455">AR</text>
                                <text class="state-label" x="995" y="415">NL</text>
                                <text class="state-label" x="975" y="440">MN</text>
                                <text class="state-label" x="955" y="445">MZ</text>
                                <text class="state-label" x="880" y="485">TR</text>
                                <text class="state-label" x="860" y="525">ML</text>
                                <text class="state-label" x="810" y="295">SK</text>
                                <text class="state-label" x="485" y="190">HP</text>
                            </g>
                        </svg>
                        
                        <!-- Tooltip -->
                        <div class="map-tooltip" id="mapTooltip"></div>
                    </div>
                    
                    <!-- Map Legend -->
                    <div class="map-legend">
                        <h6 class="mb-3"><strong>Map Legend</strong></h6>
                        <div class="d-flex flex-wrap">
                            <div class="legend-item">
                                <div class="legend-color" style="background: #e3f2fd;"></div>
                                <span>0 Reports</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: #bbdefb;"></div>
                                <span>1-5 Reports</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: #90caf9;"></div>
                                <span>6-10 Reports</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: #64b5f6;"></div>
                                <span>11+ Reports</span>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-muted"><small><strong>Instructions:</strong> Hover over states to see report counts. Click to view detailed reports.</small></p>
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
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // State post counts from PHP
            const stateCounts = <?php echo json_encode($counts_by_state); ?>;
            
            console.log('Map initialized with state counts:', stateCounts);
            
            // Tooltip element
            const $tooltip = $('#mapTooltip');
            
            // Add interactivity to state paths
            $('.state-path').each(function() {
                const $path = $(this);
                const stateName = $path.data('state');
                const count = stateCounts[stateName] || 0;
                
                // Color states based on report count
                let fillColor = '#e3f2fd'; // Default light blue
                if (count > 10) {
                    fillColor = '#64b5f6'; // Dark blue for 11+
                } else if (count > 5) {
                    fillColor = '#90caf9'; // Medium blue for 6-10
                } else if (count > 0) {
                    fillColor = '#bbdefb'; // Light medium blue for 1-5
                }
                
                $path.attr('fill', fillColor);
                
                // Mouse events for tooltip
                $path.on('mouseenter', function(e) {
                    const tooltipText = stateName + (count > 0 ? ` (${count} reports)` : ' (No reports)');
                    $tooltip.text(tooltipText).show();
                });
                
                $path.on('mousemove', function(e) {
                    $tooltip.css({
                        left: e.pageX + 10,
                        top: e.pageY - 30
                    });
                });
                
                $path.on('mouseleave', function() {
                    $tooltip.hide();
                });
                
                // Click handler
                $path.click(function() {
                    console.log('Clicked state:', stateName);
                    window.location.href = 'state.php?state=' + encodeURIComponent(stateName);
                });
                
                // Add visual feedback for states with reports
                if (count > 0) {
                    $path.css('cursor', 'pointer');
                } else {
                    $path.css('opacity', 0.7);
                }
            });
            
            console.log('Interactive map loaded successfully');
            console.log('States with data:', Object.keys(stateCounts).length);
            console.log('Total reports:', <?php echo array_sum($counts_by_state); ?>);
        });
    </script>
</body>
</html>