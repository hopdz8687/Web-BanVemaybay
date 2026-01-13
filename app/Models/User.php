<?php
require_once __DIR__ . '/../Helpers/helpers.php';

class User {
    public static function findByEmail(string $email): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT id, ten, email, mat_khau, vai_tro FROM nguoi_dung WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function create(string $name, string $email, string $password): bool {
        global $mysqli;
        $stmt = $mysqli->prepare('INSERT INTO nguoi_dung (ten,email,mat_khau,vai_tro) VALUES (?,?,?,"customer")');
        $stmt->bind_param('sss', $name, $email, $password);
        return $stmt->execute();
    }

    public static function findById(int $id): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT id, ten, email, vai_tro FROM nguoi_dung WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function getPassword(int $id): string {
        global $mysqli;
        $stmt = $mysqli->prepare('SELECT mat_khau FROM nguoi_dung WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['mat_khau'] ?? '';
    }

    public static function updateProfile(int $id, array $fields): bool {
        global $mysqli;
        if (!$fields) return true;
        $parts = [];
        $types = '';
        $values = [];
        foreach ($fields as $col => $val) {
            $parts[] = "$col = ?";
            $types .= 's';
            $values[] = $val;
        }
        $sql = 'UPDATE nguoi_dung SET ' . implode(', ', $parts) . ' WHERE id = ?';
        $types .= 'i';
        $values[] = $id;

        $stmt = $mysqli->prepare($sql);
        $bind = [$types];
        foreach ($values as $i => $v) {
            $bind[] = &$values[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind);
        return $stmt->execute();
    }

    public static function customersOrdered(): array {
        global $mysqli;
        $res = $mysqli->query("SELECT id, ten, email, mat_khau FROM nguoi_dung WHERE vai_tro='customer' ORDER BY id DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function findCustomer(int $id): ?array {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT id, ten, email, mat_khau FROM nguoi_dung WHERE id=? AND vai_tro='customer' LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    public static function createCustomer(string $name, string $email, string $password): bool {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO nguoi_dung (ten,email,mat_khau,vai_tro) VALUES (?,?,?,'customer')");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sss', $name, $email, $password);
        return $stmt->execute();
    }

    public static function updateCustomer(int $id, array $data): bool {
        global $mysqli;
        $stmt = $mysqli->prepare("UPDATE nguoi_dung SET ten=?, email=?, mat_khau=? WHERE id=? AND vai_tro='customer'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sssi', $data['ten'], $data['email'], $data['mat_khau'], $id);
        return $stmt->execute();
    }

    public static function deleteCustomer(int $id): bool {
        global $mysqli;
        $stmt = $mysqli->prepare("DELETE FROM nguoi_dung WHERE id=? AND vai_tro='customer'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public static function anonymizeAndDisable(int $id): bool {
        global $mysqli;
        // Keep the row to avoid FK cascade deleting bookings (dat_ve) and passengers.
        // Make the account effectively unusable by changing email and password.
        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $newName = 'Tài khoản đã xóa';
        $suffix = $id . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4));
        $newEmail = 'deleted_' . $suffix . '@example.invalid';
        $newPassword = bin2hex(random_bytes(16));

        $stmt = $mysqli->prepare("UPDATE nguoi_dung SET ten=?, email=?, mat_khau=? WHERE id=? AND vai_tro='customer'");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('sssi', $newName, $newEmail, $newPassword, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
