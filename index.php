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
            /* reduce bottom padding to remove extra gap below the map */
            padding: 60px 0 0 0;
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
            max-width: 900px;
            height: auto;
            display: block; /* avoid inline-gap and better scaling */
            /* remove fixed min-height to avoid unwanted vertical gap */
            min-height: 0;
        }
            /* zoom controls */
            .map-zoom-controls {
                position: absolute;
                top: 8px;
                right: 8px;
                z-index: 1200;
                display: flex;
                gap: 6px;
            }
            .map-zoom-controls button {
                background: rgba(255,255,255,0.95);
                border: 1px solid #ddd;
                padding: 6px 8px;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 600;
            }
            .map-zoom-controls button:focus {
                outline: 2px solid #667eea;
            }
        .state-path {
            cursor: pointer;
            transition: all 0.3s ease;
            stroke-width: 1 !important;
            stroke: #5c5f64 !important; /* outline color requested */
            stroke-linejoin: round !important;
            stroke-linecap: round !important;
            pointer-events: auto; /* ensure paths receive pointer events */
        }
        .state-path:hover {
            opacity: 0.8;
            stroke-width: 2 !important;
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
        /* allow the svg to overflow its container if paths extend beyond */
        #india-map {
            overflow: visible;
            z-index: 1;
            position: relative;
            margin-bottom: 0; /* ensure no extra margin below the svg container */
        }

        /* focus styles for accessibility when paths are keyboard-focusable */
        .state-path:focus {
            outline: 2px dashed rgba(0,0,0,0.2);
        }
            /* make the inlined SVG responsive and centered; default height 500px */
            #india-map svg {
                /* width: 100% !important; */
                height: 700px !important;
                display: block !important;
                margin: 0 auto !important;
                max-width: 900px;
            }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <strong>India's Cry for Justice</strong>
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
                    <li class="nav-item">
                        <a class="nav-link" href="incidents.php">Incidents</a>
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
            <h1 class="display-4 mb-4">India's Cry for Justice </h1>
            <p class="lead mb-4">Track and monitor persecution incidents across Indian states</p>
            <a href="#map" class="btn btn-light btn-lg">Explore Interactive Map</a>
        </div>
    </section>

    <!-- Map removed per user request -->
    <section id="map" class="map-container">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Explore Reports</h2>
                    <p class="lead">Browse reported incidents by using the <a href="incidents.php">Incidents</a> page or use the filters to find reports for a specific state.</p>
                </div>
            </div>
            <!-- Inline SVG map: clicking a state opens state.php?state=StateName -->
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    <div id="india-map" style="position:relative; width:100%; max-width:900px;">
                        <!-- state-count overlays are appended to this div -->
                        <div id="state-counts" aria-hidden="true"></div>
                        <!-- Begin embedded SVG -->
                        <?php
                        // Inline the SVG file so we can attach click handlers to paths.
                        $svg_path = __DIR__ . '/mapsvg/india.svg';
                        if (file_exists($svg_path)) {
                            $svg = file_get_contents($svg_path);
                            // strip XML declaration if present
                            $svg = preg_replace('/^\s*<\?xml[^>]+>\s*/i', '', $svg);
                            // output SVG directly (it's sanitized/internal asset)
                            echo $svg;
                        } else {
                            echo '<p class="text-muted">Map unavailable.</p>';
                        }
                        ?>
                        <!-- Zoom controls -->
                        <div class="map-zoom-controls" role="group" aria-label="Map zoom controls">
                            <button id="zoom-in" type="button" title="Zoom in" aria-label="Zoom in">+</button>
                            <button id="zoom-out" type="button" title="Zoom out" aria-label="Zoom out">−</button>
                            <button id="zoom-reset" type="button" title="Reset" aria-label="Reset zoom">100%</button>
                        </div>
                        <!-- Legend: shows color scale for low/medium/high counts -->
                            <div id="map-legend" class="map-legend" aria-hidden="false" style="margin-top:12px; display:flex; gap:12px; justify-content:center; align-items:center;">
                                <div class="legend-item"><span class="legend-swatch" id="legend-low" style="width:28px;height:16px;display:inline-block;border:1px solid #ccc;margin-right:8px;background:#ffffff"></span><span class="legend-label" id="legend-low-label">Low</span></div>
                                <div class="legend-item"><span class="legend-swatch" id="legend-mid" style="width:28px;height:16px;display:inline-block;border:1px solid #ccc;margin-right:8px;background:#ffb84d"></span><span class="legend-label" id="legend-mid-label">Medium</span></div>
                                <div class="legend-item"><span class="legend-swatch" id="legend-high" style="width:28px;height:16px;display:inline-block;border:1px solid #ccc;margin-right:8px;background:#ff3333"></span><span class="legend-label" id="legend-high-label">High</span></div>
                            </div>
                        <!-- End embedded SVG -->
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
                        <div>Total Incidents</div>
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
                        <div>Active Categories</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $recent_posts; ?></div>
                        <div>Incidents this Month</div>
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
                    <h5>India's Cry for Justice</h5>
                    <p>Monitoring and documenting persecution incidents across India</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 India's Cry for Justice. All rights reserved.</p>
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
                // Attach map click handlers when SVG is present
                var counts = <?php echo json_encode($counts_by_state); ?> || {};
                // normalized map for robust lookup (lowercase keys)
                var normalizedCounts = {};
                Object.keys(counts).forEach(function(k) {
                    if (!k) return;
                    normalizedCounts[String(k).trim().toLowerCase()] = counts[k];
                });

                // prepare min/max for non-zero counts to build a yellow->red gradient
                var countValues = Object.keys(normalizedCounts).map(function(k){
                    return Number(normalizedCounts[k]) || 0;
                }).filter(function(v){ return v > 0; });
                var minCount = countValues.length ? Math.min.apply(null, countValues) : 0;
                var maxCount = countValues.length ? Math.max.apply(null, countValues) : 0;

                function toHex(n) {
                    var h = n.toString(16);
                    return h.length === 1 ? '0' + h : h;
                }

                // interpolate between yellow (255,255,102) and red (255,51,51)
                function colorForCount(count) {
                    if (!count || count <= 0) return '#ffffff';
                    if (maxCount === minCount) {
                        // single non-zero value, lean towards orange
                        return '#ff8a4d';
                    }
                    var t = (count - minCount) / (maxCount - minCount);
                    // clamp
                    t = Math.max(0, Math.min(1, t));
                    var r = 255;
                    var g = Math.round(255 - t * (255 - 51)); // 255 -> 51
                    var b = Math.round(102 - t * (102 - 51)); // 102 -> 51
                    return '#' + toHex(r) + toHex(g) + toHex(b);
                }

                // Map zoom state and helpers
                var mapScale = 1;
                var minMapScale = 0.6;
                var maxMapScale = 3;
                var mapScaleStep = 0.2;

                function setMapScale(s) {
                    mapScale = Math.max(minMapScale, Math.min(maxMapScale, s));
                    // Apply CSS transform to the SVG to zoom
                    $('#india-map svg').css({ transform: 'scale(' + mapScale + ')', transformOrigin: '50% 50%' });
                    // After scaling, recalc badges and legend (bounding boxes change)
                    setTimeout(function() { placeBadges(); attachTooltipHandlers(); updateLegend(); }, 80);
                }

                function zoomIn() { setMapScale(mapScale + mapScaleStep); }
                function zoomOut() { setMapScale(mapScale - mapScaleStep); }
                function resetZoom() { setMapScale(1); }

                function placeBadges() {
                    $('#state-counts').empty();
                    var $container = $('#india-map');
                    var containerRect = $container[0].getBoundingClientRect();

                    // Find path elements that have a title attribute (state name)
                    $('#india-map svg').find('path[title], polygon[title], g[title]').each(function() {
                        var el = this;
                        var $el = $(el);
                        var stateName = ($el.attr('title') || '').trim();
                        if (!stateName) return;

                        var count = Number(normalizedCounts[stateName.trim().toLowerCase()] || counts[stateName] || 0) || 0;

                        // set fill color based on count (white for zero, yellow->red for >0)
                        try {
                            var fillColor = colorForCount(count);
                            if (el && el.style) el.style.fill = fillColor;
                            else $el.attr('fill', fillColor);
                        } catch (e) {
                            // ignore coloring errors
                        }

                        // make it keyboard accessible and clickable
                        $el.addClass('state-path').attr('tabindex', 0).css('pointer-events', 'auto');
                        // set accessible role/label
                        $el.attr('role', 'link').attr('aria-label', stateName + ' reports');

                        function resolveStateFromElement(elem) {
                            var name = (elem.getAttribute && elem.getAttribute('title')) || '';
                            if (name) return name.trim();
                            // check for <title> child node inside the SVG element
                            var titleEl = elem.querySelector && elem.querySelector('title');
                            if (titleEl && titleEl.textContent) return titleEl.textContent.trim();
                            // check aria-label or aria-labelledby
                            var aria = elem.getAttribute && (elem.getAttribute('aria-label') || elem.getAttribute('aria-labelledby'));
                            if (aria) return aria.trim();
                            // try parent nodes (some maps use groups)
                            var p = elem.parentNode;
                            while (p && p !== document && p !== document.documentElement) {
                                if (p.getAttribute) {
                                    var t = p.getAttribute('title') || (p.querySelector && (p.querySelector('title') && p.querySelector('title').textContent));
                                    if (t) return t.trim();
                                }
                                p = p.parentNode;
                            }
                            return '';
                        }

                        $el.off('click.stateNav').on('click.stateNav', function() {
                            var resolved = resolveStateFromElement(this) || stateName;
                            if (!resolved) resolved = stateName;
                            // navigate to state page; state.php will perform a tolerant LIKE fallback if exact match fails
                            window.location.href = 'state.php?state=' + encodeURIComponent(resolved);
                        });
                        $el.off('keydown.stateNav').on('keydown.stateNav', function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                $(this).trigger('click.stateNav');
                            }
                        });

                        if (count > 0) {
                            // compute a reasonable position for the badge using element bounding box
                            var rect = el.getBoundingClientRect();
                            var left = rect.left + rect.width / 2 - containerRect.left;
                            var top = rect.top + rect.height / 2 - containerRect.top;
                            var $badge = $('<div class="state-count" role="img" aria-label="' + stateName + ' has ' + count + ' reports">' + count + '</div>');
                            $badge.css({ left: left + 'px', top: top + 'px', position: 'absolute' });
                            $badge.css('pointer-events','none');
                            $('#state-counts').append($badge);
                        }
                    });
                }

                // Tooltip element
                var $tooltip = $('<div class="map-tooltip" role="status" aria-hidden="true" style="position:absolute; display:none; z-index:9999; background:rgba(0,0,0,0.8); color:#fff; padding:6px 8px; border-radius:4px; font-size:13px; white-space:nowrap; pointer-events:none;"></div>').appendTo('#india-map');

                // Place badges after a short delay to allow SVG to render, and on resize
                function updateLegend() {
                    // update legend swatches based on min/max
                    var $low = $('#legend-low');
                    var $mid = $('#legend-mid');
                    var $high = $('#legend-high');
                    if (!maxCount || maxCount === 0) {
                        $low.css('background','#ffffff');
                        $mid.css('background','#ffffff');
                        $high.css('background','#ffffff');
                        $('#legend-low-label').text('No reports');
                        $('#legend-mid-label').text('');
                        $('#legend-high-label').text('');
                        return;
                    }
                    var lowVal = minCount || 1;
                    var midVal = Math.round((minCount + maxCount) / 2) || Math.round(maxCount/2);
                    $low.css('background', colorForCount(lowVal));
                    $mid.css('background', colorForCount(midVal));
                    $high.css('background', colorForCount(maxCount));
                    // Use static textual labels (no numeric counts)
                    $('#legend-low-label').text('Low');
                    $('#legend-mid-label').text('Medium');
                    $('#legend-high-label').text('High');
                }

                setTimeout(function() { placeBadges(); attachTooltipHandlers(); updateLegend(); }, 250);
                $(window).on('resize', function() { setTimeout(function() { placeBadges(); attachTooltipHandlers(); updateLegend(); }, 150); });

                // wire zoom control buttons
                $('#zoom-in').on('click', function(e) { e.preventDefault(); zoomIn(); });
                $('#zoom-out').on('click', function(e) { e.preventDefault(); zoomOut(); });
                $('#zoom-reset').on('click', function(e) { e.preventDefault(); resetZoom(); });

                // Tooltip handlers
                function attachTooltipHandlers() {
                    $('#india-map svg').find('.state-path').each(function() {
                        var $el = $(this);
                        var stateName = ($el.attr('title') || '').trim();
                        if (!stateName) return;

                        $el.off('.stateTooltip');
                        $el.on('mouseenter.stateTooltip', function(e) {
                            var key = stateName.trim().toLowerCase();
                            var cnt = normalizedCounts[key] || counts[stateName] || 0;
                            $tooltip.text(stateName + (cnt ? ': ' + cnt + ' reports' : ': 0 reports')).show().attr('aria-hidden','false');
                        }).on('mousemove.stateTooltip', function(e) {
                            var containerOffset = $('#india-map').offset();
                            var x = e.pageX - containerOffset.left;
                            var y = e.pageY - containerOffset.top;
                            $tooltip.css({ left: x + 'px', top: (y - 18) + 'px' });
                        }).on('mouseleave.stateTooltip', function() {
                            $tooltip.hide().attr('aria-hidden','true');
                        }).on('focus.stateTooltip', function() {
                            var key = stateName.trim().toLowerCase();
                            var cnt = normalizedCounts[key] || counts[stateName] || 0;
                            // position above element center
                            var rect = this.getBoundingClientRect();
                            var containerRect = $('#india-map')[0].getBoundingClientRect();
                            var left = rect.left + rect.width/2 - containerRect.left;
                            var top = rect.top - containerRect.top - 8;
                            $tooltip.text(stateName + (cnt ? ': ' + cnt + ' reports' : ': 0 reports')).css({ left: left + 'px', top: top + 'px' }).show().attr('aria-hidden','false');
                        }).on('blur.stateTooltip', function() {
                            $tooltip.hide().attr('aria-hidden','true');
                        });
                    });
                }

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