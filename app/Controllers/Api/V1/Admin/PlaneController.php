<?php

require_once __DIR__ . '/../../../../Models/Plane.php';

class ApiV1AdminPlaneController
{
    private static function mapPlaneRow(array $row): array
    {
        return [
            'id' => (int)($row['id'] ?? 0),
            'ma_may_bay' => $row['ma_may_bay'] ?? null,
            'ten_may_bay' => $row['ten_may_bay'] ?? null,
            'hang_may_bay' => $row['hang_may_bay'] ?? null,
        ];
    }

    private static function validate(array $data, bool $isCreate): array
    {
        $errors = [];

        $ma = trim((string)($data['ma_may_bay'] ?? ''));
        $ten = trim((string)($data['ten_may_bay'] ?? ''));
        $hang = trim((string)($data['hang_may_bay'] ?? ''));

        if ($isCreate || array_key_exists('ma_may_bay', $data)) {
            if ($ma === '') {
                $errors['ma_may_bay'][] = 'Khong duoc bo trong';
            }
        }

        if ($isCreate || array_key_exists('ten_may_bay', $data)) {
            if ($ten === '') {
                $errors['ten_may_bay'][] = 'Khong duoc bo trong';
            }
        }

        // hang_may_bay is optional; model defaults to 'Khac' when missing.
        if (array_key_exists('hang_may_bay', $data) && $hang === '') {
            // allow empty string to mean default
        }

        return $errors;
    }

    public function index(): void
    {
        $ma = trim((string)($_GET['ma_may_bay'] ?? ''));
        $rows = Plane::allOrdered($ma);
        $items = array_map([self::class, 'mapPlaneRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach may bay',
        ]);
    }

    public function show(int $id): void
    {
        $row = Plane::find($id);
        if (!$row) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay may bay',
            ], 404);
        }

        json_response([
            'ok' => true,
            'data' => self::mapPlaneRow($row),
            'message' => 'Chi tiet may bay',
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

        $ok = Plane::create([
            'ma_may_bay' => (string)($data['ma_may_bay'] ?? ''),
            'ten_may_bay' => (string)($data['ten_may_bay'] ?? ''),
            'hang_may_bay' => (string)($data['hang_may_bay'] ?? ''),
        ]);

        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the tao may bay',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da tao may bay',
        ], 201);
    }

    public function update(int $id): void
    {
        $existing = Plane::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay may bay',
            ], 404);
        }

        $data = json_input();
        $errors = self::validate($data, false);
        if (!empty($errors)) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $errors,
            ], 422);
        }

        $payload = [
            'ma_may_bay' => array_key_exists('ma_may_bay', $data) ? (string)$data['ma_may_bay'] : (string)($existing['ma_may_bay'] ?? ''),
            'ten_may_bay' => array_key_exists('ten_may_bay', $data) ? (string)$data['ten_may_bay'] : (string)($existing['ten_may_bay'] ?? ''),
            'hang_may_bay' => array_key_exists('hang_may_bay', $data) ? (string)$data['hang_may_bay'] : (string)($existing['hang_may_bay'] ?? ''),
        ];

        $ok = Plane::update($id, $payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the cap nhat may bay',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da cap nhat may bay',
        ]);
    }

    public function destroy(int $id): void
    {
        $existing = Plane::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay may bay',
            ], 404);
        }

        $ok = Plane::delete($id);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the xoa may bay',
            ], 500);
        }

        // 204 is also fine; keeping 200 with message for easier demo.
        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da xoa may bay',
        ]);
    }
}
