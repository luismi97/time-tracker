<?php

namespace App\Support;

use DateTimeImmutable;

/** Resuelve un periodo (dia/semana/mes/anio/rango) a una fecha de inicio y fin. */
class DateRange
{
    /** @return array{0: DateTimeImmutable, 1: DateTimeImmutable} */
    public static function resolve(string $period, array $params): array
    {
        $today = new DateTimeImmutable('today');

        return match ($period) {
            'day' => self::day($params, $today),
            'week' => self::week($params, $today),
            'month' => self::month($params, $today),
            'year' => self::year($params, $today),
            'custom' => self::custom($params, $today),
            default => [$today, $today],
        };
    }

    private static function day(array $params, DateTimeImmutable $today): array
    {
        $date = self::parseDate($params['date'] ?? null) ?? $today;
        return [$date, $date];
    }

    private static function week(array $params, DateTimeImmutable $today): array
    {
        $date = self::parseDate($params['date'] ?? null) ?? $today;
        $start = $date->modify('monday this week');
        $end = $start->modify('+6 days');
        return [$start, $end];
    }

    private static function month(array $params, DateTimeImmutable $today): array
    {
        $year = (int) ($params['year'] ?? $today->format('Y'));
        $month = (int) ($params['month'] ?? $today->format('n'));
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        $end = $start->modify('last day of this month');
        return [$start, $end];
    }

    private static function year(array $params, DateTimeImmutable $today): array
    {
        $year = (int) ($params['year'] ?? $today->format('Y'));
        return [new DateTimeImmutable("$year-01-01"), new DateTimeImmutable("$year-12-31")];
    }

    private static function custom(array $params, DateTimeImmutable $today): array
    {
        $start = self::parseDate($params['from'] ?? null) ?? $today;
        $end = self::parseDate($params['to'] ?? null) ?? $today;

        if ($end < $start) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }

    private static function parseDate(?string $value): ?DateTimeImmutable
    {
        if (!$value) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date ?: null;
    }
}
