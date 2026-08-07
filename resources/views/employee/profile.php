<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 max-w-xl">
    <dl class="divide-y divide-gray-100 dark:divide-slate-800">
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Numero de empleado</dt><dd class="font-medium text-slate-800 dark:text-slate-100">#<?= e($employee['employee_number']) ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Nombre completo</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= e($employee['full_name']) ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Correo</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= e($employee['email']) ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Telefono</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= e($employee['phone'] ?? '-') ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Direccion</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= e($employee['address'] ?? '-') ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Documento</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= e($employee['document_id'] ?? '-') ?></dd></div>
        <div class="py-3 flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Fecha de ingreso</dt><dd class="font-medium text-slate-800 dark:text-slate-100"><?= format_date($employee['hire_date']) ?></dd></div>
    </dl>
    <p class="mt-4 text-xs text-slate-400 dark:text-slate-500">La informacion salarial es visible unicamente para el administrador.</p>
</div>
