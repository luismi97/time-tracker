<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-5 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-center sm:text-left">
        <p class="text-sm text-slate-500 dark:text-slate-400">Estado actual</p>
        <p class="font-semibold text-slate-800 dark:text-slate-100">
            <?= $openRecord ? 'Jornada abierta desde ' . format_time($openRecord['clock_in']) : 'Sin jornada abierta' ?>
        </p>
    </div>
    <?php if ($kioskMode): ?>
    <div class="w-full sm:w-auto text-center sm:text-right">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            El registro de horas se realiza en el kiosco de asistencia.
        </p>
        <a href="/kiosk" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
            Ir al kiosco &rarr;
        </a>
    </div>
    <?php else: ?>
    <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
        <form method="POST" action="/employee/attendance/clock-in" class="w-full sm:w-auto">
            <?= csrf_field() ?>
            <button <?= $openRecord ? 'disabled' : '' ?>
                class="w-full sm:w-auto rounded-lg bg-green-600 px-5 py-2.5 text-white font-medium hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed">
                Registrar entrada
            </button>
        </form>
        <form method="POST" action="/employee/attendance/clock-out" class="w-full sm:w-auto">
            <?= csrf_field() ?>
            <button <?= !$openRecord ? 'disabled' : '' ?>
                class="w-full sm:w-auto rounded-lg bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed">
                Registrar salida
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
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
                <tr><td colspan="6" class="py-6 text-center text-slate-400 dark:text-slate-500">Aun no tienes registros.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
        <?php foreach ($records as $record): ?>
        <div class="p-4">
            <div class="flex items-start justify-between gap-2">
                <p class="font-medium text-slate-800 dark:text-slate-100"><?= format_date($record['work_date']) ?></p>
                <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $record['status'] === 'closed' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' ?>">
                    <?= $record['status'] === 'closed' ? 'Cerrado' : 'Abierto' ?>
                </span>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Entrada</dt><dd class="dark:text-slate-300"><?= format_time($record['clock_in']) ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Salida</dt><dd class="dark:text-slate-300"><?= format_time($record['clock_out']) ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Horas</dt><dd class="dark:text-slate-300"><?= $record['hours_worked'] !== null ? format_hours((float) $record['hours_worked']) : '-' ?></dd></div>
                <div><dt class="text-xs text-slate-400 dark:text-slate-500">Extra</dt><dd class="dark:text-slate-300"><?= $record['overtime_hours'] !== null ? format_hours((float) $record['overtime_hours']) : '-' ?></dd></div>
            </dl>
        </div>
        <?php endforeach; ?>
        <?php if (!$records): ?>
        <p class="py-6 text-center text-slate-400 dark:text-slate-500">Aun no tienes registros.</p>
        <?php endif; ?>
    </div>

    <?php require VIEWS_PATH . '/partials/pagination.php'; ?>
</div>
