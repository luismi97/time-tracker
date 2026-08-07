<?php
$dayLabels = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 0 => 'Domingo'];
$hoursByDay = [];
foreach ($businessHours as $row) {
    $hoursByDay[(int) $row['day_of_week']] = $row;
}
$is24h = !empty($settings['is_24_7']);
$sameEveryDay = !empty($settings['same_hours_every_day']);
?>
<div class="space-y-6 max-w-3xl">
    <!-- General: nombre del sitio y logo -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">General</h2>
        <form method="POST" action="/admin/settings/general" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre del sitio</label>
                <input type="text" name="app_name" required maxlength="100" value="<?= e($settings['app_name']) ?>"
                       class="mt-1 w-full max-w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Logo</label>
                <div class="mt-1 flex items-center gap-4">
                    <?php if ($settings['logo_path']): ?>
                        <img src="<?= e($settings['logo_path']) ?>" alt="Logo actual" class="h-14 w-14 rounded object-cover border border-gray-200 dark:border-slate-700">
                    <?php else: ?>
                        <div class="h-14 w-14 rounded border border-dashed border-gray-300 dark:border-slate-600 flex items-center justify-center text-xs text-slate-400">Sin logo</div>
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           class="text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 dark:file:bg-slate-800 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 dark:file:text-slate-200">
                </div>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">PNG, JPG, WEBP o SVG. Maximo 2MB.</p>
            </div>
            <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">Guardar general</button>
        </form>
    </div>

    <!-- Registro de horas: login vs kiosco -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-1">Registro de horas</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Elige como marcan los empleados su entrada y salida.
        </p>
        <form method="POST" action="/admin/settings/attendance-mode" class="space-y-3">
            <?= csrf_field() ?>
            <label class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-slate-700 p-3 cursor-pointer">
                <input type="radio" name="attendance_mode" value="login" <?= $settings['attendance_mode'] === 'login' ? 'checked' : '' ?>
                       class="mt-0.5 border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <span class="block font-medium text-slate-800 dark:text-slate-100">Con inicio de sesion</span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400">Cada empleado inicia sesion con su correo y contrasena para marcar su horario.</span>
                </span>
            </label>
            <label class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-slate-700 p-3 cursor-pointer">
                <input type="radio" name="attendance_mode" value="kiosk" <?= $settings['attendance_mode'] === 'kiosk' ? 'checked' : '' ?>
                       class="mt-0.5 border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <span class="block font-medium text-slate-800 dark:text-slate-100">Con codigo de empleado (kiosco)</span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400">
                        Los empleados marcan su entrada/salida escribiendo su numero de empleado en
                        <a href="/kiosk" target="_blank" class="underline">/kiosk</a>, sin iniciar sesion.
                    </span>
                </span>
            </label>
            <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">Guardar modo</button>
        </form>
    </div>

    <!-- Horario del negocio -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-1">Horario de atencion</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Informativo por ahora: no bloquea el registro de horas, solo se usa para mostrar el horario del negocio.
        </p>
        <form method="POST" action="/admin/settings/business-hours" class="space-y-4" id="business-hours-form">
            <?= csrf_field() ?>
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input type="checkbox" id="is-24h" name="is_24_7_hours" value="1" <?= $is24h ? 'checked' : '' ?>
                       class="rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                Abierto las 24 horas
            </label>

            <div id="same-day-field" class="<?= $is24h ? 'hidden' : '' ?>">
                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" id="is-7-days" name="same_hours_every_day" value="1" <?= $sameEveryDay ? 'checked' : '' ?>
                           class="rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    Abierto los 7 dias, mismo horario
                </label>
            </div>

            <div id="single-hours-fields" class="grid grid-cols-2 gap-3 max-w-sm <?= ($is24h || !$sameEveryDay) ? 'hidden' : '' ?>">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Hora de apertura</label>
                    <input type="time" name="open_time" value="<?= e(substr($settings['open_time'], 0, 5)) ?>"
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Hora de cierre</label>
                    <input type="time" name="close_time" value="<?= e(substr($settings['close_time'], 0, 5)) ?>"
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div id="manual-days-fields" class="space-y-2 <?= ($is24h || $sameEveryDay) ? 'hidden' : '' ?>">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Horario manual por dia</p>
                <?php foreach ([1, 2, 3, 4, 5, 6, 0] as $day): ?>
                    <?php $dayData = $hoursByDay[$day] ?? ['is_open' => 0, 'open_time' => null, 'close_time' => null]; ?>
                    <div class="flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 dark:border-slate-700 p-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 w-28 shrink-0">
                            <input type="checkbox" name="day_open[<?= $day ?>]" value="1" <?= $dayData['is_open'] ? 'checked' : '' ?>
                                   class="rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                            <?= e($dayLabels[$day]) ?>
                        </label>
                        <input type="time" name="day_open_time[<?= $day ?>]" value="<?= e(substr($dayData['open_time'] ?? '08:00:00', 0, 5)) ?>"
                               class="min-w-0 flex-1 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                        <span class="text-slate-400 text-sm">a</span>
                        <input type="time" name="day_close_time[<?= $day ?>]" value="<?= e(substr($dayData['close_time'] ?? '17:00:00', 0, 5)) ?>"
                               class="min-w-0 flex-1 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">Guardar horario</button>
        </form>
    </div>
</div>
<script src="<?= asset('js/settings.js') ?>"></script>
