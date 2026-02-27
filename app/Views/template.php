<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Admin' ?> - AdminPanel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

    <style>
        :root {
            --sidebar-w: 240px;
            --navbar-h: 58px;
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #eff6ff;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-sub: #64748b;
            --text-muted: #94a3b8;
            --bg: #f8fafc;
            --secondary: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --accent: #0f4c92;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #fff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .main-wrap {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            height: var(--navbar-h);
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 30px;
            height: 30px;
            background: var(--primary);
            border-radius: 7px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 15px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 14px 10px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 10px 8px 4px;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 7px;
            text-decoration: none;
            color: var(--text-sub);
            font-size: 13.5px;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 1px;
        }

        .nav-link-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-link-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link-item i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 99px;
        }

        /* Sub-menu */
        .nav-sub {
            list-style: none;
            overflow: hidden;
            max-height: 0;
            transition: max-height .3s ease;
        }

        .nav-sub.open {
            max-height: 300px;
        }

        .nav-sub .nav-link-item {
            padding-left: 40px;
            font-size: 13px;
        }

        .chevron {
            margin-left: auto;
            font-size: 11px;
            color: var(--text-muted);
            transition: transform .2s;
        }

        .nav-link-item[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        /* Footer */
        .sidebar-footer {
            padding: 10px;
            border-top: 1px solid var(--border);
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: 7px;
            text-decoration: none;
            transition: background .15s;
        }

        .user-row:hover {
            background: var(--primary-light);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky;
            top: 0;
            height: var(--navbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 22px;
            gap: 12px;
            z-index: 1030;
        }

        .topbar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border);
            color: var(--text-sub);
            width: 34px;
            height: 34px;
            border-radius: 7px;
            place-items: center;
            font-size: 18px;
            cursor: pointer;
            transition: background .15s;
        }

        .topbar-toggle:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }

        .topbar-btn {
            background: none;
            border: 1px solid var(--border);
            color: var(--text-sub);
            width: 34px;
            height: 34px;
            border-radius: 7px;
            display: grid;
            place-items: center;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            transition: background .15s, color .15s;
        }

        .topbar-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border: 1px solid var(--border);
            border-radius: 99px;
            background: none;
            cursor: pointer;
            color: var(--text);
            font-family: inherit;
            transition: background .15s, border-color .15s;
        }

        .topbar-user:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }

        .topbar-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--primary);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
        }

        .topbar-uname {
            font-size: 13px;
            font-weight: 600;
        }

        /* Dropdown */
        .dd-menu {
            background: #fff !important;
            border: 1px solid var(--border) !important;
            border-radius: 10px !important;
            padding: 6px !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08) !important;
            min-width: 170px;
        }

        .dd-menu .dropdown-item {
            font-size: 13px !important;
            color: var(--text-sub) !important;
            border-radius: 6px !important;
            padding: 8px 10px !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dd-menu .dropdown-item:hover {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
        }

        .dd-menu .dropdown-divider {
            border-color: var(--border) !important;
            margin: 4px 0 !important;
        }

        /* ── Page content ── */
        .page-content {
            flex: 1;
            padding: 26px 26px 40px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── Overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .3);
            z-index: 1039;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-wrap {
                margin-left: 0 !important;
            }

            .topbar-toggle {
                display: grid;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .topbar-uname {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .page-content {
                padding: 16px 14px 32px;
            }
        }

        /* ── Utility card ── */
        .card-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>

<body>

    <!-- ── Sidebar ── -->
    <aside class="sidebar" id="sidebar">

        <a href="<?= base_url('/dashboard') ?>" class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-grid-fill"></i></div>
            <span class="brand-name">Admin FT</span>
        </a>

        <nav class="sidebar-nav">

            <div class="nav-label">Menu</div>

            <a href="<?= base_url('admin/dashboard') ?>"
                class="nav-link-item <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="nav-label">Mapping</div>
            <a href="<?= base_url('admin/mapping_cpl') ?>" class="nav-link-item <?= uri_string() === 'admin/mapping_cpl' ? 'active' : '' ?>">
                <i class="fas fa-random"></i> MK x CPL x PI
            </a>

            <a href="<?= base_url('admin/perkuliahan') ?>" class="nav-link-item <?= uri_string() === 'admin/perkuliahan' ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-teacher"></i> Perkuliahan
            </a>
            <div class="nav-label">Master</div>

            <a href="<?= base_url('admin/users') ?>" class="nav-link-item <?= uri_string() === 'admin/users' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Users
            </a>

            <a href="<?= base_url('admin/kurikulum') ?>" class="nav-link-item <?= uri_string() === 'admin/kurikulum' ? 'active' : '' ?>">
                <i class="fas fa-book-open"></i> Kurikulum
            </a>

            <a href="<?= base_url('admin/prodi') ?>" class="nav-link-item <?= uri_string() === 'admin/prodi' ? 'active' : '' ?>">
                <i class="fas fa-university"></i> Prodi
            </a>

            <a href="<?= base_url('admin/mk') ?>" class="nav-link-item <?= uri_string() === 'admin/mk' ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Matakuliah
            </a>

            <a href="<?= base_url('admin/cpl') ?>" class="nav-link-item <?= uri_string() === 'admin/cpl' ? 'active' : '' ?>">
                <i class="fas fa-project-diagram"></i> CPL
            </a>

            <a href="<?= base_url('admin/pi') ?>" class="nav-link-item <?= uri_string() === 'admin/pi' ? 'active' : '' ?>">
                <i class="fas fa-bullseye"></i> PI
            </a>

            <div class="nav-label">Portofolio</div>
            <a href="<?= base_url('portofolio') ?>" class="nav-link-item <?= uri_string() === 'portofolio' ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i> Portofolio MK
            </a>
        </nav>

    </aside>

    <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- ── Main ── -->
    <div class="main-wrap" id="mainWrap">

        <header class="topbar">
            <button class="topbar-toggle" onclick="openSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <span class="topbar-title"><?= $this->renderSection('title') ?? 'Dashboard' ?></span>

            <div class="topbar-actions">
                <div class="dropdown">
                    <button class="topbar-user" data-bs-toggle="dropdown">
                        <div class="topbar-avatar">AD</div>
                        <span class="topbar-uname"><?= session()->get('nama_lengkap') ?></span>
                        <i class="bi bi-chevron-down" style="font-size:10px;color:var(--text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dd-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('/logout') ?>"
                                style="color:#ef4444!important;">
                                <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </header>

        <main class="page-content">
            <?= $this->renderSection('content') ?>
        </main>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('overlay').classList.add('show');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('show');
        }

        function toggleSub(id, el) {
            const sub = document.getElementById(id);
            const open = sub.classList.contains('open');
            document.querySelectorAll('.nav-sub.open').forEach(s => s.classList.remove('open'));
            document.querySelectorAll('[aria-expanded="true"]').forEach(a => a.setAttribute('aria-expanded', 'false'));
            if (!open) {
                sub.classList.add('open');
                el.setAttribute('aria-expanded', 'true');
            }
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) closeSidebar();
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>