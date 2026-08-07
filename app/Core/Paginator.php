<?php

namespace App\Core;

class Paginator
{
    public function __construct(
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
    ) {
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }

    public static function currentPageFromRequest(): int
    {
        $page = (int) ($_GET['page'] ?? 1);
        return $page < 1 ? 1 : $page;
    }
}
