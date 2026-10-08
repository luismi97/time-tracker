<?php
$statusBadge = fn (string $status) => $status === 'active'
    ? '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300">Activa</span>'
    : '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400">Inactiva</span>';
?>
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Cada empresa es independiente: tiene sus propios empleados, registros, reportes y configuracion.
        </p>
        <a href="/super/companies/create" class="shrink-0 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 text-center">
            + Nueva empresa
        </a>
    </div>

    <!-- Tabla: solo visible en pantallas medianas y grandes -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
                    <th class="py-3 px-5">Empresa</th>
                    <th class="py-3 px-5">Codigo</th>
                    <th class="py-3 px-5">Empleados</th>
                    <th class="py-3 px-5">Admins</th>
                    <th class="py-3 px-5">Estado</th>
                    <th class="py-3 px-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($companies as $companyRow): ?>
                <tr>
                    <td class="py-3 px-5 font-medium text-slate-800 dark:text-slate-100"><?= e($companyRow['name']) ?></td>
                    <td class="py-3 px-5 font-mono text-slate-500 dark:text-slate-400"><?= e($companyRow['slug']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= (int) $companyRow['active_employees_count'] ?> activos / <?= (int) $companyRow['employees_count'] ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= (int) $companyRow['admins_count'] ?></td>
                    <td class="py-3 px-5"><?= $statusBadge($companyRow['status']) ?></td>
                    <td class="py-3 px-5">
                        <div class="flex justify-end gap-3">
                            <form method="POST" action="/super/companies/<?= $companyRow['id'] ?>/enter">
                                <?= csrf_field() ?>
                                <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Gestionar</button>
                            </form>
                            <a href="/super/companies/<?= $companyRow['id'] ?>/edit" class="text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white font-medium">Editar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$companies): ?>
                <tr><td colspan="6" class="py-6 text-center text-slate-400 dark:text-slate-500">Aun no hay empresas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tarjetas: solo visibles en pantallas pequenas (moviles) -->
    <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
        <?php foreach ($companies as $companyRow): ?>
        <div class="p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="font-medium text-slate-800 dark:text-slate-100 truncate"><?= e($companyRow['name']) ?></p>
                    <p class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= e($companyRow['slug']) ?></p>
                </div>
                <span class="shrink-0"><?= $statusBadge($companyRow['status']) ?></span>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Empleados</dt>
                    <dd class="dark:text-slate-300"><?= (int) $companyRow['active_employees_count'] ?> activos / <?= (int) $companyRow['employees_count'] ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Admins</dt>
                    <dd class="dark:text-slate-300"><?= (int) $companyRow['admins_count'] ?></dd>
                </div>
            </dl>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <form method="POST" action="/super/companies/<?= $companyRow['id'] ?>/enter">
                    <?= csrf_field() ?>
                    <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Gestionar</button>
                </form>
                <a href="/super/companies/<?= $companyRow['id'] ?>/edit" class="text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white font-medium">Editar</a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$companies): ?>
        <p class="py-6 text-center text-slate-400 dark:text-slate-500">Aun no hay empresas.</p>
        <?php endif; ?>
    </div>
</div>
