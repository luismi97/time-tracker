<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Error') ?> &middot; <?= e(site_name()) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?php require VIEWS_PATH . '/partials/theme-init.php'; ?>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950 flex items-center justify-center px-4 transition-colors">
    <div class="max-w-md text-center dark:text-slate-100">
        <?= $content ?>
    </div>
</body>
</html>
