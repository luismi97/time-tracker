<div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl p-8">
    <div class="text-center mb-6">
        <?php if (site_logo()): ?>
            <img src="<?= e(site_logo()) ?>" alt="Logo" class="mx-auto mb-3 h-16 w-16 rounded object-cover">
        <?php endif; ?>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?= e(site_name()) ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Marcar entrada / salida</p>
    </div>

    <?php if (!isset($employee)): ?>
        <form method="POST" action="/kiosk/lookup" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="employee_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de empleado</label>
                <input type="text" id="employee_number" name="employee_number" required autofocus placeholder="Ej: 001"
                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-lg tracking-widest">
            </div>
            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-semibold hover:bg-indigo-700 transition">
                Continuar
            </button>
        </form>
        <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
            <a href="/login" class="hover:text-indigo-600 dark:hover:text-indigo-400">Iniciar sesion en su lugar</a>
        </p>
    <?php else: ?>
        <div class="text-center mb-4">
            <p class="text-sm text-slate-500 dark:text-slate-400">Empleado</p>
            <p class="text-lg font-semibold text-slate-800 dark:text-slate-100">
                #<?= e($employee['employee_number']) ?> &middot; <?= e($employee['full_name']) ?>
            </p>
        </div>

        <?php if ($justAction === 'in' && $openRecord): ?>
        <div class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-300 text-center">
            Entrada registrada a las <?= format_time($openRecord['clock_in']) ?>.
        </div>
        <?php elseif ($justAction === 'out' && $todayRecord && $todayRecord['clock_out']): ?>
        <div class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-300 text-center">
            Salida registrada.<br>
            Entrada: <?= format_time($todayRecord['clock_in']) ?> &middot; Salida: <?= format_time($todayRecord['clock_out']) ?><br>
            Horas trabajadas: <?= format_hours((float) $todayRecord['hours_worked']) ?>
        </div>
        <?php endif; ?>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400 mb-4">
            <?= $openRecord ? 'Jornada abierta desde ' . format_time($openRecord['clock_in']) : 'Sin jornada abierta' ?>
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="/kiosk/clock-in" class="w-full">
                <?= csrf_field() ?>
                <input type="hidden" name="employee_number" value="<?= e($employee['employee_number']) ?>">
                <button <?= $openRecord ? 'disabled' : '' ?>
                    class="w-full rounded-lg bg-green-600 px-5 py-2.5 text-white font-medium hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Marcar entrada
                </button>
            </form>
            <form method="POST" action="/kiosk/clock-out" class="w-full">
                <?= csrf_field() ?>
                <input type="hidden" name="employee_number" value="<?= e($employee['employee_number']) ?>">
                <button <?= !$openRecord ? 'disabled' : '' ?>
                    class="w-full rounded-lg bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Marcar salida
                </button>
            </form>
        </div>

        <div class="mt-4 text-center">
            <a href="/kiosk" class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Otro empleado</a>
        </div>
    <?php endif; ?>
</div>
