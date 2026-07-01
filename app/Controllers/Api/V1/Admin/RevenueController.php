<?php

require_once __DIR__ . '/../../../../Models/Booking.php';

class ApiV1AdminRevenueController
{
    public function summary(): void
    {
        $mode = (string)($_GET['mode'] ?? 'day');
        if (!in_array($mode, ['day', 'month', 'year', 'all'], true)) {
            $mode = 'day';
        }

        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear = (int)date('Y');

        $day = (string)($_GET['day'] ?? ($_GET['date'] ?? $today));
        $month = (string)($_GET['month'] ?? $thisMonth);
        $year = isset($_GET['year']) ? (int)$_GET['year'] : $thisYear;

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            $day = $today;
        }
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = $thisMonth;
        }
        if ($year < 2000 || $year > 2100) {
            $year = $thisYear;
        }

        $summary = ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
        $breakdown = [];
        $bookings = [];

        if ($mode === 'all') {
            $summary = Booking::paidRevenueSummaryAll();
            $breakdown = Booking::paidRevenueBreakdownAllByYear();
        } elseif ($mode === 'day') {
            $summary = Booking::paidRevenueSummaryByDay($day);
            $bookings = Booking::paidBookingsForDay($day);
        } elseif ($mode === 'month') {
            $summary = Booking::paidRevenueSummaryByMonth($month);
            $breakdown = Booking::paidRevenueBreakdownByMonth($month);
        } else {
            $summary = Booking::paidRevenueSummaryByYear($year);
            $breakdown = Booking::paidRevenueBreakdownByYear($year);
        }

        json_response([
            'ok' => true,
            'data' => [
                'mode' => $mode,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'summary' => $summary,
                'breakdown' => $breakdown,
                'bookings' => $bookings,
            ],
            'message' => 'Thong ke doanh thu',
        ]);
    }
}
