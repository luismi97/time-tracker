<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div class="min-w-0 col-span-1 sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Empleado</label>
                <select name="employee_id" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                    <option value="">Todos</option>
                    <?php foreach ($employees as $employeeOption): ?>
                    <option value="<?= $employeeOption['id'] ?>" <?= (string) ($filters['employee_id'] ?? '') === (string) $employeeOption['id'] ? 'selected' : '' ?>>
                        <?= e('#' . $employeeOption['employee_number'] . ' - ' . $employeeOption['full_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="min-w-0">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Periodo</label>
                <select name="period" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                    <option value="">Todo</option>
                    <?php foreach (['day' => 'Dia', 'week' => 'Semana', 'month' => 'Mes', 'year' => 'Ano', 'custom' => 'Rango'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($filters['period'] ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="min-w-0">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Fecha (dia/semana)</label>
                <input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
            </div>
            <div class="min-w-0">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Ano</label>
                <input type="number" name="year" value="<?= e($filters['year'] ?? '') ?>" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
            </div>
            <div class="min-w-0">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Mes</label>
                <input type="number" min="1" max="12" name="month" value="<?= e($filters['month'] ?? '') ?>" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
            </div>
            <div class="min-w-0 col-span-1 sm:col-span-2 lg:col-span-1 flex gap-2">
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Desde</label>
                    <input type="date" name="from" value="<?= e($filters['from'] ?? '') ?>" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                </div>
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Hasta</label>
                    <input type="date" name="to" value="<?= e($filters['to'] ?? '') ?>" class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                </div>
            </div>
            <div class="col-span-2 sm:col-span-6">
                <button class="rounded-lg bg-slate-800 text-white px-4 py-2 text-sm">Filtrar</button>
            </div>
        </form>
    </div>
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
                    <th class="py-3 px-5">Empleado</th>
                    <th class="py-3 px-5">Fecha</th>
                    <th class="py-3 px-5">Entrada</th>
                    <th class="py-3 px-5">Salida</th>
                    <th class="py-3 px-5">Horas</th>
                    <th class="py-3 px-5">Extra</th>
                    <th class="py-3 px-5">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($records as $record): ?>
                <tr>
                    <td class="py-3 px-5 font-medium text-slate-800 dark:text-slate-100"><?= e($record['full_name']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_date($record['work_date']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_time($record['clock_in']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_time($record['clock_out']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= $record['hours_worked'] !== null ? format_hours((float) $record['hours_worked']) : '-' ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= $record['overtime_hours'] !== null ? format_hours((float) $record['overtime_hours']) : '-' ?></td>
                    <td class="py-3 px-5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $record['status'] === 'closed' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' ?>">
                            <?= $record['status'] === 'closed' ? 'Cerrado' : 'Abierto' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$records): ?>
                <tr><td colspan="7" class="py-6 text-center text-slate-400 dark:text-slate-500">No hay registros con los filtros seleccionados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
        <?php foreach ($records as $record): ?>
        <div class="p-4">
            <div class="flex items-start justify-between gap-2">
                <p class="font-medium text-slate-800 dark:text-slate-100 truncate"><?= e($record['full_name']) ?></p>
                <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $record['status'] === 'closed' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' ?>">
                    <?= $record['status'] === 'closed' ? 'Cerrado' : 'Abierto' ?>
                </span>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Fecha</dt><dd class="dark:text-slate-300"><?= format_date($record['work_date']) ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Horas</dt><dd class="dark:text-slate-300"><?= $record['hours_worked'] !== null ? format_hours((float) $record['hours_worked']) : '-' ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Entrada</dt><dd class="dark:text-slate-300"><?= format_time($record['clock_in']) ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Salida</dt><dd class="dark:text-slate-300"><?= format_time($record['clock_out']) ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Extra</dt><dd class="dark:text-slate-300"><?= $record['overtime_hours'] !== null ? format_hours((float) $record['overtime_hours']) : '-' ?></dd></div>
            </dl>
        </div>
        <?php endforeach; ?>
        <?php if (!$records): ?>
        <p class="py-6 text-center text-slate-400 dark:text-slate-500">No hay registros con los filtros seleccionados.</p>
        <?php endif; ?>
    </div>

    <?php require VIEWS_PATH . '/partials/pagination.php'; ?>
</div>
