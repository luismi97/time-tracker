<?php
$success = \App\Core\Session::getFlash('success');
$error = \App\Core\Session::getFlash('error');
?>
<?php if ($success): ?>
<div class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-300" role="alert">
    <?= e($success) ?>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="mb-4 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-800 dark:text-red-300" role="alert">
    <?= e($error) ?>
</div>
<?php endif; ?>
