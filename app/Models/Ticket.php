<?php
require_once __DIR__ . '/../Helpers/helpers.php';

class Ticket {
    public static function byFlightId(int $flightId): array {
        global $mysqli;
        $stmt = $mysqli->prepare(
            'SELECT v.id, v.ma_ve, cb.so_hieu AS ma_chuyen_bay, v.hang_ve, v.gia, v.so_luong_con '
            . 'FROM ve v '
            . 'JOIN chuyen_bay cb ON v.chuyen_bay_id = cb.id '
            . 'WHERE v.chuyen_bay_id = ? '
            . 'ORDER BY v.id DESC'
        );
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $flightId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function find(int $id): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT * FROM ve WHERE id=? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function findByCode(string $maVe): ?array {
        global $mysqli;
        $maVe = strtoupper(trim($maVe));
        $stmt = $mysqli->prepare('SELECT * FROM ve WHERE ma_ve=? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $maVe);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function create(array $data): bool {
        global $mysqli;
        $maVe = strtoupper(trim($data['ma_ve'] ?? ''));
        $chuyenBayId = (int)($data['chuyen_bay_id'] ?? 0);
        $hangVe = $data['hang_ve'] ?? 'Thuong';
        $gia = (float)($data['gia'] ?? 0);
        $soLuongCon = (int)($data['so_luong_con'] ?? 0);

        $stmt = $mysqli->prepare('INSERT INTO ve (ma_ve, chuyen_bay_id, hang_ve, gia, so_luong_con) VALUES (?,?,?,?,?)');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sisdi', $maVe, $chuyenBayId, $hangVe, $gia, $soLuongCon);
        return $stmt->execute();
    }

    public static function update(int $id, array $data): bool {
        global $mysqli;
        $maVe = strtoupper(trim($data['ma_ve'] ?? ''));
        $hangVe = $data['hang_ve'] ?? 'Thuong';
        $gia = (float)($data['gia'] ?? 0);
        $soLuongCon = (int)($data['so_luong_con'] ?? 0);

        $stmt = $mysqli->prepare('UPDATE ve SET ma_ve=?, hang_ve=?, gia=?, so_luong_con=? WHERE id=?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssdii', $maVe, $hangVe, $gia, $soLuongCon, $id);
        return $stmt->execute();
    }

    public static function delete(int $id): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('DELETE FROM ve WHERE id=?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function reserve(int $ticketId, int $qty): bool {
        if ($ticketId <= 0 || $qty <= 0) {
            return false;
        }
        global $mysqli;
        $stmt = $mysqli->prepare('UPDATE ve SET so_luong_con = so_luong_con - ? WHERE id = ? AND so_luong_con >= ?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('iii', $qty, $ticketId, $qty);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public static function release(int $ticketId, int $qty): bool {
        if ($ticketId <= 0 || $qty <= 0) {
            return false;
        }
        global $mysqli;
        $stmt = $mysqli->prepare('UPDATE ve SET so_luong_con = so_luong_con + ? WHERE id = ?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ii', $qty, $ticketId);
        return $stmt->execute();
    }

}
