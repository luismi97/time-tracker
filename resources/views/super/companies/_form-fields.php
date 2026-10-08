<?php $companyData = $company ?? []; ?>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre de la empresa</label>
        <input type="text" name="name" required maxlength="100" value="<?= old('name', $companyData['name'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('name')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de empresa</label>
        <input type="text" name="slug" maxlength="50" placeholder="Se genera del nombre" value="<?= old('slug', $companyData['slug'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Solo letras, numeros y guiones. Se usa en la URL del kiosco: /kiosk/codigo</p>
        <?php if ($error = field_error('slug')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Estado</label>
        <?php $statusValue = old('status', $companyData['status'] ?? 'active'); ?>
        <select name="status" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            <option value="active" <?= $statusValue === 'active' ? 'selected' : '' ?>>Activa</option>
            <option value="inactive" <?= $statusValue === 'inactive' ? 'selected' : '' ?>>Inactiva</option>
        </select>
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Si esta inactiva, sus usuarios no pueden iniciar sesion ni usar el kiosco.</p>
    </div>
</div>
