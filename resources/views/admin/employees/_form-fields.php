<?php $employeeData = $employee ?? []; ?>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <?php if (isset($employeeData['employee_number'])): ?>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Numero de empleado</label>
        <input type="text" value="#<?= e($employeeData['employee_number']) ?>" disabled
               class="mt-1 w-full rounded-lg border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
    </div>
    <?php endif; ?>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre completo</label>
        <input type="text" name="full_name" required maxlength="150" value="<?= old('full_name', $employeeData['full_name'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('full_name')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo electronico</label>
        <input type="email" name="email" required maxlength="150" value="<?= old('email', $employeeData['email'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('email')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Telefono</label>
        <input type="text" name="phone" value="<?= old('phone', $employeeData['phone'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Documento de identificacion</label>
        <input type="text" name="document_id" value="<?= old('document_id', $employeeData['document_id'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Direccion</label>
        <input type="text" name="address" value="<?= old('address', $employeeData['address'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Fecha de ingreso</label>
        <input type="date" name="hire_date" required value="<?= old('hire_date', $employeeData['hire_date'] ?? '') ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('hire_date')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Salario por hora ($)</label>
        <input type="number" step="0.01" min="0" name="hourly_rate" required value="<?= old('hourly_rate', (string) ($employeeData['hourly_rate'] ?? '')) ?>"
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('hourly_rate')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php
        $overtimeChecked = old_input() ? old_checked('overtime_paid') : !empty($employeeData['overtime_paid'] ?? false);
        $lunchChecked = old_input() ? old_checked('has_lunch_break') : !empty($employeeData['has_lunch_break'] ?? false);
        ?>
        <label class="flex items-start gap-2 rounded-lg border border-gray-200 dark:border-slate-700 p-3 text-sm text-slate-700 dark:text-slate-300">
            <input type="checkbox" name="overtime_paid" value="1" <?= $overtimeChecked ? 'checked' : '' ?>
                   class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block font-medium text-slate-800 dark:text-slate-100">Paga horas extra (1.5x)</span>
                <span class="block text-xs text-slate-500 dark:text-slate-400">Si no se marca, las horas extra se pagan igual que las normales.</span>
            </span>
        </label>
        <label class="flex items-start gap-2 rounded-lg border border-gray-200 dark:border-slate-700 p-3 text-sm text-slate-700 dark:text-slate-300">
            <input type="checkbox" name="has_lunch_break" value="1" <?= $lunchChecked ? 'checked' : '' ?>
                   class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block font-medium text-slate-800 dark:text-slate-100">Tiene hora de almuerzo</span>
                <span class="block text-xs text-slate-500 dark:text-slate-400">Se descuenta 1 hora del pago en cada jornada trabajada.</span>
            </span>
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Estado</label>
        <?php $status = old('status', $employeeData['status'] ?? 'active'); ?>
        <select name="status" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Activo</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            Contrasena <?= isset($employeeData['id']) ? '(dejar en blanco para no cambiar)' : '' ?>
        </label>
        <input type="password" name="password" minlength="8" <?= isset($employeeData['id']) ? '' : 'required' ?>
               class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        <?php if ($error = field_error('password')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
</div>
