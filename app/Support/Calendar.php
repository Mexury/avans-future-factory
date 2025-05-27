<?php

namespace App\Support;

use Carbon\CarbonImmutable;

class Calendar
{
    public static function buildMonth($year, $month): array
    {
        $startOfMonth = CarbonImmutable::create($year, $month, 1);
        $endOfMonth = $startOfMonth->endOfMonth();

        $startOfWeek = $startOfMonth->startOfWeek();
        $endOfWeek = $endOfMonth->endOfWeek();

        $days = collect($startOfWeek->toPeriod($endOfWeek)->toArray());
        $weekdays = $days->filter(fn(CarbonImmutable $date) => $date->isWeekday());

        $weeks = $weekdays->map(fn(CarbonImmutable $date) => [
            'path' => route('calendar.show', [
                'year' => $date->year,
                'month' => str_pad($date->month, 2, '0', STR_PAD_LEFT),
                'day' => str_pad($date->day, 2, '0', STR_PAD_LEFT)
            ]),
            'year' => $date->year,
            'month' => $date->month,
            'day' => $date->day
        ])->chunk(5);

        $prevMonth = $startOfMonth->subMonth();
        $nextMonth = $startOfMonth->addMonth();

        return [
            'year' => $startOfMonth->year,
            'month' => $startOfMonth->month,
            'weeks' => $weeks,
            'monthName' => $startOfMonth->format('F'),
            'prev' => route('calendar.index', [
                'year' => $prevMonth->year,
                'month' => str_pad($prevMonth->month, 2, '0', STR_PAD_LEFT),
            ]),
            'next' => route('calendar.index', [
                'year' => $nextMonth->year,
                'month' => str_pad($nextMonth->month, 2, '0', STR_PAD_LEFT),
            ]),
        ];
    }
}
