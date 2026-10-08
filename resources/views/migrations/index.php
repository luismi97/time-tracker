<div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl p-8">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 text-center">Migraciones</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 mb-6 text-center">Actualiza la base de datos a la version del codigo.</p>

    <?php if (isset($result)): ?>
        <?php if ($result['ran']): ?>
        <div class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-300">
            Ejecutadas correctamente:
            <ul class="mt-1 font-mono text-xs">
                <?php foreach ($result['ran'] as $file): ?><li><?= e($file) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php if ($result['failed']): ?>
        <div class="mb-4 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-800 dark:text-red-300 break-words">
            Fallo <span class="font-mono"><?= e($result['failed']) ?></span> y se detuvo la ejecucion:<br>
            <span class="font-mono text-xs"><?= e($result['error']) ?></span>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($status['pending']): ?>
        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Pendientes (<?= count($status['pending']) ?>):</p>
        <ul class="mt-1 mb-4 space-y-1 font-mono text-sm text-slate-600 dark:text-slate-300">
            <?php foreach ($status['pending'] as $file): ?><li><?= e($file) ?></li><?php endforeach; ?>
        </ul>
        <p class="mb-4 text-xs text-slate-400 dark:text-slate-500">Haz un respaldo de la base de datos antes de continuar (Cloudways &rarr; Backups).</p>
        <form method="POST" action="/migrate">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-semibold hover:bg-indigo-700 transition">
                Ejecutar migraciones pendientes
            </button>
        </form>
    <?php else: ?>
        <div class="rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-300 text-center">
            La base de datos esta al dia. No hay migraciones pendientes.
        </div>
    <?php endif; ?>

    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">Aplicadas: <?= count($status['applied']) ?></p>
</div>
