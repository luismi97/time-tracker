<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-2">
            <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Buscar por nombre o correo"
                   class="w-full sm:w-auto rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="status" class="w-full sm:w-auto rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-sm">
                <option value="">Todos los estados</option>
                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <button class="w-full sm:w-auto rounded-lg bg-slate-800 text-white px-4 py-2 text-sm">Filtrar</button>
        </form>
        <a href="/admin/employees/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 text-center">
            + Nuevo empleado
        </a>
    </div>

    <!-- Tabla: solo visible en pantallas medianas y grandes -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60">
                    <th class="py-3 px-5">No.</th>
                    <th class="py-3 px-5">Nombre</th>
                    <th class="py-3 px-5">Correo</th>
                    <th class="py-3 px-5">Telefono</th>
                    <th class="py-3 px-5">Salario/h</th>
                    <th class="py-3 px-5">Ingreso</th>
                    <th class="py-3 px-5">Estado</th>
                    <th class="py-3 px-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($employees as $employeeRow): ?>
                <tr>
                    <td class="py-3 px-5 font-mono text-slate-500 dark:text-slate-400">#<?= e($employeeRow['employee_number']) ?></td>
                    <td class="py-3 px-5 font-medium text-slate-800 dark:text-slate-100"><?= e($employeeRow['full_name']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= e($employeeRow['email']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= e($employeeRow['phone'] ?? '-') ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_money((float) $employeeRow['hourly_rate']) ?></td>
                    <td class="py-3 px-5 dark:text-slate-300"><?= format_date($employeeRow['hire_date']) ?></td>
                    <td class="py-3 px-5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $employeeRow['status'] === 'active' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400' ?>">
                            <?= $employeeRow['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="py-3 px-5">
                        <div class="flex justify-end gap-2">
                            <a href="/admin/employees/<?= $employeeRow['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Editar</a>
                            <form method="POST" action="/admin/employees/<?= $employeeRow['id'] ?>/toggle-status">
                                <?= csrf_field() ?>
                                <button class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium">
                                    <?= $employeeRow['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                </button>
                            </form>
                            <form method="POST" action="/admin/employees/<?= $employeeRow['id'] ?>/delete"
                                  data-confirm="Eliminar a <?= e($employeeRow['full_name']) ?>? Esta accion no se puede deshacer.">
                                <?= csrf_field() ?>
                                <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$employees): ?>
                <tr><td colspan="8" class="py-6 text-center text-slate-400 dark:text-slate-500">No se encontraron empleados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tarjetas: solo visibles en pantallas pequenas (moviles) -->
    <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-800">
        <?php foreach ($employees as $employeeRow): ?>
        <div class="p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="font-mono text-xs text-slate-500 dark:text-slate-400">#<?= e($employeeRow['employee_number']) ?></p>
                    <p class="font-medium text-slate-800 dark:text-slate-100 truncate"><?= e($employeeRow['full_name']) ?></p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 truncate"><?= e($employeeRow['email']) ?></p>
                </div>
                <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $employeeRow['status'] === 'active' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400' ?>">
                    <?= $employeeRow['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Telefono</dt>
                    <dd class="dark:text-slate-300"><?= e($employeeRow['phone'] ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Salario/h</dt>
                    <dd class="dark:text-slate-300"><?= format_money((float) $employeeRow['hourly_rate']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Ingreso</dt>
                    <dd class="dark:text-slate-300"><?= format_date($employeeRow['hire_date']) ?></dd>
                </div>
            </dl>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <a href="/admin/employees/<?= $employeeRow['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Editar</a>
                <form method="POST" action="/admin/employees/<?= $employeeRow['id'] ?>/toggle-status">
                    <?= csrf_field() ?>
                    <button class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium">
                        <?= $employeeRow['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                    </button>
                </form>
                <form method="POST" action="/admin/employees/<?= $employeeRow['id'] ?>/delete"
                      data-confirm="Eliminar a <?= e($employeeRow['full_name']) ?>? Esta accion no se puede deshacer.">
                    <?= csrf_field() ?>
                    <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Eliminar</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$employees): ?>
        <p class="py-6 text-center text-slate-400 dark:text-slate-500">No se encontraron empleados.</p>
        <?php endif; ?>
    </div>

    <?php require VIEWS_PATH . '/partials/pagination.php'; ?>
</div>
