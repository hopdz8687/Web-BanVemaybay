<?php

require_once __DIR__ . '/../../../Models/Booking.php';
require_once __DIR__ . '/../../../Models/Flight.php';
require_once __DIR__ . '/../../../Models/Ticket.php';

class ApiV1BookingController
{
    private static function mapBookingRow(array $row): array
    {
        $plane = null;
        if (isset($row['ma_may_bay']) || isset($row['ten_may_bay']) || isset($row['hang_may_bay'])) {
            $plane = [
                'ma_may_bay' => $row['ma_may_bay'] ?? null,
                'ten_may_bay' => $row['ten_may_bay'] ?? null,
                'hang_may_bay' => $row['hang_may_bay'] ?? null,
            ];
        }

        return [
            'id' => (int)($row['id'] ?? 0),
            'chuyen_bay_id' => isset($row['chuyen_bay_id']) ? (int)$row['chuyen_bay_id'] : null,
            've_id' => isset($row['ve_id']) ? (int)$row['ve_id'] : null,
            'trang_thai' => $row['trang_thai'] ?? null,
            'so_ghe_dat' => isset($row['so_ghe_dat']) ? (int)$row['so_ghe_dat'] : null,
            'tong_tien' => isset($row['tong_tien']) ? (float)$row['tong_tien'] : null,
            'dat_luc' => $row['dat_luc'] ?? null,
            'thanh_toan_luc' => $row['thanh_toan_luc'] ?? null,
            'chuyen_bay' => [
                'so_hieu' => $row['so_hieu'] ?? null,
                'noi_di' => $row['noi_di'] ?? null,
                'noi_den' => $row['noi_den'] ?? null,
                'gio_khoi_hanh' => $row['gio_khoi_hanh'] ?? null,
                'gio_ha_canh' => $row['gio_ha_canh'] ?? null,
                'may_bay' => $plane,
            ],
            've' => [
                'ma_ve' => $row['ma_ve'] ?? null,
                'hang_ve' => $row['hang_ve'] ?? null,
            ],
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
        $status = trim((string)($_GET['status'] ?? 'cart'));
        if ($status === '') {
            $status = 'cart';
        }

        $rows = Booking::listForUserByStatus((int)$user['id'], $status);
        $items = array_map([self::class, 'mapBookingRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach dat ve',
        ]);
    }

    public function show(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay dat ve',
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
            'message' => 'Chi tiet dat ve',
        ]);
    }

    public function store(array $user): void
    {
        $data = json_input();
        $flightId = (int)($data['chuyen_bay_id'] ?? 0);
        $ticketId = (int)($data['ve_id'] ?? 0);
        $qty = (int)($data['so_luong'] ?? 0);
        $passengers = $data['hanh_khach'] ?? [];

        if ($flightId <= 0 || $ticketId <= 0 || $qty <= 0) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
            ], 422);
        }

        if (!is_array($passengers) || count($passengers) !== $qty) {
            json_response([
                'ok' => false,
                'message' => 'Thong tin hanh khach khong hop le',
            ], 422);
        }

        $flight = Flight::find($flightId);
        if (!$flight) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay chuyen bay',
            ], 404);
        }

        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
            ], 404);
        }

        $max = (int)($ticket['so_luong_con'] ?? 0);
        $flightAvail = (int)($flight['ghe_con'] ?? 0);
        if ($max > $flightAvail) {
            $max = $flightAvail;
        }
        if ($qty > $max) {
            json_response([
                'ok' => false,
                'message' => 'So luong vuot qua so ghe con',
            ], 422);
        }

        foreach ($passengers as $p) {
            $name = trim((string)($p['ten'] ?? $p['ten_hanh_khach'] ?? ''));
            $phone = trim((string)($p['dien_thoai'] ?? ''));
            $email = trim((string)($p['email'] ?? $p['email_hanh_khach'] ?? ''));
            if ($name === '' || $phone === '' || $email === '') {
                json_response([
                    'ok' => false,
                    'message' => 'Vui long nhap day du thong tin hanh khach',
                ], 422);
            }
        }

        $ticketPrice = (float)($ticket['gia'] ?? 0);
        $ticketType = (string)($ticket['hang_ve'] ?? 'Thuong');
        $total = $ticketPrice * $qty;

        global $mysqli;
        $mysqli->begin_transaction();
        $reserved = false;
        try {
            $reserved = Ticket::reserve($ticketId, $qty);
            if (!$reserved) {
                throw new RuntimeException('Khong du so luong ve');
            }

            $bookingId = Booking::create(
                (int)$user['id'],
                $flightId,
                $qty,
                $total,
                $ticketId,
                'cart',
                []
            );
            if (!$bookingId) {
                throw new RuntimeException('Tao dat ve that bai');
            }

            $start = Booking::maxSeatNumberForFlight($flightId) + 1;
            for ($i = 0; $i < $qty; $i++) {
                $p = $passengers[$i] ?? [];
                $ok = Booking::addPassenger($bookingId, [
                    'name' => trim((string)($p['ten'] ?? $p['ten_hanh_khach'] ?? '')),
                    'phone' => trim((string)($p['dien_thoai'] ?? '')),
                    'email' => trim((string)($p['email'] ?? $p['email_hanh_khach'] ?? '')),
                    'gender' => (string)($p['gioi_tinh'] ?? 'Nam'),
                    'age' => (int)($p['tuoi'] ?? 18),
                    'ticket_type' => $ticketType,
                    'ticket_price' => $ticketPrice,
                    'seat_number' => $start + $i,
                ]);
                if (!$ok) {
                    throw new RuntimeException('Luu hanh khach that bai');
                }
            }

            if (!Flight::adjustSeats($flightId, -$qty)) {
                throw new RuntimeException('Cap nhat ghe that bai');
            }

            $mysqli->commit();
            json_response([
                'ok' => true,
                'data' => [
                    'booking_id' => $bookingId,
                    'trang_thai' => 'cart',
                ],
                'message' => 'Da tao dat ve',
            ], 201);
        } catch (Throwable $e) {
            $mysqli->rollback();
            if ($reserved) {
                Ticket::release($ticketId, $qty);
            }
            json_response([
                'ok' => false,
                'message' => $e->getMessage() ?: 'Dat ve that bai',
            ], 500);
        }
    }

    public function update(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'cart') {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay dat ve',
            ], 404);
        }

        $data = json_input();
        $soLuong = (int)($data['so_luong'] ?? 0);
        if ($soLuong <= 0) {
            json_response([
                'ok' => false,
                'message' => 'So luong khong hop le',
            ], 422);
        }

        $currentQty = (int)($booking['so_ghe_dat'] ?? 0);
        if ($soLuong === $currentQty) {
            json_response([
                'ok' => true,
                'data' => null,
                'message' => 'Khong co thay doi',
            ]);
        }

        $flight = Flight::find((int)$booking['chuyen_bay_id']);
        if (!$flight) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay chuyen bay',
            ], 404);
        }

        $ticket = null;
        $ticketPrice = 0.0;
        if (!empty($booking['ve_id'])) {
            $ticket = Ticket::find((int)$booking['ve_id']);
            $ticketPrice = (float)($ticket['gia'] ?? 0);
        }
        if ($ticketPrice <= 0 && $currentQty > 0) {
            $ticketPrice = (float)($booking['tong_tien'] ?? 0) / $currentQty;
        }

        $flightAvail = (int)($flight['ghe_con'] ?? 0) + $currentQty;
        $ticketAvail = $ticket ? ((int)($ticket['so_luong_con'] ?? 0) + $currentQty) : $flightAvail;
        $maxQty = min($flightAvail, $ticketAvail);
        if ($soLuong > $maxQty) {
            json_response([
                'ok' => false,
                'message' => 'So luong vuot qua so ghe con',
            ], 422);
        }

        $delta = $soLuong - $currentQty;

        global $mysqli;
        $mysqli->begin_transaction();
        $reservedMore = false;
        try {
            if ($delta > 0) {
                if ($ticket) {
                    $reservedMore = Ticket::reserve((int)$booking['ve_id'], $delta);
                    if (!$reservedMore) {
                        throw new RuntimeException('Khong du so luong ve');
                    }
                }
                if (!Flight::adjustSeats((int)$booking['chuyen_bay_id'], -$delta)) {
                    throw new RuntimeException('Cap nhat ghe that bai');
                }

                $existingPassengers = Booking::passengersForBooking($bookingId);
                $template = $existingPassengers[0] ?? null;
                $tplName = $template ? ($template['ten_hanh_khach'] ?? '') : ($user['ten'] ?? 'Hanh khach');
                $tplPhone = $template ? ($template['dien_thoai'] ?? '') : '';
                $tplEmail = $template ? ($template['email_hanh_khach'] ?? '') : ($user['email'] ?? '');
                $tplGender = $template ? ($template['gioi_tinh'] ?? 'Nam') : 'Nam';
                $tplAge = $template ? (int)($template['tuoi'] ?? 18) : 18;
                $tplLoaiVe = $template ? ($template['loai_ve'] ?? 'Thuong') : 'Thuong';

                $start = Booking::maxSeatNumberForFlight((int)$booking['chuyen_bay_id']) + 1;
                for ($i = 0; $i < $delta; $i++) {
                    $ok = Booking::addPassenger($bookingId, [
                        'name' => $tplName,
                        'phone' => $tplPhone,
                        'email' => $tplEmail,
                        'gender' => $tplGender,
                        'age' => $tplAge,
                        'ticket_type' => $tplLoaiVe,
                        'ticket_price' => $ticketPrice,
                        'seat_number' => $start + $i,
                    ]);
                    if (!$ok) {
                        throw new RuntimeException('Luu hanh khach that bai');
                    }
                }
            } elseif ($delta < 0) {
                $reduce = -$delta;
                if (!Booking::removeLastPassengers($bookingId, $reduce)) {
                    throw new RuntimeException('Cap nhat hanh khach that bai');
                }
                if (!Flight::adjustSeats((int)$booking['chuyen_bay_id'], $reduce)) {
                    throw new RuntimeException('Cap nhat ghe that bai');
                }
                if ($ticket) {
                    Ticket::release((int)$booking['ve_id'], $reduce);
                }
            }

            $newTotal = $ticketPrice * $soLuong;
            if (!Booking::updateCartBooking($bookingId, (int)$user['id'], $soLuong, $newTotal, [])) {
                throw new RuntimeException('Cap nhat don that bai');
            }

            $mysqli->commit();
            json_response([
                'ok' => true,
                'data' => null,
                'message' => 'Da cap nhat dat ve',
            ]);
        } catch (Throwable $e) {
            $mysqli->rollback();
            if ($ticket && $delta > 0 && $reservedMore) {
                Ticket::release((int)$booking['ve_id'], $delta);
            }
            json_response([
                'ok' => false,
                'message' => $e->getMessage() ?: 'Cap nhat that bai',
            ], 500);
        }
    }

    public function destroy(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'cart') {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay dat ve',
            ], 404);
        }

        if (!Booking::deleteForUser($bookingId, (int)$user['id'])) {
            json_response([
                'ok' => false,
                'message' => 'Khong the xoa dat ve',
            ], 500);
        }

        Flight::adjustSeats((int)$booking['chuyen_bay_id'], (int)$booking['so_ghe_dat']);
        if (!empty($booking['ve_id'])) {
            Ticket::release((int)$booking['ve_id'], (int)$booking['so_ghe_dat']);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da xoa dat ve',
        ]);
    }

    public function checkout(array $user, int $bookingId): void
    {
        $booking = Booking::findForUser($bookingId, (int)$user['id']);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'cart') {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay dat ve',
            ], 404);
        }

        $data = json_input();
        $payName = trim((string)($data['ten_thanh_toan'] ?? ''));
        $payPhone = trim((string)($data['dien_thoai_thanh_toan'] ?? ''));
        $payEmail = trim((string)($data['email_thanh_toan'] ?? ''));
        $payAddress = trim((string)($data['dia_chi_thanh_toan'] ?? ''));
        $paymentMethod = (string)($data['phuong_thuc_thanh_toan'] ?? 'direct');
        $allowed = ['direct', 'atm', 'momo'];
        if (!in_array($paymentMethod, $allowed, true)) {
            $paymentMethod = 'direct';
        }

        $affected = Booking::paySelectedWithPayment((int)$user['id'], [$bookingId], [
            'ten_thanh_toan' => $payName,
            'email_thanh_toan' => $payEmail,
            'dien_thoai_thanh_toan' => $payPhone,
            'dia_chi_thanh_toan' => $payAddress,
            'phuong_thuc_thanh_toan' => $paymentMethod,
        ]);

        if ($affected <= 0) {
            json_response([
                'ok' => false,
                'message' => 'Thanh toan that bai',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => [
                'booking_id' => $bookingId,
                'trang_thai' => 'paid',
            ],
            'message' => 'Thanh toan thanh cong',
        ]);
    }
}
