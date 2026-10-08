<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 max-w-2xl">
    <form method="POST" action="/super/companies" class="space-y-6">
        <?= csrf_field() ?>
        <?php require VIEWS_PATH . '/super/companies/_form-fields.php'; ?>

        <div class="border-t border-gray-100 dark:border-slate-800 pt-5">
            <h2 class="font-semibold text-slate-800 dark:text-slate-100">Administrador de la empresa</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Con esta cuenta el cliente gestiona sus empleados, reportes y configuracion.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo electronico</label>
                    <input type="email" name="admin_email" required maxlength="150" value="<?= old('admin_email') ?>"
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if ($error = field_error('admin_email')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Contrasena</label>
                    <input type="password" name="admin_password" required minlength="8"
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
                    <?php if ($error = field_error('admin_password')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/super/companies" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Cancelar</a>
            <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Crear empresa</button>
        </div>
    </form>
</div>
