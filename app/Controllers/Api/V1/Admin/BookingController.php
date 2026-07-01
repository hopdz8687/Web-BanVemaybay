<?php

require_once __DIR__ . '/../../../../Models/Booking.php';

class ApiV1AdminBookingController
{
    private static function mapBookingRow(array $row): array
    {
        $plane = trim((string)($row['ma_may_bay'] ?? '') . (!empty($row['ten_may_bay']) ? (' - ' . ($row['ten_may_bay'] ?? '')) : ''));
        return [
            'id' => (int)($row['id'] ?? 0),
            'nguoi_dung_id' => isset($row['nguoi_dung_id']) ? (int)$row['nguoi_dung_id'] : null,
            'customer_name' => $row['customer_name'] ?? null,
            'customer_email' => $row['customer_email'] ?? null,
            'so_hieu' => $row['so_hieu'] ?? null,
            'noi_di' => $row['noi_di'] ?? null,
            'noi_den' => $row['noi_den'] ?? null,
            'gio_khoi_hanh' => $row['gio_khoi_hanh'] ?? null,
            'trang_thai' => $row['trang_thai'] ?? null,
            'dat_luc' => $row['dat_luc'] ?? null,
            'thanh_toan_luc' => $row['thanh_toan_luc'] ?? null,
            'so_ghe_dat' => isset($row['so_ghe_dat']) ? (int)$row['so_ghe_dat'] : null,
            'tong_tien' => isset($row['tong_tien']) ? (float)$row['tong_tien'] : null,
            'hang_may_bay' => $row['hang_may_bay'] ?? null,
            'may_bay' => $plane,
        ];
    }

    public function index(): void
    {
        $status = trim((string)($_GET['status'] ?? 'paid'));
        $rows = Booking::listWithUsers($status);
        $items = array_map([self::class, 'mapBookingRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach dat ve',
        ]);
    }

    public function show(int $id): void
    {
        $rows = Booking::listWithUsers();
        $booking = null;
        foreach ($rows as $r) {
            if ((int)($r['id'] ?? 0) === $id) {
                $booking = $r;
                break;
            }
        }

        if (!$booking) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay dat ve',
            ], 404);
        }

        $passengers = Booking::passengersForBooking($id);

        json_response([
            'ok' => true,
            'data' => [
                'booking' => self::mapBookingRow($booking),
                'hanh_khach' => $passengers,
            ],
            'message' => 'Chi tiet dat ve',
        ]);
    }
}
