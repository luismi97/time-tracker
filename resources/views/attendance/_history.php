<?php
/** Historial de modificaciones de una marca. Requiere $edits. */
$roleLabels = ['employee' => 'Empleado', 'admin' => 'Administrador', 'super_admin' => 'Super admin'];
$formatMark = fn (?string $datetime) => $datetime ? format_date($datetime) . ' ' . format_time($datetime) : 'abierta';
?>
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-6">
    <h2 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">Historial de modificaciones</h2>
    <?php if (!$edits): ?>
        <p class="text-sm text-slate-400 dark:text-slate-500">Esta marca no ha sido modificada.</p>
    <?php else: ?>
    <ol class="space-y-4">
        <?php foreach ($edits as $editRow): ?>
        <li class="rounded-lg border border-gray-200 dark:border-slate-700 p-4 text-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <p class="font-medium text-slate-800 dark:text-slate-100 break-all">
                    <?= e($editRow['editor_email']) ?>
                    <span class="font-normal text-slate-500 dark:text-slate-400">(<?= e($roleLabels[$editRow['editor_role']] ?? $editRow['editor_role']) ?>)</span>
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400"><?= $formatMark($editRow['created_at']) ?></p>
            </div>
            <dl class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-600 dark:text-slate-300">
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Entrada</dt>
                    <dd><?= $formatMark($editRow['old_clock_in']) ?> &rarr; <strong><?= $formatMark($editRow['new_clock_in']) ?></strong></dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 dark:text-slate-500">Salida</dt>
                    <dd><?= $formatMark($editRow['old_clock_out']) ?> &rarr; <strong><?= $formatMark($editRow['new_clock_out']) ?></strong></dd>
                </div>
            </dl>
            <p class="mt-2 text-slate-700 dark:text-slate-200"><span class="text-xs text-slate-400 dark:text-slate-500">Justificacion:</span> <?= nl2br(e($editRow['reason'])) ?></p>
            <?php if ($editRow['ip_address']): ?>
            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">IP: <?= e($editRow['ip_address']) ?></p>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>
</div>
