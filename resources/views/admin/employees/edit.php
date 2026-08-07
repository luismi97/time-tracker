<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6 max-w-2xl">
    <form method="POST" action="/admin/employees/<?= $employee['id'] ?>" class="space-y-4">
        <?= csrf_field() ?>
        <?php require VIEWS_PATH . '/admin/employees/_form-fields.php'; ?>
        <div class="flex justify-end gap-3 pt-2">
            <a href="/admin/employees" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Cancelar</a>
            <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Actualizar</button>
        </div>
    </form>
</div>
