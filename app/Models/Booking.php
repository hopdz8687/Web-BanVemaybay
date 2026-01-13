<?php
require_once __DIR__ . '/../Helpers/helpers.php';

class Booking {
    private static function paidAtExpr(): string {
        // Prefer payment timestamp; fall back to booking timestamp if needed
        return 'COALESCE(b.thanh_toan_luc, b.dat_luc)';
    }

    public static function paidRevenueSummaryByDay(string $dateYmd): array {
        global $mysqli;
        $sql = 'SELECT COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' AND DATE(" . self::paidAtExpr() . ") = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
        }
        $stmt->bind_param('s', $dateYmd);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return [
            'total_revenue' => (float)($row['total_revenue'] ?? 0),
            'total_bookings' => (int)($row['total_bookings'] ?? 0),
            'total_seats' => (int)($row['total_seats'] ?? 0),
        ];
    }

    public static function paidRevenueSummaryByMonth(string $yearMonth): array {
        global $mysqli;
        $sql = 'SELECT COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' AND DATE_FORMAT(" . self::paidAtExpr() . ", '%Y-%m') = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
        }
        $stmt->bind_param('s', $yearMonth);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return [
            'total_revenue' => (float)($row['total_revenue'] ?? 0),
            'total_bookings' => (int)($row['total_bookings'] ?? 0),
            'total_seats' => (int)($row['total_seats'] ?? 0),
        ];
    }

    public static function paidRevenueSummaryByYear(int $year): array {
        global $mysqli;
        $sql = 'SELECT COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' AND YEAR(" . self::paidAtExpr() . ") = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
        }
        $stmt->bind_param('i', $year);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return [
            'total_revenue' => (float)($row['total_revenue'] ?? 0),
            'total_bookings' => (int)($row['total_bookings'] ?? 0),
            'total_seats' => (int)($row['total_seats'] ?? 0),
        ];
    }

    public static function paidRevenueSummaryAll(): array {
        global $mysqli;
        $sql = 'SELECT COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid'";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return [
            'total_revenue' => (float)($row['total_revenue'] ?? 0),
            'total_bookings' => (int)($row['total_bookings'] ?? 0),
            'total_seats' => (int)($row['total_seats'] ?? 0),
        ];
    }

    public static function paidRevenueBreakdownAllByYear(): array {
        global $mysqli;
        $sql = 'SELECT YEAR(' . self::paidAtExpr() . ') AS period_year, '
             . 'COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' "
             . 'GROUP BY YEAR(' . self::paidAtExpr() . ') '
             . 'ORDER BY period_year ASC';
        $res = $mysqli->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function paidRevenueBreakdownByMonth(string $yearMonth): array {
        global $mysqli;
        $sql = 'SELECT DATE(' . self::paidAtExpr() . ') AS period_day, '
             . 'COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' AND DATE_FORMAT(" . self::paidAtExpr() . ", '%Y-%m') = ? "
             . 'GROUP BY DATE(' . self::paidAtExpr() . ') '
             . 'ORDER BY period_day ASC';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $yearMonth);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function paidRevenueBreakdownByYear(int $year): array {
        global $mysqli;
        $sql = 'SELECT MONTH(' . self::paidAtExpr() . ') AS period_month, '
             . 'COALESCE(SUM(b.tong_tien),0) AS total_revenue, COUNT(*) AS total_bookings, COALESCE(SUM(b.so_ghe_dat),0) AS total_seats '
             . 'FROM dat_ve b '
             . "WHERE b.trang_thai='paid' AND YEAR(" . self::paidAtExpr() . ") = ? "
             . 'GROUP BY MONTH(' . self::paidAtExpr() . ') '
             . 'ORDER BY period_month ASC';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $year);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function paidBookingsForDay(string $dateYmd): array {
        global $mysqli;
        $sql = 'SELECT b.*, '
             . 'f.so_hieu, f.noi_di, f.noi_den, f.gio_khoi_hanh, '
             . 'u.ten AS customer_name, u.email AS customer_email '
             . 'FROM dat_ve b '
             . 'JOIN chuyen_bay f ON b.chuyen_bay_id = f.id '
             . 'JOIN nguoi_dung u ON b.nguoi_dung_id = u.id '
             . "WHERE b.trang_thai='paid' AND DATE(" . self::paidAtExpr() . ") = ? "
             . 'ORDER BY ' . self::paidAtExpr() . ' DESC';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $dateYmd);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public static function find(int $bookingId): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT * FROM dat_ve WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function create(
        int $userId,
        int $flightId,
        int $seats,
        float $total,
        ?int $ticketId = null,
        string $status = 'cart',
        array $paymentInfo = []
    ): int {
        global $mysqli;

        $veId = $ticketId !== null ? (int)$ticketId : null;
        $tenThanhToan = $paymentInfo['ten_thanh_toan'] ?? null;
        $emailThanhToan = $paymentInfo['email_thanh_toan'] ?? null;
        $dienThoaiThanhToan = $paymentInfo['dien_thoai_thanh_toan'] ?? null;
        $diaChiThanhToan = $paymentInfo['dia_chi_thanh_toan'] ?? null;

        $stmt = $mysqli->prepare(
            'INSERT INTO dat_ve (nguoi_dung_id, chuyen_bay_id, ve_id, trang_thai, ten_thanh_toan, email_thanh_toan, dien_thoai_thanh_toan, dia_chi_thanh_toan, so_ghe_dat, tong_tien) '
            . 'VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->bind_param('iiisssssid', $userId, $flightId, $veId, $status, $tenThanhToan, $emailThanhToan, $dienThoaiThanhToan, $diaChiThanhToan, $seats, $total);
        if ($stmt->execute()) {
            return (int)$mysqli->insert_id;
        }
        return 0;
    }

    public static function addPassenger(int $bookingId, array $p): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('INSERT INTO hanh_khach (dat_ve_id, ten_hanh_khach, dien_thoai, email_hanh_khach, gioi_tinh, tuoi, loai_ve, gia_ve, so_ghe) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param(
            'issssisdi',
            $bookingId,
            $p['name'],
            $p['phone'],
            $p['email'],
            $p['gender'],
            $p['age'],
            $p['ticket_type'],
            $p['ticket_price'],
            $p['seat_number']
        );
        return $stmt->execute();
    }

    public static function findForUser(int $bookingId, int $userId): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT * FROM dat_ve WHERE id = ? AND nguoi_dung_id = ? LIMIT 1');
        $stmt->bind_param('ii', $bookingId, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function deleteForUser(int $bookingId, int $userId): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('DELETE FROM dat_ve WHERE id = ? AND nguoi_dung_id = ?');
        $stmt->bind_param('ii', $bookingId, $userId);
        return $stmt->execute();
    }

    public static function delete(int $bookingId): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('DELETE FROM dat_ve WHERE id = ?');
        $stmt->bind_param('i', $bookingId);
        return $stmt->execute();
    }

    public static function listForUserByStatus(int $userId, string $status): array {
        global $mysqli;
        $sql = 'SELECT b.*, f.so_hieu, f.noi_di, f.noi_den, f.gio_khoi_hanh, f.gio_ha_canh,
                       mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay,
                       v.ma_ve, v.hang_ve
         FROM dat_ve b
         JOIN chuyen_bay f ON b.chuyen_bay_id = f.id
         LEFT JOIN may_bay mb ON f.may_bay_id = mb.id
         LEFT JOIN ve v ON b.ve_id = v.id
         WHERE b.nguoi_dung_id = ? AND b.trang_thai = ?
         ORDER BY b.dat_luc DESC';
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('is', $userId, $status);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function listForUserByIdsAndStatus(int $userId, array $bookingIds, string $status): array {
        $bookingIds = array_values(array_filter(array_map('intval', $bookingIds), static fn($v) => $v > 0));
        if (empty($bookingIds)) {
            return [];
        }
        global $mysqli;
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
        $types = 'is' . str_repeat('i', count($bookingIds));
        $params = array_merge([$userId, $status], $bookingIds);
        $sql = 'SELECT b.*, f.so_hieu, f.noi_di, f.noi_den, f.gio_khoi_hanh, f.gio_ha_canh,
                       mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay,
                       v.ma_ve, v.hang_ve
         FROM dat_ve b
         JOIN chuyen_bay f ON b.chuyen_bay_id = f.id
         LEFT JOIN may_bay mb ON f.may_bay_id = mb.id
         LEFT JOIN ve v ON b.ve_id = v.id
         WHERE b.nguoi_dung_id = ? AND b.trang_thai = ? AND b.id IN (' . $placeholders . ')
         ORDER BY b.dat_luc DESC';
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function paySelected(int $userId, array $bookingIds): int {
        $bookingIds = array_values(array_filter(array_map('intval', $bookingIds), static fn($v) => $v > 0));
        if (empty($bookingIds)) {
            return 0;
        }
        global $mysqli;
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
        $types = 'i' . str_repeat('i', count($bookingIds));
        $params = array_merge([$userId], $bookingIds);
        $sql = "UPDATE dat_ve SET trang_thai='paid', thanh_toan_luc=COALESCE(thanh_toan_luc, CURRENT_TIMESTAMP()) "
             . "WHERE nguoi_dung_id=? AND trang_thai='cart' AND id IN ($placeholders)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->affected_rows;
    }

    public static function paySelectedWithPayment(int $userId, array $bookingIds, array $paymentInfo = []): int {
        $bookingIds = array_values(array_filter(array_map('intval', $bookingIds), static fn($v) => $v > 0));
        if (empty($bookingIds)) {
            return 0;
        }

        $tenThanhToan = $paymentInfo['ten_thanh_toan'] ?? null;
        $emailThanhToan = $paymentInfo['email_thanh_toan'] ?? null;
        $dienThoaiThanhToan = $paymentInfo['dien_thoai_thanh_toan'] ?? null;
        $diaChiThanhToan = $paymentInfo['dia_chi_thanh_toan'] ?? null;
        $phuongThucThanhToan = $paymentInfo['phuong_thuc_thanh_toan'] ?? null;

        global $mysqli;
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
           $types = 'sssssi' . str_repeat('i', count($bookingIds));
           $params = array_merge([$tenThanhToan, $emailThanhToan, $dienThoaiThanhToan, $diaChiThanhToan, $phuongThucThanhToan, $userId], $bookingIds);
           $sql = "UPDATE dat_ve SET trang_thai='paid', ten_thanh_toan=?, email_thanh_toan=?, dien_thoai_thanh_toan=?, dia_chi_thanh_toan=?, phuong_thuc_thanh_toan=?, thanh_toan_luc=COALESCE(thanh_toan_luc, CURRENT_TIMESTAMP()) "
             . "WHERE nguoi_dung_id=? AND trang_thai='cart' AND id IN ($placeholders)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->affected_rows;
    }

    public static function updateCartBooking(int $bookingId, int $userId, int $seats, float $total, array $paymentInfo = []): bool {
        global $mysqli;
        $tenThanhToan = $paymentInfo['ten_thanh_toan'] ?? null;
        $emailThanhToan = $paymentInfo['email_thanh_toan'] ?? null;
        $dienThoaiThanhToan = $paymentInfo['dien_thoai_thanh_toan'] ?? null;
        $diaChiThanhToan = $paymentInfo['dia_chi_thanh_toan'] ?? null;

        $stmt = $mysqli->prepare('UPDATE dat_ve SET so_ghe_dat=?, tong_tien=?, ten_thanh_toan=?, email_thanh_toan=?, dien_thoai_thanh_toan=?, dia_chi_thanh_toan=? WHERE id=? AND nguoi_dung_id=? AND trang_thai=\'cart\'');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('idssssii', $seats, $total, $tenThanhToan, $emailThanhToan, $dienThoaiThanhToan, $diaChiThanhToan, $bookingId, $userId);
        return $stmt->execute();
    }

    public static function removeLastPassengers(int $bookingId, int $count): bool {
        $count = (int)$count;
        if ($bookingId <= 0 || $count <= 0) {
            return true;
        }
        global $mysqli;
        $bookingId = (int)$bookingId;
        // MySQL/MariaDB supports ORDER BY + LIMIT in DELETE
        $sql = 'DELETE FROM hanh_khach WHERE dat_ve_id=' . $bookingId . ' ORDER BY so_ghe DESC LIMIT ' . $count;
        return (bool)$mysqli->query($sql);
    }

    public static function passengersByBookingIds(array $bookingIds): array {
        if (empty($bookingIds)) return [];
        global $mysqli;
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
        $types = str_repeat('i', count($bookingIds));
        $stmt = $mysqli->prepare('SELECT * FROM hanh_khach WHERE dat_ve_id IN (' . $placeholders . ') ORDER BY so_ghe ASC');
        $stmt->bind_param($types, ...$bookingIds);
        $stmt->execute();
        $res = $stmt->get_result();
        $map = [];
        while ($row = $res->fetch_assoc()) {
            $map[$row['dat_ve_id']][] = $row;
        }
        return $map;
    }

    public static function passengersForBooking(int $bookingId): array {
        $map = self::passengersByBookingIds([$bookingId]);
        return $map[$bookingId] ?? [];
    }

    public static function listWithUsers(?string $status = null): array {
        global $mysqli;
        $base = 'SELECT b.*, f.so_hieu, f.noi_di, f.noi_den, f.gio_khoi_hanh,
                       mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay,
                       u.ten AS customer_name, u.email AS customer_email
         FROM dat_ve b
         JOIN chuyen_bay f ON b.chuyen_bay_id = f.id
         LEFT JOIN may_bay mb ON f.may_bay_id = mb.id
         JOIN nguoi_dung u ON b.nguoi_dung_id = u.id';

        if ($status === null || $status === '') {
            $sql = $base . ' ORDER BY b.dat_luc DESC';
            $res = $mysqli->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }

        $sql = $base . ' WHERE b.trang_thai = ? ORDER BY b.dat_luc DESC';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function setConfirmed(int $bookingId, bool $confirmed): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('UPDATE dat_ve SET da_xac_nhan = ? WHERE id = ?');
        $val = $confirmed ? 1 : 0;
        $stmt->bind_param('ii', $val, $bookingId);
        return $stmt->execute();
    }

    public static function updatePassengersInfoBySeat(int $bookingId, array $passengers): bool {
        if ($bookingId <= 0 || empty($passengers)) {
            return false;
        }
        global $mysqli;
        $mysqli->begin_transaction();
        try {
            $stmt = $mysqli->prepare('UPDATE hanh_khach SET ten_hanh_khach=?, dien_thoai=?, email_hanh_khach=?, gioi_tinh=?, tuoi=? WHERE dat_ve_id=? AND so_ghe=?');
            if (!$stmt) {
                $mysqli->rollback();
                return false;
            }

            foreach ($passengers as $p) {
                $name = (string)($p['name'] ?? '');
                $phone = (string)($p['phone'] ?? '');
                $email = (string)($p['email'] ?? '');
                $gender = (string)($p['gender'] ?? 'Nam');
                $age = (int)($p['age'] ?? 18);
                $seat = (int)($p['seat_number'] ?? 0);
                if ($seat <= 0 || trim($name) === '' || trim($phone) === '' || trim($email) === '') {
                    $mysqli->rollback();
                    return false;
                }

                $stmt->bind_param('ssssiii', $name, $phone, $email, $gender, $age, $bookingId, $seat);
                if (!$stmt->execute()) {
                    $mysqli->rollback();
                    return false;
                }
            }

            $mysqli->commit();
            return true;
        } catch (Throwable $e) {
            $mysqli->rollback();
            return false;
        }
    }

    public static function updateTotalPrice(int $bookingId, float $total): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('UPDATE dat_ve SET tong_tien = ? WHERE id = ?');
        $stmt->bind_param('di', $total, $bookingId);
        return $stmt->execute();
    }

    public static function maxSeatNumberForFlight(int $flightId): int {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT MAX(hk.so_ghe) AS max_seat FROM hanh_khach hk JOIN dat_ve b ON hk.dat_ve_id = b.id WHERE b.chuyen_bay_id = ?');
        $stmt->bind_param('i', $flightId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['max_seat'] ?? 0);
    }
}
