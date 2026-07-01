<?php

require_once __DIR__ . '/../../../Models/Booking.php';

class ApiV1TicketController
{
    private static function mapBookingRow(array $row): array
    {
        $plane = trim((string)($row['ma_may_bay'] ?? '') . (!empty($row['ten_may_bay']) ? (' - ' . ($row['ten_may_bay'] ?? '')) : ''));
        return [
            'id' => (int)($row['id'] ?? 0),
            'so_hieu' => $row['so_hieu'] ?? null,
            'noi_di' => $row['noi_di'] ?? null,
            'noi_den' => $row['noi_den'] ?? null,
            'gio_khoi_hanh' => $row['gio_khoi_hanh'] ?? null,
            'gio_ha_canh' => $row['gio_ha_canh'] ?? null,
            'hang_may_bay' => $row['hang_may_bay'] ?? null,
            'may_bay' => $plane,
            'so_ghe_dat' => isset($row['so_ghe_dat']) ? (int)$row['so_ghe_dat'] : null,
            'tong_tien' => isset($row['tong_tien']) ? (float)$row['tong_tien'] : null,
            'trang_thai' => $row['trang_thai'] ?? null,
            'ma_ve' => $row['ma_ve'] ?? null,
            'hang_ve' => $row['hang_ve'] ?? null,
            'dat_luc' => $row['dat_luc'] ?? null,
            'thanh_toan_luc' => $row['thanh_toan_luc'] ?? null,
        ];
    }

    private static function mapPassengerRow(array $row): array
    {
        return [
            'ten_hanh_khach' => $row['ten_hanh_khach'] ?? null,
            'dien_thoai' => $row['dien_thoai'] ?? null,
            'email_hanh_khach' => $row['email_hanh_khach'] ?? null,
            'gioi_tinh' => $row['gioi_tinh'] ?? null,
            'tuoi' => isset($row['tuoi']) ? (int)$row['tuoi'] : null,
            'loai_ve' => $row['loai_ve'] ?? null,
            'gia_ve' => isset($row['gia_ve']) ? (float)$row['gia_ve'] : null,
            'so_ghe' => isset($row['so_ghe']) ? (int)$row['so_ghe'] : null,
        ];
    }

    public function index(array $user): void
    {
        $rows = Booking::listForUserByStatus((int)$user['id'], 'paid');
        $items = array_map([self::class, 'mapBookingRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach ve cua toi',
        ]);
    }

    public function show(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'paid') {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
            ], 404);
        }

        $passengers = Booking::passengersForBooking($bookingId);
        $passengers = array_map([self::class, 'mapPassengerRow'], $passengers);

        json_response([
            'ok' => true,
            'data' => [
                'booking' => $booking,
                'hanh_khach' => $passengers,
            ],
            'message' => 'Chi tiet ve',
        ]);
    }

    public function updatePassengers(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'paid') {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
            ], 404);
        }

        $data = json_input();
        $passengers = $data['hanh_khach'] ?? [];
        if (!is_array($passengers) || empty($passengers)) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu hanh khach khong hop le',
            ], 422);
        }

        $payload = [];
        foreach ($passengers as $p) {
            $payload[] = [
                'name' => trim((string)($p['ten_hanh_khach'] ?? $p['ten'] ?? '')),
                'phone' => trim((string)($p['dien_thoai'] ?? '')),
                'email' => trim((string)($p['email_hanh_khach'] ?? $p['email'] ?? '')),
                'gender' => (string)($p['gioi_tinh'] ?? 'Nam'),
                'age' => (int)($p['tuoi'] ?? 18),
                'seat_number' => (int)($p[''] ?? 0),
            ];
        }

        $ok = Booking::updatePassengersInfoBySeat($bookingId, $payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Cap nhat hanh khach that bai',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da cap nhat hanh khach',
        ]);
    }
}
