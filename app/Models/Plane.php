<?php
require_once __DIR__ . '/../Helpers/helpers.php';

class Plane {
    public static function allOrdered(string $ma_may_bay = ''): array {
        global $mysqli;
        $ma_may_bay = strtoupper(trim($ma_may_bay));

        if ($ma_may_bay !== '') {
            $stmt = $mysqli->prepare('SELECT * FROM may_bay WHERE ma_may_bay LIKE ? ORDER BY id DESC');
            if ($stmt) {
                $like = '%' . $ma_may_bay . '%';
                $stmt->bind_param('s', $like);
                $stmt->execute();
                $res = $stmt->get_result();
                return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            }
        }

        $res = $mysqli->query('SELECT * FROM may_bay ORDER BY id DESC');
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function find(int $id): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT * FROM may_bay WHERE id=? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function findByCode(string $ma_may_bay): ?array {
        global $mysqli;
        $ma_may_bay = strtoupper(trim($ma_may_bay));
        $stmt = $mysqli->prepare('SELECT * FROM may_bay WHERE ma_may_bay=? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $ma_may_bay);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function create(array $data): bool {
        global $mysqli;
        if (isset($data['ma_may_bay'])) {
            $data['ma_may_bay'] = strtoupper(trim($data['ma_may_bay']));
        }
        $hang_may_bay = $data['hang_may_bay'] ?? 'Khác';
        $stmt = $mysqli->prepare('INSERT INTO may_bay (ma_may_bay, ten_may_bay, hang_may_bay) VALUES (?, ?, ?)');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sss', $data['ma_may_bay'], $data['ten_may_bay'], $hang_may_bay);
        return $stmt->execute();
    }

    public static function update(int $id, array $data): bool {
        global $mysqli;
        if (isset($data['ma_may_bay'])) {
            $data['ma_may_bay'] = strtoupper(trim($data['ma_may_bay']));
        }
        $hang_may_bay = $data['hang_may_bay'] ?? 'Khác';
        $stmt = $mysqli->prepare('UPDATE may_bay SET ma_may_bay=?, ten_may_bay=?, hang_may_bay=? WHERE id=?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sssi', $data['ma_may_bay'], $data['ten_may_bay'], $hang_may_bay, $id);
        return $stmt->execute();
    }

    public static function delete(int $id): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('DELETE FROM may_bay WHERE id=?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
