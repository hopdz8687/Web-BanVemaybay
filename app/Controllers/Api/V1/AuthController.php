<?php

require_once __DIR__ . '/../../../Models/User.php';

class ApiV1AuthController
{
    private function input(): array
    {
        $data = json_input();
        if (!$data && !empty($_POST)) {
            $data = $_POST;
        }
        return is_array($data) ? $data : [];
    }

    public function login(): void
    {
        $data = $this->input();
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($email === '' || $password === '') {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => [
                    'email' => $email === '' ? ['Khong duoc bo trong'] : [],
                    'password' => $password === '' ? ['Khong duoc bo trong'] : [],
                ],
            ], 422);
        }

        $user = User::findByEmail($email);
        if (!$user || $password !== ($user['mat_khau'] ?? '')) {
            json_response([
                'ok' => false,
                'message' => 'Sai thong tin dang nhap',
            ], 401);
        }

        $token = jwt_encode([
            'sub' => (int)$user['id'],
            'role' => $user['vai_tro'] ?? 'customer',
            'email' => $user['email'] ?? null,
        ]);

        json_response([
            'ok' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => (int)$user['id'],
                    'ten' => $user['ten'] ?? null,
                    'email' => $user['email'] ?? null,
                    'vai_tro' => $user['vai_tro'] ?? null,
                ],
            ],
            'message' => 'Dang nhap thanh cong',
        ]);
    }

    public function register(): void
    {
        $data = $this->input();
        $name = trim((string)($data['ten'] ?? $data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        $errors = [];
        if ($name === '') {
            $errors['ten'][] = 'Khong duoc bo trong';
        }
        if ($email === '') {
            $errors['email'][] = 'Khong duoc bo trong';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email khong hop le';
        }
        if ($password === '') {
            $errors['password'][] = 'Khong duoc bo trong';
        }

        if ($errors) {
            json_response([
                'ok' => false,
                'message' => 'Du lieu khong hop le',
                'errors' => $errors,
            ], 422);
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            json_response([
                'ok' => false,
                'message' => 'Email da ton tai',
            ], 409);
        }

        $ok = User::create($name, $email, $password);
        if (!$ok) {
            json_response([
                'ok' => false,
                'message' => 'Khong the dang ky',
            ], 500);
        }

        json_response([
            'ok' => true,
            'data' => [
                'ten' => $name,
                'email' => $email,
            ],
            'message' => 'Dang ky thanh cong',
        ], 201);
    }

    public function me(): void
    {
        $user = api_require_auth();
        json_response([
            'ok' => true,
            'data' => $user,
            'message' => 'Thong tin nguoi dung',
        ]);
    }

    public function logout(): void
    {
        json_response([
            'ok' => true,
            'message' => 'Da dang xuat',
        ]);
    }
}
