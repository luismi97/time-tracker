<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-8">
    <?php
    $cards = [
        ['label' => 'Total empleados', 'value' => $stats['total_employees']],
        ['label' => 'Empleados activos', 'value' => $stats['active_employees']],
        ['label' => 'Horas hoy', 'value' => format_hours($stats['hours_today'])],
        ['label' => 'Horas esta semana', 'value' => format_hours($stats['hours_week'])],
        ['label' => 'Horas este mes', 'value' => format_hours($stats['hours_month'])],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <div class="rounded-xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-gray-100 dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400"><?= e($card['label']) ?></p>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100"><?= e((string) $card['value']) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">Ultimos registros</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800">
                        <th class="py-2 pr-4">Empleado</th>
                        <th class="py-2 pr-4">Fecha</th>
                        <th class="py-2 pr-4">Entrada</th>
                        <th class="py-2">Salida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 dark:text-slate-200">
                    <?php foreach ($recentRecords as $record): ?>
                    <tr>
                        <td class="py-2 pr-4"><?= e($record['full_name']) ?></td>
                        <td class="py-2 pr-4"><?= format_date($record['work_date']) ?></td>
                        <td class="py-2 pr-4"><?= format_time($record['clock_in']) ?></td>
                        <td class="py-2"><?= format_time($record['clock_out']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentRecords): ?>
                    <tr><td colspan="4" class="py-4 text-center text-slate-400 dark:text-slate-500">Sin registros aun.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">Empleados con mas horas (mes actual)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800">
                        <th class="py-2 pr-4">Empleado</th>
                        <th class="py-2">Horas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 dark:text-slate-200">
                    <?php foreach ($topEmployees as $topEmployee): ?>
                    <tr>
                        <td class="py-2 pr-4"><?= e($topEmployee['full_name']) ?></td>
                        <td class="py-2"><?= format_hours((float) $topEmployee['total_hours']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$topEmployees): ?>
                    <tr><td colspan="2" class="py-4 text-center text-slate-400 dark:text-slate-500">Sin datos aun.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 mt-6">
    <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">Accesos recientes</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800">
                    <th class="py-2 pr-4">Usuario</th>
                    <th class="py-2 pr-4">Rol</th>
                    <th class="py-2">Ultimo acceso</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800 dark:text-slate-200">
                <?php foreach ($recentLogins as $login): ?>
                <tr>
                    <td class="py-2 pr-4"><?= e($login['full_name'] ?? $login['email']) ?></td>
                    <td class="py-2 pr-4 capitalize"><?= e($login['role_name']) ?></td>
                    <td class="py-2"><?= e((new DateTimeImmutable($login['last_login_at']))->format('d/m/Y h:i A')) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$recentLogins): ?>
                <tr><td colspan="3" class="py-4 text-center text-slate-400 dark:text-slate-500">Sin accesos registrados aun.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
