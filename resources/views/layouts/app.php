<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? '') ?> &middot; <?= e(site_name()) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?php require VIEWS_PATH . '/partials/theme-init.php'; ?>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors">
    <div class="flex min-h-screen">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 -translate-x-full transform bg-slate-900 text-slate-100 transition-transform duration-200 lg:static lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 px-6 text-lg font-bold border-b border-slate-800">
                <?php if (site_logo()): ?>
                    <img src="<?= e(site_logo()) ?>" alt="Logo" class="h-8 w-8 rounded object-cover">
                <?php endif; ?>
                <span class="truncate"><?= e(site_name()) ?></span>
            </div>
            <nav class="px-3 py-4 space-y-1 text-sm">
                <?php if (\App\Core\Auth::isAdmin()): ?>
                    <?= nav_link('/admin/dashboard', 'Dashboard') ?>
                    <?= nav_link('/admin/employees', 'Empleados') ?>
                    <?= nav_link('/admin/attendance', 'Registros') ?>
                    <?= nav_link('/admin/reports', 'Reportes') ?>
                    <?= nav_link('/admin/settings', 'Configuracion') ?>
                <?php else: ?>
                    <?= nav_link('/employee/dashboard', 'Mi panel') ?>
                    <?= nav_link('/employee/attendance', 'Mis registros') ?>
                    <?= nav_link('/employee/profile', 'Mi perfil') ?>
                <?php endif; ?>
            </nav>
        </aside>
        <div id="sidebar-overlay" class="fixed inset-0 z-20 hidden bg-black/40 lg:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="flex h-16 items-center justify-between gap-2 border-b border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 sm:px-4 lg:px-8 transition-colors">
                <button id="sidebar-toggle" class="shrink-0 lg:hidden text-slate-600 dark:text-slate-300" aria-label="Abrir menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="flex-1 min-w-0 truncate text-center lg:text-left text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100"><?= e($title ?? '') ?></h1>
                <div class="shrink-0 flex items-center gap-1 sm:gap-4">
                    <button id="theme-toggle" type="button" aria-label="Cambiar tema" title="Cambiar tema"
                            class="rounded-lg p-2 text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                    <form method="POST" action="/logout">
                        <?= csrf_field() ?>
                        <button type="submit" class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 whitespace-nowrap">Cerrar sesion</button>
                    </form>
                </div>
            </header>
            <main class="flex-1 p-4 lg:p-8">
                <?php require VIEWS_PATH . '/partials/alerts.php'; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
