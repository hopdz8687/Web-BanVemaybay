<?php

require_once __DIR__ . '/../../../../Models/User.php';

class ApiV1AdminCustomerController
{
    private static function mapCustomerRow(array $row): array
    {
        return [
            'id' => (int)($row['id'] ?? 0),
            'ten' => $row['ten'] ?? null,
            'email' => $row['email'] ?? null,
        ];
    }

    private static function validate(array $data, bool $isCreate): array
    {
        $errors = [];
        $ten = trim((string)($data['ten'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $matKhau = trim((string)($data['mat_khau'] ?? ''));

        if ($isCreate || array_key_exists('ten', $data)) {
            if ($ten === '') {
                $errors['ten'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('email', $data)) {
            if ($email === '') {
                $errors['email'][] = 'Khong duoc bo trong';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'][] = 'Email khong hop le';
            }
        }
        if ($isCreate || array_key_exists('mat_khau', $data)) {
            if ($matKhau === '') {
                $errors['mat_khau'][] = 'Khong duoc bo trong';
            }
        }

        return $errors;
    }

    public function index(): void
    {
        $rows = User::customersOrdered();
        $items = array_map([self::class, 'mapCustomerRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach khach hang',
        ]);
    }

    public function show(int $id): void
    {
        $row = User::findCustomer($id);
        if (!$row) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay khach hang',
            ], 404);
        }

        json_response([
            'ok' => true,
            'data' => self::mapCustomerRow($row),
            'message' => 'Chi tiet khach hang',
        ]);
    }

    public function store(): void
    {
        $data = json_input();
        $errors = self::validate($data, true);
        if (!empty($errors)) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $errors,
            ], 422);
        }

        $email = trim((string)($data['email'] ?? ''));
        if (User::findByEmail($email)) {
            json_response([
                'ok' => false,
                'message' => 'Email da ton tai',
            ], 409);
        }

        $ok = User::createCustomer(
            trim((string)($data['ten'] ?? '')),
            $email,
            trim((string)($data['mat_khau'] ?? ''))
        );

        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the tao khach hang',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da tao khach hang',
        ], 201);
    }

    public function update(int $id): void
    {
        $existing = User::findCustomer($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay khach hang',
            ], 404);
        }

        $data = json_input();
        $errors = self::validate($data, true);
        if (!empty($errors)) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $errors,
            ], 422);
        }

        $email = trim((string)($data['email'] ?? ''));
        $check = User::findByEmail($email);
        if ($check && (int)$check['id'] !== $id) {
            json_response([
                'ok' => false,
                'message' => 'Email da ton tai',
            ], 409);
        }

        $ok = User::updateCustomer($id, [
            'ten' => trim((string)($data['ten'] ?? '')),
            'email' => $email,
            'mat_khau' => trim((string)($data['mat_khau'] ?? '')),
        ]);

        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the cap nhat khach hang',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da cap nhat khach hang',
        ]);
    }

    public function destroy(int $id): void
    {
        $existing = User::findCustomer($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay khach hang',
            ], 404);
        }

        $ok = User::deleteCustomer($id);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the xoa khach hang',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da xoa khach hang',
        ]);
    }
}
