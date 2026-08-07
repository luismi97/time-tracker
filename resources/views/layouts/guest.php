<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? site_name()) ?> &middot; <?= e(site_name()) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?php require VIEWS_PATH . '/partials/theme-init.php'; ?>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950 flex items-center justify-center px-4 transition-colors">
    <button id="theme-toggle" type="button" aria-label="Cambiar tema" title="Cambiar tema"
            class="fixed top-4 right-4 rounded-lg p-2 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>
    <div class="w-full max-w-md">
        <?php require VIEWS_PATH . '/partials/alerts.php'; ?>
        <?= $content ?>
    </div>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
