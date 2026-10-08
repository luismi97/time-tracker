<?php $kioskUrl = '/kiosk/' . rawurlencode($company['slug']); ?>
<div class="space-y-6 max-w-3xl">
    <!-- Datos de la empresa -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="font-semibold text-slate-800 dark:text-slate-100">Datos de la empresa</h2>
            <form method="POST" action="/super/companies/<?= $company['id'] ?>/enter">
                <?= csrf_field() ?>
                <button class="rounded-lg bg-slate-800 dark:bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 dark:hover:bg-slate-600">
                    Gestionar empleados, reportes y configuracion &rarr;
                </button>
            </form>
        </div>
        <form method="POST" action="/super/companies/<?= $company['id'] ?>" class="space-y-4">
            <?= csrf_field() ?>
            <?php require VIEWS_PATH . '/super/companies/_form-fields.php'; ?>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Kiosco: <a href="<?= e($kioskUrl) ?>" target="_blank" class="font-mono underline"><?= e($kioskUrl) ?></a>
                <span class="text-xs">(funciona si la empresa activo el modo kiosco en su configuracion)</span>
            </p>
            <div class="flex justify-end gap-3">
                <a href="/super/companies" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Volver</a>
                <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
            </div>
        </form>
    </div>

    <!-- Administradores -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-1">Administradores</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Cuentas que gestionan esta empresa. Solo ven los datos de esta empresa.</p>

        <div class="divide-y divide-gray-100 dark:divide-slate-800 border-y border-gray-100 dark:border-slate-800 mb-5">
            <?php foreach ($admins as $admin): ?>
            <div class="py-3 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-slate-800 dark:text-slate-100 truncate"><?= e($admin['email']) ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        <?= $admin['is_active'] ? 'Activo' : 'Inactivo' ?>
                        &middot; Ultimo acceso: <?= $admin['last_login_at'] ? format_date($admin['last_login_at']) . ' ' . format_time($admin['last_login_at']) : 'nunca' ?>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                    <form method="POST" action="/super/companies/<?= $company['id'] ?>/admins/<?= $admin['id'] ?>/password" class="flex gap-2">
                        <?= csrf_field() ?>
                        <input type="password" name="password" required minlength="8" placeholder="Nueva contrasena"
                               class="w-40 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm py-1.5">
                        <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Cambiar</button>
                    </form>
                    <form method="POST" action="/super/companies/<?= $company['id'] ?>/admins/<?= $admin['id'] ?>/toggle-status">
                        <?= csrf_field() ?>
                        <button class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium">
                            <?= $admin['is_active'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                    </form>
                    <form method="POST" action="/super/companies/<?= $company['id'] ?>/admins/<?= $admin['id'] ?>/delete"
                          data-confirm="Eliminar al administrador <?= e($admin['email']) ?>?">
                        <?= csrf_field() ?>
                        <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Eliminar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (!$admins): ?>
            <p class="py-4 text-sm text-slate-400 dark:text-slate-500">Esta empresa no tiene administradores.</p>
            <?php endif; ?>
        </div>

        <form method="POST" action="/super/companies/<?= $company['id'] ?>/admins" class="space-y-3">
            <?= csrf_field() ?>
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Agregar administrador</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <input type="email" name="admin_email" required maxlength="150" placeholder="Correo electronico" value="<?= old('admin_email') ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if ($error = field_error('admin_email')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
                </div>
                <div>
                    <input type="password" name="admin_password" required minlength="8" placeholder="Contrasena (min. 8)"
                           class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if ($error = field_error('admin_password')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
                </div>
            </div>
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Agregar administrador</button>
        </form>
    </div>

    <!-- Zona de peligro -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-red-200 dark:border-red-900 p-6">
        <h2 class="font-semibold text-red-700 dark:text-red-400 mb-1">Eliminar empresa</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Borra la empresa con todos sus empleados, registros de asistencia, administradores y configuracion. No se puede deshacer.
            Si solo quieres bloquear el acceso, cambia el estado a Inactiva.
        </p>
        <form method="POST" action="/super/companies/<?= $company['id'] ?>/delete"
              data-confirm="Eliminar &quot;<?= e($company['name']) ?>&quot; y TODOS sus datos? Esta accion no se puede deshacer.">
            <?= csrf_field() ?>
            <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Eliminar empresa</button>
        </form>
    </div>
</div>
