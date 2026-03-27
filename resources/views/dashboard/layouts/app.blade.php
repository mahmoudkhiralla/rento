<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - Rento</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Favicon (Tab Icon) -->
    <link rel="icon" type="image/svg+xml" href="/images/rento-logo.svg">
    <link rel="alternate icon" href="/favicon.ico" sizes="any">

    <style>
        :root {
            --primary-color: #2B7FE6;
            --secondary-color: #1E5BB8;
            --success-color: #22C55E;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #06B6D4;
            --light-bg: #F8F9FA;
            --sidebar-bg: #FFFFFF;
            --sidebar-width: 220px; /* موحّد مع عرض السايدبار الجديد */
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-left: none; /* إزالة الخط الجانبي بجوار كلمة رينتو */
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        /* Persisted logo/text sizes per request */
        .sidebar-logo {
            max-width: 30px;
            height: auto;
            margin-right: 5px;
        }

        .sidebar-text {
            max-width: 120px;
            height: auto;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            margin: 2px 10px;
            border-radius: 8px;
        }

        .menu-item:hover {
            background-color: #F3F4F6;
            color: var(--primary-color);
        }

        .menu-item.active {
            background-color: var(--primary-color);
            color: white;
        }

        .menu-item i {
            margin-left: 12px;
            width: 20px;
            text-align: center;
        }

        .menu-item .badge {
            margin-right: auto;
            font-size: 11px;
            padding: 3px 8px;
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-box {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 25px;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(43, 127, 230, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-card-title {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .stat-card-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .stat-card-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-card-change.positive {
            color: var(--success-color);
        }

        .stat-card-change.negative {
            color: var(--danger-color);
        }

        .stat-card-button {
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .stat-card-button:hover {
            background: var(--secondary-color);
        }

        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .chart-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-filter {
            padding: 6px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 13px;
            outline: none;
            cursor: pointer;
        }

        /* Summary Cards */
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .summary-card-title {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .summary-card-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }

            .search-box {
                max-width: 100%;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @stack('styles')
</head>
<body>

@include('dashboard.layouts.sidebar')

<div class="main-content">
    @include('dashboard.layouts.navbar')

    <div id="appContent" class="container-fluid p-4">
        @yield('content')
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (optional) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    // Mobile Sidebar Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    });
</script>

<script>
    // PJAX-like navigation for dashboard: load content without full page reload
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent double initialization on re-dispatched DOMContentLoaded
        if (window.__rentoPjaxBootstrapped) {
            return;
        }
        window.__rentoPjaxBootstrapped = true;

        // Global navigation state to avoid duplicate loads
        window.__rentoNavState = window.__rentoNavState || { isNavigating: false, controller: null };

        const appContent = document.getElementById('appContent');
        const loader = document.getElementById('pjaxLoader');
        
        function clearActiveSidebar() {
            document.querySelectorAll('.sidebar-menu > a.menu-item.active').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.menu-item-group .submenu .submenu-item.active').forEach(a => a.classList.remove('active'));
        }

        function closeOtherGroups(except) {
            document.querySelectorAll('.menu-item-group.open').forEach(g => {
                if (g !== except) g.classList.remove('open');
            });
        }

        function showLoader() {
            if (!loader) return;
            loader.classList.add('show');
            loader.setAttribute('aria-hidden', 'false');
        }

        function hideLoader() {
            if (!loader) return;
            loader.classList.remove('show');
            loader.setAttribute('aria-hidden', 'true');
        }

        function setActiveLink(link) {
            // Remove active from all menu anchors
            clearActiveSidebar();
            // Add active to clicked link
            if (link) {
                link.classList.add('active');
                // Ensure only the relevant group is open when clicking submenu
                const group = link.closest('.menu-item-group');
                if (group) {
                    closeOtherGroups(group);
                    group.classList.add('open');
                } else {
                    // Close any open groups if top-level link clicked
                    closeOtherGroups(null);
                }
            }
        }

        function applyHeadAssets(doc) {
            if (!doc) return;
            // Remove previously injected dynamic assets
            document.head.querySelectorAll('style[data-pjax-style], link[rel="stylesheet"][data-pjax-style]').forEach(el => el.remove());

            // Merge stylesheet links from fetched document head (dedupe by href)
            const existingLinks = new Set(Array.from(document.head.querySelectorAll('link[rel="stylesheet"]')).map(l => l.href));
            doc.head && doc.head.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                const href = link.href || link.getAttribute('href');
                if (!href) return;
                if (!existingLinks.has(href)) {
                    const newLink = link.cloneNode(true);
                    newLink.setAttribute('data-pjax-style', 'true');
                    document.head.appendChild(newLink);
                }
            });

            // Merge inline style tags from fetched document head (dedupe by content)
            const existingStyleContent = new Set(Array.from(document.head.querySelectorAll('style')).map(s => (s.textContent || '').trim()));
            doc.head && doc.head.querySelectorAll('style').forEach(style => {
                const content = (style.textContent || '').trim();
                if (!content) return;
                if (!existingStyleContent.has(content)) {
                    const newStyle = document.createElement('style');
                    newStyle.textContent = content;
                    newStyle.setAttribute('data-pjax-style', 'true');
                    document.head.appendChild(newStyle);
                }
            });
        }

        function applyPushedScripts(doc) {
            if (!doc) return;
            // Remove previously injected pushed scripts
            document.querySelectorAll('script[data-pjax-push]').forEach(s => s.remove());

            // Collect existing scripts to dedupe
            const existingSrcs = new Set(Array.from(document.querySelectorAll('script[src]')).map(s => s.src));
            const existingInline = new Set(Array.from(document.querySelectorAll('script:not([src])')).map(s => (s.textContent || '').trim()));

            // Append scripts that are not inside the content container (e.g., @push('scripts'))
            const newScripts = Array.from(doc.querySelectorAll('body script')).filter(s => !s.closest('.container-fluid.p-4'));
            for (const oldScript of newScripts) {
                if (oldScript.src) {
                    if (existingSrcs.has(oldScript.src)) continue;
                } else {
                    const content = (oldScript.textContent || '').trim();
                    if (!content || existingInline.has(content)) continue;
                }
                const s = document.createElement('script');
                if (oldScript.src) s.src = oldScript.src; else s.textContent = oldScript.textContent;
                s.async = false;
                s.setAttribute('data-pjax-push', 'true');
                document.body.appendChild(s);
            }
        }

        async function loadPage(url, linkEl = null, push = true) {
            const navState = window.__rentoNavState;
            if (navState && navState.isNavigating) {
                return; // ignore while a navigation is in progress
            }
            navState.isNavigating = true;
            // Abort previous pending navigation if any
            try {
                if (navState.controller) navState.controller.abort();
            } catch (_) {}
            navState.controller = new AbortController();

            showLoader();
            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-PJAX': 'true'
                    },
                    credentials: 'same-origin',
                    signal: navState.controller.signal
                });
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContainer = doc.querySelector('.container-fluid.p-4');
                if (!newContainer || !appContent) {
                    // Fallback to normal navigation if structure not found
                    window.location.href = url;
                    return;
                }
                // Replace inner content
                appContent.innerHTML = newContainer.innerHTML;

                // Apply head-level assets like @push('styles') from fetched page
                applyHeadAssets(doc);

                // Update document title from fetched page
                if (doc.title) {
                    document.title = doc.title;
                }

                // Apply @push('scripts') from fetched page (outside content container)
                applyPushedScripts(doc);

                // Execute any inline or external scripts inside the new content
                // Remove previously injected inline content scripts to avoid duplicates
                document.querySelectorAll('script[data-pjax-inline]').forEach(s => s.remove());
                const existingSrcs = new Set(Array.from(document.querySelectorAll('script[src]')).map(s => s.src));
                const scripts = Array.from(appContent.querySelectorAll('script'));
                for (const oldScript of scripts) {
                    const s = document.createElement('script');
                    if (oldScript.src) {
                        if (existingSrcs.has(oldScript.src)) {
                            oldScript.remove();
                            continue; // skip duplicate external scripts
                        }
                        s.src = oldScript.src;
                    } else {
                        const content = (oldScript.textContent || '').trim();
                        // If an identical inline script already exists, skip
                        const hasSameInline = Array.from(document.querySelectorAll('script:not([src])')).some(x => (x.textContent || '').trim() === content);
                        if (hasSameInline) {
                            oldScript.remove();
                            continue;
                        }
                        s.textContent = oldScript.textContent;
                        s.setAttribute('data-pjax-inline', 'true');
                    }
                    s.async = false;
                    oldScript.remove();
                    document.body.appendChild(s);
                }

                // Re-dispatch DOMContentLoaded for scripts that rely on it (common in pages)
                try {
                    const ev = new Event('DOMContentLoaded');
                    document.dispatchEvent(ev);
                } catch (err) {
                    // no-op
                }
                // Dispatch a custom event hook for page-ready if needed by future code
                document.dispatchEvent(new Event('rento:page-loaded'));

                // Push history state
                if (push) {
                    history.pushState({ url }, '', url);
                }

                // Update active state in sidebar
                setActiveLink(linkEl);
            } catch (e) {
                console.error('Navigation failed:', e);
                window.location.href = url;
            } finally {
                hideLoader();
                const navStateDone = window.__rentoNavState;
                if (navStateDone) {
                    navStateDone.isNavigating = false;
                    navStateDone.controller = null;
                }
            }
        }

        function handleSidebarClick(e) {
            const navState = window.__rentoNavState;
            if (navState && navState.isNavigating) {
                e.preventDefault();
                return; // ignore clicks during navigation
            }
            const link = e.currentTarget;
            const url = link.getAttribute('href');
            if (!url) return;
            // Only intercept dashboard links
            if (!url.includes('/dashboard')) return;

            // Toggle group headers: open the group; if it's an anchor with a real href, also navigate
            const parentGroup = link.closest('.menu-item-group');
            if (parentGroup && link.classList.contains('toggle')) {
                e.preventDefault();
                closeOtherGroups(parentGroup);
                parentGroup.classList.add('open');

                const isAnchor = link.tagName.toLowerCase() === 'a';
                const href = link.getAttribute('href');
                const navigable = isAnchor && href && href !== '#' && href.trim() !== '';

                if (navigable) {
                    loadPage(url, link, true);
                } else {
                    // Pure toggle button: clear previous active state
                    clearActiveSidebar();
                }
                return;
            }

            e.preventDefault();
            loadPage(url, link, true);
        }

        // Dynamic navigation disabled: let anchors perform normal full-page reloads
    });
</script>

    @stack('scripts')
</body>
</html>
