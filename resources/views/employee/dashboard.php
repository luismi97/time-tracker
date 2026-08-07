<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    <div class="rounded-xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-gray-100 dark:border-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Hora de entrada hoy</p>
        <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100"><?= $todayRecord ? format_time($todayRecord['clock_in']) : '-' ?></p>
    </div>
    <div class="rounded-xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-gray-100 dark:border-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Hora de salida hoy</p>
        <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100"><?= $todayRecord && $todayRecord['clock_out'] ? format_time($todayRecord['clock_out']) : '-' ?></p>
    </div>
    <div class="rounded-xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-gray-100 dark:border-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Horas trabajadas hoy</p>
        <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100"><?= format_hours($hoursToday) ?></p>
    </div>
    <div class="rounded-xl bg-white dark:bg-slate-900 p-5 shadow-sm border border-gray-100 dark:border-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Horas esta semana</p>
        <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100"><?= format_hours($hoursWeek) ?></p>
    </div>
</div>
<a href="/employee/attendance" class="inline-block rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
    Ir a registrar entrada / salida
</a>
