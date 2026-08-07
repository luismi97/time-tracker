<div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl p-8">
    <div class="text-center mb-6">
        <?php if (site_logo()): ?>
            <img src="<?= e(site_logo()) ?>" alt="Logo" class="mx-auto mb-3 h-16 w-16 rounded object-cover">
        <?php endif; ?>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?= e(site_name()) ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Control de horas y nomina de empleados</p>
    </div>
    <form method="POST" action="/login" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo electronico</label>
            <input type="email" id="email" name="email" required autofocus value="<?= old('email') ?>"
                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Contrasena</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-semibold hover:bg-indigo-700 transition">
            Iniciar sesion
        </button>
    </form>
    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
        Admin demo: admin@timetracking.test / Admin123!<br>
        Empleado demo: empleado@timetracking.test / Employee123!
    </p>
    <?php if (\App\Models\Settings::get()['attendance_mode'] === 'kiosk'): ?>
    <div class="mt-4 text-center">
        <a href="/kiosk" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
            &rarr; Marcar entrada / salida con codigo de empleado
        </a>
    </div>
    <?php endif; ?>
</div>
