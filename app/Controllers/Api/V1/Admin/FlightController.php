<?php

require_once __DIR__ . '/../../../../Models/Flight.php';
require_once __DIR__ . '/../../../../Models/Plane.php';

class ApiV1AdminFlightController
{
    private static function mapFlightRow(array $row): array
    {
        $mayBay = null;
        if (isset($row['ma_may_bay']) || isset($row['ten_may_bay']) || isset($row['hang_may_bay'])) {
            $mayBay = [
                'ma_may_bay' => $row['ma_may_bay'] ?? null,
                'ten_may_bay' => $row['ten_may_bay'] ?? null,
                'hang_may_bay' => $row['hang_may_bay'] ?? null,
            ];
        }

        return [
            'id' => (int)($row['id'] ?? 0),
            'so_hieu' => $row['so_hieu'] ?? null,
            'noi_di' => $row['noi_di'] ?? null,
            'noi_den' => $row['noi_den'] ?? null,
            'gio_khoi_hanh' => $row['gio_khoi_hanh'] ?? null,
            'gio_ha_canh' => $row['gio_ha_canh'] ?? null,
            'gia_thuong' => isset($row['gia_thuong']) ? (float)$row['gia_thuong'] : null,
            'gia_thuong_gia' => isset($row['gia_thuong_gia']) ? (float)$row['gia_thuong_gia'] : null,
            'ghe_con' => isset($row['ghe_con']) ? (int)$row['ghe_con'] : null,
            'may_bay_id' => isset($row['may_bay_id']) ? (int)$row['may_bay_id'] : null,
            'may_bay' => $mayBay,
        ];
    }

    private static function validate(array $data, bool $isCreate): array
    {
        $errors = [];

        $soHieu = trim((string)($data['so_hieu'] ?? ''));
        $noiDi = trim((string)($data['noi_di'] ?? ''));
        $noiDen = trim((string)($data['noi_den'] ?? ''));
        $gioKhoiHanh = trim((string)($data['gio_khoi_hanh'] ?? ''));
        $gioHaCanh = trim((string)($data['gio_ha_canh'] ?? ''));

        if ($isCreate || array_key_exists('so_hieu', $data)) {
            if ($soHieu === '') {
                $errors['so_hieu'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('noi_di', $data)) {
            if ($noiDi === '') {
                $errors['noi_di'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('noi_den', $data)) {
            if ($noiDen === '') {
                $errors['noi_den'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('gio_khoi_hanh', $data)) {
            if ($gioKhoiHanh === '') {
                $errors['gio_khoi_hanh'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('gio_ha_canh', $data)) {
            if ($gioHaCanh === '') {
                $errors['gio_ha_canh'][] = 'Khong duoc bo trong';
            }
        }

        return $errors;
    }

    public function index(): void
    {
        $soHieu = trim((string)($_GET['so_hieu'] ?? ''));
        $rows = Flight::allOrdered($soHieu);
        $items = array_map([self::class, 'mapFlightRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach chuyen bay',
        ]);
    }

    public function show(int $id): void
    {
        $row = Flight::find($id);
        if (!$row) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay chuyen bay',
            ], 404);
        }

        $flight = self::mapFlightRow($row);
        $mayBayId = $flight['may_bay_id'] ?? null;
        if ($mayBayId) {
            $plane = Plane::find((int)$mayBayId);
            if ($plane) {
                $flight['may_bay'] = [
                    'id' => (int)$plane['id'],
                    'ma_may_bay' => $plane['ma_may_bay'] ?? null,
                    'ten_may_bay' => $plane['ten_may_bay'] ?? null,
                    'hang_may_bay' => $plane['hang_may_bay'] ?? null,
                ];
            }
        }

        json_response([
            'ok' => true,
            'data' => $flight,
            'message' => 'Chi tiet chuyen bay',
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

        $payload = [
            'so_hieu' => (string)($data['so_hieu'] ?? ''),
            'noi_di' => (string)($data['noi_di'] ?? ''),
            'noi_den' => (string)($data['noi_den'] ?? ''),
            'gio_khoi_hanh' => (string)($data['gio_khoi_hanh'] ?? ''),
            'gio_ha_canh' => (string)($data['gio_ha_canh'] ?? ''),
            'gia_thuong' => (float)($data['gia_thuong'] ?? 0),
            'gia_thuong_gia' => (float)($data['gia_thuong_gia'] ?? 0),
            'ghe_con' => (int)($data['ghe_con'] ?? 0),
            'may_bay_id' => (int)($data['may_bay_id'] ?? 0),
        ];

        // Nếu may_bay_id > 0 thì kiểm tra xem máy bay có tồn tại không
        if (!empty($payload['may_bay_id']) && $payload['may_bay_id'] > 0) {
            $plane = Plane::find((int)$payload['may_bay_id']);
            if (!$plane) {
                json_response([
                    'ok' => false,
                    'message' => 'may_bay_id khong ton tai',
                ], 422);
            }
        }

        $ok = Flight::create($payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the tao chuyen bay',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da tao chuyen bay',
        ], 201);
    }

    public function update(int $id): void
    {
        $existing = Flight::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay chuyen bay',
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
            'so_hieu' => array_key_exists('so_hieu', $data) ? (string)$data['so_hieu'] : (string)($existing['so_hieu'] ?? ''),
            'noi_di' => array_key_exists('noi_di', $data) ? (string)$data['noi_di'] : (string)($existing['noi_di'] ?? ''),
            'noi_den' => array_key_exists('noi_den', $data) ? (string)$data['noi_den'] : (string)($existing['noi_den'] ?? ''),
            'gio_khoi_hanh' => array_key_exists('gio_khoi_hanh', $data) ? (string)$data['gio_khoi_hanh'] : (string)($existing['gio_khoi_hanh'] ?? ''),
            'gio_ha_canh' => array_key_exists('gio_ha_canh', $data) ? (string)$data['gio_ha_canh'] : (string)($existing['gio_ha_canh'] ?? ''),
            'gia_thuong' => array_key_exists('gia_thuong', $data) ? (float)$data['gia_thuong'] : (float)($existing['gia_thuong'] ?? 0),
            'gia_thuong_gia' => array_key_exists('gia_thuong_gia', $data) ? (float)$data['gia_thuong_gia'] : (float)($existing['gia_thuong_gia'] ?? 0),
            'ghe_con' => array_key_exists('ghe_con', $data) ? (int)$data['ghe_con'] : (int)($existing['ghe_con'] ?? 0),
            'may_bay_id' => array_key_exists('may_bay_id', $data) ? (int)$data['may_bay_id'] : (int)($existing['may_bay_id'] ?? 0),
        ];

        // Nếu may_bay_id > 0 thì kiểm tra xem máy bay có tồn tại không
        if (!empty($payload['may_bay_id']) && $payload['may_bay_id'] > 0) {
            $plane = Plane::find((int)$payload['may_bay_id']);
            if (!$plane) {
                json_response([
                    'ok' => false,
                    'message' => 'may_bay_id khong ton tai',
                ], 422);
            }
        }

        $ok = Flight::update($id, $payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the cap nhat chuyen bay',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da cap nhat chuyen bay',
        ]);
    }

    public function destroy(int $id): void
    {
        $existing = Flight::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay chuyen bay',
            ], 404);
        }

        $ok = Flight::delete($id);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the xoa chuyen bay',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da xoa chuyen bay',
        ]);
    }
}
