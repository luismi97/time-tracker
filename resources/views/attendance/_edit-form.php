<?php
/** Formulario de modificacion de marca. Requiere $record y $formAction. */
$toInput = fn (?string $datetime) => $datetime ? (new DateTimeImmutable($datetime))->format('Y-m-d\TH:i') : '';
$isOpen = $record['status'] === 'open';
?>
<form method="POST" action="<?= e($formAction) ?>" class="space-y-4">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="clock_in" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Entrada</label>
            <input type="datetime-local" id="clock_in" name="clock_in" required value="<?= old('clock_in', $toInput($record['clock_in'])) ?>"
                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            <?php if ($error = field_error('clock_in')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="clock_out" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Salida</label>
            <input type="datetime-local" id="clock_out" name="clock_out" <?= $isOpen ? '' : 'required' ?> value="<?= old('clock_out', $toInput($record['clock_out'])) ?>"
                   class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            <?php if ($isOpen): ?>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Jornada abierta: deja la salida vacia para mantenerla abierta.</p>
            <?php endif; ?>
            <?php if ($error = field_error('clock_out')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
        </div>
    </div>
    <div>
        <label for="reason" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Justificacion del cambio (obligatoria)</label>
        <textarea id="reason" name="reason" rows="3" required minlength="10" maxlength="500"
                  placeholder="Ej: Olvide marcar la salida al terminar el turno."
                  class="mt-1 w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500"><?= old('reason') ?></textarea>
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Queda registrada junto con tu usuario, la fecha y los valores anteriores.</p>
        <?php if ($error = field_error('reason')): ?><p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= e($error) ?></p><?php endif; ?>
    </div>
    <div class="flex justify-end gap-3">
        <a href="<?= e($backUrl) ?>" class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Volver</a>
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Guardar cambio</button>
    </div>
</form>
