<?php
require_once __DIR__ . '/../Helpers/helpers.php';

class Flight {
    public static function allOrdered(string $so_hieu = ''): array {
        global $mysqli;
        $so_hieu = strtoupper(trim($so_hieu));

        if ($so_hieu !== '') {
            $stmt = $mysqli->prepare(
                'SELECT cb.*, mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay '
                . 'FROM chuyen_bay cb '
                . 'LEFT JOIN may_bay mb ON cb.may_bay_id = mb.id '
                . 'WHERE cb.so_hieu LIKE ? '
                . 'ORDER BY cb.gio_khoi_hanh DESC'
            );
            if ($stmt) {
                $like = '%' . $so_hieu . '%';
                $stmt->bind_param('s', $like);
                $stmt->execute();
                $res = $stmt->get_result();
                return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            }
        }

        $sql = 'SELECT cb.*, mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay '
             . 'FROM chuyen_bay cb '
             . 'LEFT JOIN may_bay mb ON cb.may_bay_id = mb.id '
             . 'ORDER BY cb.gio_khoi_hanh DESC';
        $res = $mysqli->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        $res2 = $mysqli->query('SELECT * FROM chuyen_bay ORDER BY gio_khoi_hanh DESC');
        return $res2 ? $res2->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function recent(int $limit = 6): array {
        global $mysqli;
        $stmt = $mysqli->prepare(
            'SELECT cb.*, mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay '
            . 'FROM chuyen_bay cb '
            . 'LEFT JOIN may_bay mb ON cb.may_bay_id = mb.id '
            . 'ORDER BY cb.gio_khoi_hanh ASC LIMIT ?'
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function search(string $origin = '', string $destination = '', string $date = ''): array {
        global $mysqli;
        $sql = 'SELECT cb.*, mb.hang_may_bay, mb.ma_may_bay, mb.ten_may_bay '
             . 'FROM chuyen_bay cb '
             . 'LEFT JOIN may_bay mb ON cb.may_bay_id = mb.id '
             . 'WHERE 1=1';
        $params = [];
        $types = '';

        if ($origin !== '') {
            $sql .= ' AND cb.noi_di LIKE ?';
            $params[] = "%$origin%";
            $types .= 's';
        }
        if ($destination !== '') {
            $sql .= ' AND cb.noi_den LIKE ?';
            $params[] = "%$destination%";
            $types .= 's';
        }
        if ($date !== '') {
            $sql .= ' AND DATE(cb.gio_khoi_hanh) = ?';
            $params[] = $date;
            $types .= 's';
        }

        $sql .= ' ORDER BY cb.gio_khoi_hanh ASC';

        $stmt = $mysqli->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function find(int $id): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT * FROM chuyen_bay WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function findByCode(string $so_hieu): ?array {
        global $mysqli;
        $so_hieu = strtoupper(trim($so_hieu));
        $stmt = $mysqli->prepare('SELECT * FROM chuyen_bay WHERE so_hieu = ? LIMIT 1');
        $stmt->bind_param('s', $so_hieu);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function create(array $data): bool {
        global $mysqli;
        // Chuan hoa ma chuyen bay truoc khi luu
        if (isset($data['so_hieu'])) {
            $data['so_hieu'] = strtoupper(trim($data['so_hieu']));
        }
        $may_bay_id = $data['may_bay_id'] ?? null;
        if ($may_bay_id === '' || $may_bay_id === 0) {
            $may_bay_id = null;
        }
        $stmt = $mysqli->prepare('INSERT INTO chuyen_bay (so_hieu,noi_di,noi_den,gio_khoi_hanh,gio_ha_canh,gia_thuong,gia_thuong_gia,ghe_con,may_bay_id) VALUES (?,?,?,?,?,?,?,?,?)');
        if (!$stmt) {
            throw new Exception('MySQL prepare failed: ' . ($mysqli->error ?? 'unknown error'));
        }
        $bind = $stmt->bind_param(
            'sssssddii',
            $data['so_hieu'],
            $data['noi_di'],
            $data['noi_den'],
            $data['gio_khoi_hanh'],
            $data['gio_ha_canh'],
            $data['gia_thuong'],
            $data['gia_thuong_gia'],
            $data['ghe_con'],
            $may_bay_id
        );
        if ($bind === false) {
            throw new Exception('bind_param failed: ' . ($stmt->error ?: $mysqli->error));
        }
        $exec = $stmt->execute();
        if ($exec === false) {
            throw new Exception('MySQL execute failed: ' . ($stmt->error ?: $mysqli->error));
        }
        return true;
    }

    public static function update(int $id, array $data): bool {
        global $mysqli;
        $may_bay_id = $data['may_bay_id'] ?? null;
        if ($may_bay_id === '' || $may_bay_id === 0) {
            $may_bay_id = null;
        }
        $stmt = $mysqli->prepare('UPDATE chuyen_bay SET so_hieu=?, noi_di=?, noi_den=?, gio_khoi_hanh=?, gio_ha_canh=?, gia_thuong=?, gia_thuong_gia=?, ghe_con=?, may_bay_id=? WHERE id=?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'sssssddiii',
            $data['so_hieu'],
            $data['noi_di'],
            $data['noi_den'],
            $data['gio_khoi_hanh'],
            $data['gio_ha_canh'],
            $data['gia_thuong'],
            $data['gia_thuong_gia'],
            $data['ghe_con'],
            $may_bay_id,
            $id
        );
        return $stmt->execute();
    }

    public static function delete(int $id): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('DELETE FROM chuyen_bay WHERE id=?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function adjustSeats(int $id, int $delta): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('UPDATE chuyen_bay SET ghe_con = ghe_con + ? WHERE id = ?');
        $stmt->bind_param('ii', $delta, $id);
        return $stmt->execute();
    }
}
