<?php
require_once __DIR__ . '/../Helpers/helpers.php';
require_once __DIR__ . '/../Models/Flight.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Models/User.php';

class CustomerController {
    public function dashboard(): void {
        require_login();
        if (is_admin()) {
            redirect('/admin/dashboard');
        }
        $flights = Flight::recent(6);
        $search = [
            'noi_di' => (string)($_GET['noi_di'] ?? ''),
            'noi_den' => (string)($_GET['noi_den'] ?? ''),
            'ngay' => (string)($_GET['ngay'] ?? ''),
        ];
        view('customer/dashboard', compact('flights', 'search'));
    }

    public function search(): void {
        require_login();
        $noi_di = $_GET['noi_di'] ?? '';
        $noi_den = $_GET['noi_den'] ?? '';
        $ngay = $_GET['ngay'] ?? '';
        $flights = Flight::search($noi_di, $noi_den, $ngay);
        view('customer/search', compact('noi_di','noi_den','ngay','flights'));
    }

    public function book(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }
        $chuyen_bay_id = (int)($_GET['chuyen_bay_id'] ?? $_POST['chuyen_bay_id'] ?? 0);
        if (!$chuyen_bay_id) { redirect('/customer/dashboard'); }

        $flight = Flight::find($chuyen_bay_id);
        if (!$flight) { echo 'Khong tim thay chuyen bay'; exit; }

        $error = '';
        $step = $_POST['step'] ?? 'chon_ve';
        $selectedTicketId = (int)($_POST['ticket_id'] ?? 0);
        $selectedQty = (int)($_POST['so_luong'] ?? 1);
        $selectedTicket = null;
        $selectedPrice = 0.0;
        $selectedClass = 'Thuong';
        $total = 0.0;

        // Danh sach ve cua chuyen bay
        $tickets = Ticket::byFlightId($chuyen_bay_id);
        if (empty($tickets)) {
            // Khong cho dat neu admin chua tao ve cho chuyen bay
            $error = 'Chuyến bay chưa có loại vé. Vui lòng liên hệ admin để thêm vé trước khi đặt.';
            $step = 'chon_ve';
        }

        // Step 1: chon ve
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'chon_ve') {
            $selectedTicketId = (int)($_POST['ticket_id'] ?? 0);
            $selected = null;
            foreach ($tickets as $t) {
                if ((int)($t['id'] ?? 0) === $selectedTicketId) {
                    $selected = $t;
                    break;
                }
            }
            if (!$selected) {
                $error = 'Vui long chon ve';
            } else {
                $step = 'chon_so_luong';
            }
        }

        // Step 2: chon so luong
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'chon_so_luong') {
            $selectedTicketId = (int)($_POST['ticket_id'] ?? 0);
            $selectedQty = (int)($_POST['so_luong'] ?? 0);
            $selected = null;
            foreach ($tickets as $t) {
                if ((int)($t['id'] ?? 0) === $selectedTicketId) {
                    $selected = $t;
                    break;
                }
            }

            $max = (int)($selected['so_luong_con'] ?? 0);
            if ($max > (int)$flight['ghe_con']) {
                $max = (int)$flight['ghe_con'];
            }

            if (!$selected) {
                $error = 'Vui long chon ve';
                $step = 'chon_ve';
            } elseif ($selectedQty < 1 || $selectedQty > $max) {
                $error = 'So luong khong hop le';
            } else {
                $step = 'nhap_hanh_khach';
            }
        }

        // Step 3: nhap thong tin hanh khach + them vao gio hang
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'xac_nhan') {
            global $mysqli;
            $selectedTicketId = (int)($_POST['ticket_id'] ?? 0);
            $selectedQty = (int)($_POST['so_luong'] ?? 0);
            $ticket_id = $selectedTicketId;

            // Tim ve duoc chon
            $selected = null;
            foreach ($tickets as $t) {
                if ((int)($t['id'] ?? 0) === $selectedTicketId) {
                    $selected = $t;
                    break;
                }
            }
            if (!$selected) {
                $error = 'Vui long chon ve';
                $step = 'chon_ve';
            } else {
                $max = (int)($selected['so_luong_con'] ?? 0);
                if ($max > (int)$flight['ghe_con']) {
                    $max = (int)$flight['ghe_con'];
                }
                if ($selectedQty < 1 || $selectedQty > $max) {
                    $error = 'So luong khong hop le';
                    $step = 'chon_so_luong';
                }
            }

            // Nhap thong tin hanh khach theo so luong
            $hkTen = $_POST['hk_ten'] ?? [];
            $hkDienThoai = $_POST['hk_dien_thoai'] ?? [];
            $hkEmail = $_POST['hk_email'] ?? [];
            $hkGioiTinh = $_POST['hk_gioi_tinh'] ?? [];
            $hkTuoi = $_POST['hk_tuoi'] ?? [];

            if (!$error) {
                if (!is_array($hkTen) || !is_array($hkDienThoai) || !is_array($hkEmail) || !is_array($hkGioiTinh) || !is_array($hkTuoi)) {
                    $error = 'Du lieu hanh khach khong hop le';
                } elseif (count($hkTen) !== $selectedQty) {
                    $error = 'Vui long nhap thong tin hanh khach theo so luong da chon';
                } else {
                    for ($i = 0; $i < $selectedQty; $i++) {
                        $name = trim((string)($hkTen[$i] ?? ''));
                        $phone = trim((string)($hkDienThoai[$i] ?? ''));
                        $email = trim((string)($hkEmail[$i] ?? ''));
                        if ($name === '' || $phone === '' || $email === '') {
                            $error = 'Vui long nhap day du ho ten, dien thoai, email cho tat ca hanh khach';
                            break;
                        }
                    }
                }
            }

            if (!$error) {
                $ticketPrice = (float)($selected['gia'] ?? 0);
                $ticketType = $selected['hang_ve'] ?? 'Thuong';
                $total = $ticketPrice * $selectedQty;

                $mysqli->begin_transaction();
                $reserved = true;
                try {
                    if ($ticket_id > 0) {
                        $reserved = Ticket::reserve($ticket_id, $selectedQty);
                        if (!$reserved) {
                            throw new RuntimeException('Khong du so luong ve');
                        }
                    }

                    $booking_id = Booking::create(
                        $_SESSION['user']['id'],
                        $chuyen_bay_id,
                        $selectedQty,
                        $total,
                        $ticket_id > 0 ? $ticket_id : null,
                        'cart',
                        []
                    );
                    if (!$booking_id) {
                        throw new RuntimeException('Tao dat ve that bai');
                    }

                    $start_seat = Booking::maxSeatNumberForFlight($chuyen_bay_id) + 1;
                    for ($i = 0; $i < $selectedQty; $i++) {
                        $seat_number = $start_seat + $i;
                        $ok = Booking::addPassenger($booking_id, [
                            'name' => trim((string)($hkTen[$i] ?? '')),
                            'phone' => trim((string)($hkDienThoai[$i] ?? '')),
                            'email' => trim((string)($hkEmail[$i] ?? '')),
                            'gender' => (string)($hkGioiTinh[$i] ?? 'Nam'),
                            'age' => (int)($hkTuoi[$i] ?? 18),
                            'ticket_type' => $ticketType,
                            'ticket_price' => $ticketPrice,
                            'seat_number' => $seat_number,
                        ]);
                        if (!$ok) {
                            throw new RuntimeException('Luu hanh khach that bai');
                        }
                    }

                    if (!Flight::adjustSeats($chuyen_bay_id, -$selectedQty)) {
                        throw new RuntimeException('Cap nhat so ghe that bai');
                    }

                    $mysqli->commit();
                    redirect('/customer/cart');
                } catch (Throwable $e) {
                    $mysqli->rollback();
                    if ($ticket_id > 0 && $reserved) {
                        // Neu da tru ve ma rollback, cong lai
                        Ticket::release($ticket_id, $selectedQty);
                    }
                    $error = $e->getMessage() ?: 'Dat ve that bai';
                    $step = 'nhap_hanh_khach';
                }
            }
        }

        // Prepare selected ticket info for view (no $_POST usage in templates)
        foreach (($tickets ?? []) as $t) {
            if ((int)($t['id'] ?? 0) === $selectedTicketId) {
                $selectedTicket = $t;
                break;
            }
        }
        if ($selectedTicket) {
            $selectedPrice = (float)($selectedTicket['gia'] ?? 0);
            $selectedClass = (string)($selectedTicket['hang_ve'] ?? 'Thuong');
        } else {
            // Fallback to economy price if no ticket selected
            $selectedPrice = (float)($flight['gia_thuong'] ?? 0);
            $selectedClass = 'Thuong';
        }
        if ($selectedQty < 1) {
            $selectedQty = 1;
        }
        $total = $selectedPrice * $selectedQty;

        view('customer/book', [
            'flight' => $flight,
            'flight_id' => $chuyen_bay_id,
            'tickets' => $tickets,
            'error' => $error,
            'step' => $step,
            'selectedTicketId' => $selectedTicketId,
            'selectedQty' => $selectedQty,
            'selectedTicket' => $selectedTicket,
            'selectedPrice' => $selectedPrice,
            'selectedClass' => $selectedClass,
            'total' => $total,
        ]);
    }

    public function cart(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }

        $message = '';

        // Remove an item from cart
        if (isset($_GET['remove'])) {
            $bookingId = (int)$_GET['remove'];
            if ($bookingId) {
                $booking = Booking::findForUser($bookingId, $_SESSION['user']['id']);
                if ($booking && ($booking['trang_thai'] ?? '') === 'cart') {
                    Booking::deleteForUser($bookingId, $_SESSION['user']['id']);
                    Flight::adjustSeats((int)$booking['chuyen_bay_id'], (int)$booking['so_ghe_dat']);
                    if (!empty($booking['ve_id'])) {
                        Ticket::release((int)$booking['ve_id'], (int)$booking['so_ghe_dat']);
                    }
                    $message = 'Đã xóa khỏi giỏ hàng.';
                }
            }
        }

        $userId = $_SESSION['user']['id'];
        $cartBookings = Booking::listForUserByStatus($userId, 'cart');
        $cartGrandTotal = 0.0;
        foreach (($cartBookings ?? []) as $b) {
            $cartGrandTotal += (float)($b['tong_tien'] ?? 0);
        }
        view('customer/cart', compact('cartBookings', 'message', 'cartGrandTotal'));
    }

    public function editCart(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }

        $userId = $_SESSION['user']['id'];
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        if (!$bookingId) {
            redirect('/customer/cart');
        }

        $booking = Booking::findForUser($bookingId, $userId);
        if (!$booking || ($booking['trang_thai'] ?? '') !== 'cart') {
            redirect('/customer/cart');
        }

        $flight = Flight::find((int)$booking['chuyen_bay_id']);
        if (!$flight) {
            redirect('/customer/cart');
        }

        $ticket = null;
        $ticketPrice = 0.0;
        if (!empty($booking['ve_id'])) {
            $ticket = Ticket::find((int)$booking['ve_id']);
            $ticketPrice = (float)($ticket['gia'] ?? 0);
        }
        if (!$ticketPrice) {
            // fallback: tinh gia trung binh theo tong tien / so luong
            $ticketPrice = ((int)($booking['so_ghe_dat'] ?? 1) > 0) ? ((float)($booking['tong_tien'] ?? 0) / (int)($booking['so_ghe_dat'] ?? 1)) : 0;
        }

        $error = '';
        $soLuong = null;

        $currentQty = (int)($booking['so_ghe_dat'] ?? 0);
        $flightAvail = (int)($flight['ghe_con'] ?? 0) + $currentQty;
        $ticketAvail = $ticket ? ((int)($ticket['so_luong_con'] ?? 0) + $currentQty) : $flightAvail;
        $maxQty = min($flightAvail, $ticketAvail);
        if ($maxQty < 1) {
            $maxQty = 1;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $mysqli;
            $soLuong = (int)($_POST['so_luong'] ?? 0);

            if ($soLuong < 1 || $soLuong > $maxQty) {
                $error = 'Số lượng không hợp lệ';
            } else {
                $delta = $soLuong - $currentQty;
                $mysqli->begin_transaction();
                $reservedMore = false;
                try {
                    $existingPassengers = Booking::passengersForBooking($bookingId);
                    $template = $existingPassengers[0] ?? null;
                    $tplName = $template ? ($template['ten_hanh_khach'] ?? '') : ($_SESSION['user']['ten'] ?? 'Hanh khach');
                    $tplPhone = $template ? ($template['dien_thoai'] ?? '') : '';
                    $tplEmail = $template ? ($template['email_hanh_khach'] ?? '') : ($_SESSION['user']['email'] ?? '');
                    $tplGender = $template ? ($template['gioi_tinh'] ?? 'Nam') : 'Nam';
                    $tplAge = $template ? (int)($template['tuoi'] ?? 18) : 18;
                    $tplLoaiVe = $template ? ($template['loai_ve'] ?? 'Thuong') : 'Thuong';

                    if ($delta > 0) {
                        if (!empty($booking['ve_id'])) {
                            $reservedMore = Ticket::reserve((int)$booking['ve_id'], $delta);
                            if (!$reservedMore) {
                                throw new RuntimeException('Không đủ số lượng vé');
                            }
                        }
                        if (!Flight::adjustSeats((int)$booking['chuyen_bay_id'], -$delta)) {
                            throw new RuntimeException('Cập nhật ghế thất bại');
                        }

                        // them hanh khach (nhan ban theo hanh khach dau tien)
                        $start = Booking::maxSeatNumberForFlight((int)$booking['chuyen_bay_id']) + 1;
                        for ($i = 0; $i < $delta; $i++) {
                            $ok = Booking::addPassenger($bookingId, [
                                'name' => $tplName,
                                'phone' => $tplPhone,
                                'email' => $tplEmail,
                                'gender' => $tplGender,
                                'age' => $tplAge,
                                'ticket_type' => $tplLoaiVe,
                                'ticket_price' => $ticketPrice,
                                'seat_number' => $start + $i,
                            ]);
                            if (!$ok) {
                                throw new RuntimeException('Lưu hành khách thất bại');
                            }
                        }
                    } elseif ($delta < 0) {
                        $reduce = -$delta;
                        // xoa bot hanh khach
                        if (!Booking::removeLastPassengers($bookingId, $reduce)) {
                            throw new RuntimeException('Cập nhật hành khách thất bại');
                        }
                        if (!Flight::adjustSeats((int)$booking['chuyen_bay_id'], $reduce)) {
                            throw new RuntimeException('Cập nhật ghế thất bại');
                        }
                        if (!empty($booking['ve_id'])) {
                            Ticket::release((int)$booking['ve_id'], $reduce);
                        }
                    }

                    $newTotal = (float)$ticketPrice * $soLuong;
                    $ok = Booking::updateCartBooking($bookingId, $userId, $soLuong, $newTotal, []);
                    if (!$ok) {
                        throw new RuntimeException('Lưu đơn thất bại');
                    }

                    $mysqli->commit();
                    redirect('/customer/cart');
                } catch (Throwable $e) {
                    $mysqli->rollback();
                    if (!empty($booking['ve_id']) && $delta > 0 && $reservedMore) {
                        Ticket::release((int)$booking['ve_id'], $delta);
                    }
                    $error = $e->getMessage() ?: 'Cập nhật thất bại';
                }
            }
        }

        view('customer/cart_edit', compact('booking', 'flight', 'ticket', 'maxQty', 'error', 'soLuong'));
    }

    public function deleteAccount(): void {
        require_login();
        if (is_admin()) {
            redirect('/admin/dashboard');
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirm = $_POST['confirm'] ?? '';
            if ($confirm !== 'yes') {
                $error = 'Vui lòng xác nhận trước khi xóa tài khoản.';
            } else {
                $userId = (int)($_SESSION['user']['id'] ?? 0);
                if ($userId <= 0) {
                    redirect('/auth/login');
                }

                // NOTE: Do not hard-delete user because DB FK would cascade-delete bookings,
                // which would break revenue history. We anonymize/disable instead.
                if (User::anonymizeAndDisable($userId)) {
                    $_SESSION = [];
                    if (ini_get('session.use_cookies')) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                    }
                    session_destroy();
                    redirect('/auth/login');
                }
                $error = 'Xóa tài khoản thất bại. Vui lòng thử lại.';
            }
        }

        view('customer/delete_account', compact('error'));
    }

    public function checkout(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }

        $userId = $_SESSION['user']['id'];

        // POST from cart: select bookings to checkout
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['step'] ?? '') === 'select')) {
            $selected = $_POST['booking_ids'] ?? [];
            $selected = is_array($selected) ? $selected : [$selected];
            $selected = array_values(array_filter(array_map('intval', $selected), static fn($v) => $v > 0));
            if (!$selected) {
                redirect('/customer/cart');
            }
            $_SESSION['checkout_booking_ids'] = $selected;
            redirect('/customer/checkout');
        }

        // POST from checkout form: pay
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['step'] ?? '') === 'pay')) {
            $ids = $_SESSION['checkout_booking_ids'] ?? [];
            $ids = is_array($ids) ? $ids : [];
            $ids = array_values(array_filter(array_map('intval', $ids), static fn($v) => $v > 0));
            if (!$ids) {
                redirect('/customer/cart');
            }

            $payName = trim($_POST['ten_thanh_toan'] ?? '');
            $payPhone = trim($_POST['dien_thoai_thanh_toan'] ?? '');
            $payEmail = trim($_POST['email_thanh_toan'] ?? '');
            $payAddress = trim($_POST['dia_chi_thanh_toan'] ?? '');
            $paymentMethod = $_POST['phuong_thuc_thanh_toan'] ?? 'direct';
            $allowedMethods = ['direct','atm','momo'];
            if (!in_array($paymentMethod, $allowedMethods, true)) {
                $paymentMethod = 'direct';
            }
            if ($payName === '' || $payPhone === '' || $payEmail === '') {
                $error = 'Vui lòng nhập đầy đủ thông tin thanh toán';
                $bookings = Booking::listForUserByIdsAndStatus($userId, $ids, 'cart');
                $grandTotal = 0.0;
                foreach (($bookings ?? []) as $b) {
                    $grandTotal += (float)($b['tong_tien'] ?? 0);
                }
                view('customer/checkout', compact('bookings', 'grandTotal', 'error', 'payName', 'payPhone', 'payEmail', 'payAddress', 'paymentMethod'));
                return;
            }

            Booking::paySelectedWithPayment($userId, $ids, [
                'ten_thanh_toan' => $payName,
                'email_thanh_toan' => $payEmail,
                'dien_thoai_thanh_toan' => $payPhone,
                'dia_chi_thanh_toan' => $payAddress,
                'phuong_thuc_thanh_toan' => $paymentMethod,
            ]);
            unset($_SESSION['checkout_booking_ids']);
            redirect('/customer/my-tickets');
        }

        // GET: show payment info form
        $ids = $_SESSION['checkout_booking_ids'] ?? [];
        $ids = is_array($ids) ? $ids : [];
        $ids = array_values(array_filter(array_map('intval', $ids), static fn($v) => $v > 0));
        if (!$ids) {
            redirect('/customer/cart');
        }
        $bookings = Booking::listForUserByIdsAndStatus($userId, $ids, 'cart');
        if (!$bookings) {
            redirect('/customer/cart');
        }
        $grandTotal = 0.0;
        foreach (($bookings ?? []) as $b) {
            $grandTotal += (float)($b['tong_tien'] ?? 0);
        }
        $error = '';
        $payName = '';
        $payPhone = '';
        $payEmail = '';
        $payAddress = '';
        $paymentMethod = 'direct';
        view('customer/checkout', compact('bookings', 'grandTotal', 'error', 'payName', 'payPhone', 'payEmail', 'payAddress', 'paymentMethod'));
    }

    public function myTickets(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }
        // Handle delete
        if (isset($_GET['delete_booking'])) {
            $booking_id = (int)$_GET['delete_booking'];
            $booking_to_delete = Booking::findForUser($booking_id, $_SESSION['user']['id']);
            if ($booking_to_delete) {
                Booking::deleteForUser($booking_id, $_SESSION['user']['id']);
                Flight::adjustSeats((int)$booking_to_delete['chuyen_bay_id'], (int)$booking_to_delete['so_ghe_dat']);
                if (!empty($booking_to_delete['ve_id'])) {
                    Ticket::release((int)$booking_to_delete['ve_id'], (int)$booking_to_delete['so_ghe_dat']);
                }
                redirect('/customer/my-tickets');
            }
        }

        $user_id = $_SESSION['user']['id'];
        $bookings = Booking::listForUserByStatus($user_id, 'paid');
        $bookingIds = array_column($bookings, 'id');
        $passengersByBooking = Booking::passengersByBookingIds($bookingIds);

        if (isset($_GET['export'])) {
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="my_tickets_' . date('Ymd_His') . '.xls"');
            echo "\xEF\xBB\xBF";
            echo "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>";
            echo '<table border="1" cellspacing="0" cellpadding="5">';

            $headers = [
                'Ma dat ve','Chuyen bay','Noi di','Noi den','Gio khoi hanh','Hang bay','May bay','Ngay dat',
                'So ghe','Tong tien','Trang thai thanh toan','Ghe','Hanh khach','Dien thoai','Email','Gioi tinh','Do tuoi','Loai ve','Gia ve'
            ];
            echo '<tr>';
            foreach ($headers as $h) {
                echo '<th>' . htmlspecialchars($h) . '</th>';
            }
            echo '</tr>';

            foreach ($bookings as $booking) {
                $dep = !empty($booking['gio_khoi_hanh']) ? date('Y-m-d H:i:s', strtotime($booking['gio_khoi_hanh'])) : '';
                $bk = !empty($booking['dat_luc']) ? date('Y-m-d H:i:s', strtotime($booking['dat_luc'])) : '';
                $airline = $booking['hang_may_bay'] ?? '';
                $plane = trim((string)($booking['ma_may_bay'] ?? '') . (!empty($booking['ten_may_bay']) ? (' - ' . ($booking['ten_may_bay'] ?? '')) : ''));
                $payStatus = (($booking['trang_thai'] ?? '') === 'paid') ? 'Da thanh toan' : (string)($booking['trang_thai'] ?? '');
                $passengers = $passengersByBooking[$booking['id']] ?? [];
                if ($passengers) {
                    foreach ($passengers as $p) {
                        $row = [
                            $booking['id'] ?? '', $booking['so_hieu'] ?? '', $booking['noi_di'] ?? '', $booking['noi_den'] ?? '',
                            $dep, $airline, $plane, $bk,
                            $booking['so_ghe_dat'] ?? '', $booking['tong_tien'] ?? '', $payStatus,
                            $p['so_ghe'] ?? '', $p['ten_hanh_khach'] ?? '', $p['dien_thoai'] ?? '', $p['email_hanh_khach'] ?? '',
                            $p['gioi_tinh'] ?? '', $p['tuoi'] ?? '', $p['loai_ve'] ?? '', $p['gia_ve'] ?? ''
                        ];
                        echo '<tr>';
                        foreach ($row as $cell) {
                            echo '<td>' . htmlspecialchars((string)$cell) . '</td>';
                        }
                        echo '</tr>';
                    }
                } else {
                    $row = [
                        $booking['id'] ?? '', $booking['so_hieu'] ?? '', $booking['noi_di'] ?? '', $booking['noi_den'] ?? '',
                        $dep, $airline, $plane, $bk,
                        $booking['so_ghe_dat'] ?? '', $booking['tong_tien'] ?? '', $payStatus
                    ];
                    echo '<tr>';
                    foreach ($row as $cell) {
                        echo '<td>' . htmlspecialchars((string)$cell) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</table></body></html>';
            exit;
        }

        view('customer/my_tickets', ['bookings' => $bookings, 'passengersByBooking' => $passengersByBooking]);
    }

    public function editTicket(): void {
        require_login();
        if (is_admin()) { redirect('/admin/dashboard'); }

        $bookingId = (int)($_GET['booking_id'] ?? 0);
        if (!$bookingId) { redirect('/customer/my-tickets'); }

        $booking = Booking::findForUser($bookingId, $_SESSION['user']['id']);
        if (!$booking) {
            http_response_code(404);
            echo 'Khong tim thay dat ve';
            return;
        }

        if (!empty($booking['da_xac_nhan'])) {
            redirect('/customer/my-tickets');
        }

        $flight = Flight::find((int)$booking['chuyen_bay_id']);
        if (!$flight) {
            http_response_code(404);
            echo 'Khong tim thay chuyen bay';
            return;
        }

        $passengers = Booking::passengersForBooking($bookingId);
        $passengersForForm = array_values($passengers); // already ordered by seat_number
        $existingSeats = array_map(static fn($p) => (int)$p['so_ghe'], $passengers);
        sort($existingSeats, SORT_NUMERIC);

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seatsBooked = (int)$booking['so_ghe_dat'];
            for ($i = 1; $i <= $seatsBooked; $i++) {
                $name = trim($_POST['ten_hanh_khach_' . $i] ?? '');
                $phone = trim($_POST['dien_thoai_' . $i] ?? '');
                $email = trim($_POST['email_hanh_khach_' . $i] ?? '');
                $gender = $_POST['gioi_tinh_' . $i] ?? 'Nam';
                $age = (int)($_POST['tuoi_' . $i] ?? 18);

                if ($name === '' || $phone === '' || $email === '') {
                    $error = 'Vui long nhap day du thong tin hanh khach';
                    break;
                }

                $payload[] = [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'age' => $age,
                    'seat_number' => $existingSeats[$i - 1] ?? $i,
                ];
            }

            if (!$error) {
                if (Booking::updatePassengersInfoBySeat($bookingId, $payload)) {
                    redirect('/customer/my-tickets');
                } else {
                    $error = 'Cap nhat that bai. Vui long thu lai.';
                }
            }
        }

        view('customer/edit_ticket', [
            'booking' => $booking,
            'flight' => $flight,
            'passengersForForm' => $passengersForForm,
            'existingSeats' => $existingSeats,
            'error' => $error,
        ]);
    }
}
