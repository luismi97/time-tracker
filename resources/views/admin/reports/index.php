<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 max-w-2xl">
    <form method="POST" action="/admin/reports/preview" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Empleado</label>
            <select name="employee_id" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <option value="all" <?= $formValues['employee_id'] === 'all' ? 'selected' : '' ?>>Todos los empleados</option>
                <?php foreach ($employees as $employeeOption): ?>
                <option value="<?= $employeeOption['id'] ?>" <?= (string) $formValues['employee_id'] === (string) $employeeOption['id'] ? 'selected' : '' ?>>
                    <?= e('#' . $employeeOption['employee_number'] . ' - ' . $employeeOption['full_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Periodo</label>
            <select name="period" id="report-period" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <?php foreach (['day' => 'Dia', 'week' => 'Semana', 'month' => 'Mes', 'custom' => 'Rango personalizado'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= $formValues['period'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="report-date-field">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Fecha</label>
            <input type="date" name="date" value="<?= e($formValues['date']) ?>" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        </div>
        <div id="report-month-fields" class="hidden grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Ano</label>
                <input type="number" name="year" value="<?= e((string) $formValues['year']) ?>" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mes</label>
                <input type="number" min="1" max="12" name="month" value="<?= e((string) $formValues['month']) ?>" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
        </div>
        <div id="report-range-fields" class="hidden grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Desde</label>
                <input type="date" name="from" value="<?= e($formValues['from']) ?>" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Hasta</label>
                <input type="date" name="to" value="<?= e($formValues['to']) ?>" class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" formaction="/admin/reports/preview"
                    class="w-full sm:w-auto rounded-lg bg-slate-800 px-5 py-2.5 text-white font-medium hover:bg-slate-900">
                Vista previa
            </button>
            <button type="submit" formaction="/admin/reports/generate"
                    class="w-full sm:w-auto rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
                Generar PDF
            </button>
        </div>
    </form>
</div>

<?php if (isset($previewRows)): ?>
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 mt-6 max-w-4xl">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100">Vista previa</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Periodo: <?= format_date($previewStart) ?> a <?= format_date($previewEnd) ?></p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
                    <th class="py-3 px-5">Empleado</th>
                    <th class="py-3 px-5">Horas trabajadas</th>
                    <th class="py-3 px-5">Horas extra</th>
                    <th class="py-3 px-5">Horas pagadas</th>
                    <th class="py-3 px-5">Total a pagar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($previewRows as $row): ?>
                <tr>
                    <td class="py-3 px-5 font-medium text-slate-800 dark:text-slate-100">
                        #<?= e($row['employee']['employee_number']) ?> &middot; <?= e($row['employee']['full_name']) ?>
                    </td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_hours($row['summary']['total_hours']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_hours($row['summary']['total_overtime']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_hours($row['summary']['total_paid_hours']) ?></td>
                    <td class="py-3 px-5 font-medium dark:text-slate-100"><?= format_money($row['summary']['total_pay']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$previewRows): ?>
                <tr><td colspan="5" class="py-6 text-center text-slate-400 dark:text-slate-500">Sin datos para el periodo seleccionado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script src="<?= asset('js/reports.js') ?>"></script>
