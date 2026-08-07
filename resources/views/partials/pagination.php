<?php
/** @var \App\Core\Paginator $paginator */
$lastPage = $paginator->lastPage();
$current = $paginator->currentPage;
?>
<?php if ($lastPage > 1): ?>
<nav class="flex items-center justify-between border-t border-gray-200 dark:border-slate-700 px-4 py-3 sm:px-6" aria-label="Paginacion">
    <div class="flex flex-1 flex-wrap justify-center sm:justify-end gap-2">
        <a href="<?= e(pagination_url($_GET, max(1, $current - 1))) ?>"
           class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 <?= $current <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
            Anterior
        </a>
        <span class="inline-flex items-center px-2 sm:px-4 py-2 text-sm text-gray-600 dark:text-slate-400 whitespace-nowrap">Pagina <?= $current ?> de <?= $lastPage ?></span>
        <a href="<?= e(pagination_url($_GET, min($lastPage, $current + 1))) ?>"
           class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 <?= $current >= $lastPage ? 'pointer-events-none opacity-50' : '' ?>">
            Siguiente
        </a>
    </div>
</nav>
<?php endif; ?>
