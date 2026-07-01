<?php

require_once __DIR__ . '/../../../Models/Flight.php';
require_once __DIR__ . '/../../../Models/Plane.php';

class ApiV1FlightController
{
    private static function mapFlightRow(array $row): array
    {
        // Models sometimes return joined plane fields; normalize them into may_bay.
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

    public function index(): void
    {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $rows = Flight::allOrdered();
        $items = array_map([self::class, 'mapFlightRow'], $rows);

        if ($limit > 0) {
            $items = array_slice($items, max(0, $offset), $limit);
        } elseif ($offset > 0) {
            $items = array_slice($items, max(0, $offset));
        }

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach chuyen bay',
        ]);
    }

    public function search(): void
    {
        $noiDi = trim((string)($_GET['noi_di'] ?? ''));
        $noiDen = trim((string)($_GET['noi_den'] ?? ''));
        $ngay = trim((string)($_GET['ngay'] ?? ''));

        if ($ngay !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngay)) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => [
                    'ngay' => ['Ngay phai theo dinh dang YYYY-MM-DD'],
                ],
            ], 422);
        }

        $rows = Flight::search($noiDi, $noiDen, $ngay);
        $items = array_map([self::class, 'mapFlightRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Ket qua tim kiem',
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

        // Add plane info if available (detail query does not join by default).
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
}
