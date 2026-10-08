<div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl p-8">
    <div class="text-center mb-6">
        <?php if (site_logo()): ?>
            <img src="<?= e(site_logo()) ?>" alt="Logo" class="mx-auto mb-3 h-16 w-16 rounded object-cover">
        <?php endif; ?>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?= e(site_name()) ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Marcar entrada / salida</p>
    </div>

    <?php if (!isset($employee)): ?>
        <form method="POST" action="<?= e($kioskUrl) ?>/lookup" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="employee_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de empleado</label>
                <input type="text" id="employee_number" name="employee_number" required autofocus placeholder="Ej: 001"
                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-lg tracking-widest">
            </div>
            <div>
                <label for="document_pin" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Ultimos 5 digitos de tu cedula</label>
                <input type="password" id="document_pin" name="document_pin" required inputmode="numeric" pattern="[0-9]{5}" maxlength="5"
                       autocomplete="off" placeholder="&bull;&bull;&bull;&bull;&bull;" title="Escribe los ultimos 5 digitos de tu cedula"
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

        <p class="text-center text-sm text-slate-500 dark:text-slate-400 mb-4">
            <?= $openRecord ? 'Jornada abierta desde ' . format_time($openRecord['clock_in']) : 'Sin jornada abierta' ?>
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="<?= e($kioskUrl) ?>/clock-in" class="w-full">
                <?= csrf_field() ?>
                <button <?= $openRecord ? 'disabled' : '' ?>
                    class="w-full rounded-lg bg-green-600 px-5 py-2.5 text-white font-medium hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Marcar entrada
                </button>
            </form>
            <form method="POST" action="<?= e($kioskUrl) ?>/clock-out" class="w-full">
                <?= csrf_field() ?>
                <button <?= !$openRecord ? 'disabled' : '' ?>
                    class="w-full rounded-lg bg-red-600 px-5 py-2.5 text-white font-medium hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Marcar salida
                </button>
            </form>
        </div>

        <div class="mt-4 text-center">
            <a href="<?= e($kioskUrl) ?>" class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">&larr; Cancelar</a>
        </div>
        <!-- Si nadie marca, vuelve a la pantalla principal cuando vence la verificacion. -->
        <script>setTimeout(() => { window.location.href = <?= json_encode($kioskUrl) ?>; }, <?= (int) $returnAfterSeconds * 1000 ?>);</script>
    <?php endif; ?>
</div>
