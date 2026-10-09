<div class="space-y-6 max-w-3xl">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Corrige la hora de entrada o salida si te equivocaste u olvidaste marcar. Tu administrador
            podra ver el cambio, los valores anteriores y tu justificacion.
        </p>
        <?php
        $formAction = '/employee/attendance/' . $record['id'];
        $backUrl = '/employee/attendance';
        require VIEWS_PATH . '/attendance/_edit-form.php';
        ?>
    </div>
    <?php require VIEWS_PATH . '/attendance/_history.php'; ?>
</div>
