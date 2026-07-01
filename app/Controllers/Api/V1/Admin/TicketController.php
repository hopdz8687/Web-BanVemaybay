<?php

require_once __DIR__ . '/../../../../Models/Ticket.php';

class ApiV1AdminTicketController
{
    private static function mapTicketRow(array $row): array
    {
        return [
            'id' => (int)($row['id'] ?? 0),
            'ma_ve' => $row['ma_ve'] ?? null,
            'chuyen_bay_id' => isset($row['chuyen_bay_id']) ? (int)$row['chuyen_bay_id'] : null,
            'ma_chuyen_bay' => $row['ma_chuyen_bay'] ?? null,
            'hang_ve' => $row['hang_ve'] ?? null,
            'gia' => isset($row['gia']) ? (float)$row['gia'] : null,
            'so_luong_con' => isset($row['so_luong_con']) ? (int)$row['so_luong_con'] : null,
        ];
    }

    private static function validate(array $data, bool $isCreate): array
    {
        $errors = [];

        $maVe = trim((string)($data['ma_ve'] ?? ''));
        $hangVe = trim((string)($data['hang_ve'] ?? ''));

        if ($isCreate || array_key_exists('ma_ve', $data)) {
            if ($maVe === '') {
                $errors['ma_ve'][] = 'Khong duoc bo trong';
            }
        }
        if ($isCreate || array_key_exists('hang_ve', $data)) {
            if ($hangVe === '') {
                $errors['hang_ve'][] = 'Khong duoc bo trong';
            }
        }

        return $errors;
    }

    public function listByFlight(int $flightId): void
    {
        $rows = Ticket::byFlightId($flightId);
        $items = array_map([self::class, 'mapTicketRow'], $rows);

        json_response([
            'ok' => true,
            'data' => $items,
            'message' => 'Danh sach ve',
        ]);
    }

    public function show(int $id): void
    {
        $row = Ticket::find($id);
        if (!$row) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
            ], 404);
        }

        json_response([
            'ok' => true,
            'data' => self::mapTicketRow($row),
            'message' => 'Chi tiet ve',
        ]);
    }

    public function store(int $flightId): void
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
            'ma_ve' => (string)($data['ma_ve'] ?? ''),
            'chuyen_bay_id' => $flightId,
            'hang_ve' => (string)($data['hang_ve'] ?? ''),
            'gia' => (float)($data['gia'] ?? 0),
            'so_luong_con' => (int)($data['so_luong_con'] ?? 0),
        ];

        $ok = Ticket::create($payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the tao ve',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da tao ve',
        ], 201);
    }

    public function update(int $id): void
    {
        $existing = Ticket::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
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
            'ma_ve' => array_key_exists('ma_ve', $data) ? (string)$data['ma_ve'] : (string)($existing['ma_ve'] ?? ''),
            'hang_ve' => array_key_exists('hang_ve', $data) ? (string)$data['hang_ve'] : (string)($existing['hang_ve'] ?? ''),
            'gia' => array_key_exists('gia', $data) ? (float)$data['gia'] : (float)($existing['gia'] ?? 0),
            'so_luong_con' => array_key_exists('so_luong_con', $data) ? (int)$data['so_luong_con'] : (int)($existing['so_luong_con'] ?? 0),
        ];

        $ok = Ticket::update($id, $payload);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the cap nhat ve',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da cap nhat ve',
        ]);
    }

    public function destroy(int $id): void
    {
        $existing = Ticket::find($id);
        if (!$existing) {
            json_response([
                'ok' => false,
                'message' => 'Khong tim thay ve',
            ], 404);
        }

        $ok = Ticket::delete($id);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the xoa ve',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => null,
            'message' => 'Da xoa ve',
        ]);
    }
}
