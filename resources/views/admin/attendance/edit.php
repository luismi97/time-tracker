<div class="space-y-6 max-w-3xl">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <p class="text-sm text-slate-500 dark:text-slate-400">Empleado</p>
        <p class="font-semibold text-slate-800 dark:text-slate-100 mb-4">
            #<?= e($record['employee_number']) ?> &middot; <?= e($record['full_name']) ?>
        </p>
        <?php
        $formAction = '/admin/attendance/' . $record['id'];
        $backUrl = '/admin/attendance';
        require VIEWS_PATH . '/attendance/_edit-form.php';
        ?>
    </div>
    <?php require VIEWS_PATH . '/attendance/_history.php'; ?>
</div>
