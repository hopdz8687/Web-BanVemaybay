<?php
require_once __DIR__ . '/../Helpers/helpers.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController {
    public function login(): void {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            if ($user && $password === $user['mat_khau']) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'ten' => $user['ten'],
                    'email' => $user['email'],
                    'vai_tro' => $user['vai_tro'],
                ];
                redirect('/');
            }
            $error = 'Invalid credentials';
        }
        view('auth/login', compact('error'));
    }

    public function register(): void {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            if (empty($name) || empty($email) || empty($password)) {
                $error = 'Fill all fields';
            } else {
                if (User::create($name, $email, $password)) {
                    redirect('/auth/login');
                } else {
                    $error = 'Registration failed';
                }
            }
        }
        view('auth/register', compact('error'));
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect('/auth/login');
    }

    public function changeProfile(): void {
        require_login();

        $user_id = $_SESSION['user']['id'];
        $error = '';
        $success_msg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $stored = User::getPassword($user_id);

            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ';
            }
            if (!$error && $new !== '') {
                if (empty($current)) {
                    $error = 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu';
                } elseif ($current !== $stored) {
                    $error = 'Mật khẩu hiện tại không đúng';
                } elseif ($new !== $confirm) {
                    $error = 'Mật khẩu mới và xác nhận không khớp';
                }
            }
            if (!$error && $email && $email !== $_SESSION['user']['email']) {
                if (empty($current)) {
                    $error = 'Vui lòng nhập mật khẩu hiện tại để đổi email';
                } elseif ($current !== $stored) {
                    $error = 'Mật khẩu hiện tại không đúng';
                }
            }

            if (!$error) {
                $fields = [];
                if ($name !== '' && $name !== $_SESSION['user']['ten']) {
                    $fields['ten'] = $name;
                }
                if ($email !== '' && $email !== $_SESSION['user']['email']) {
                    $fields['email'] = $email;
                }
                if ($new !== '') {
                    $fields['mat_khau'] = $new;
                }

                if ($fields) {
                    if (User::updateProfile($user_id, $fields)) {
                        $u = User::findById($user_id);
                        if ($u) {
                            $_SESSION['user'] = $u;
                        }
                        $success_msg = 'Cập nhật thông tin thành công';
                    } else {
                        $error = 'Cập nhật thất bại';
                    }
                } else {
                    $success_msg = 'Không có thay đổi nào';
                }
            }
        }

        view('auth/change_profile', compact('error', 'success_msg'));
    }
}
