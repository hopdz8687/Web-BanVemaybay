<?php
require_once __DIR__ . '/../Helpers/helpers.php';
require_once __DIR__ . '/../Models/Flight.php';
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/Plane.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Ticket.php';

class AdminController {
    private function toDatetimeLocalString($value): string {
      if (empty($value)) {
        return '';
      }
      $ts = strtotime((string)$value);
      if ($ts === false) {
        return '';
      }
      return date('Y-m-d\\TH:i', $ts);
    }

    public function dashboard(): void {
        require_admin();
        view('admin/dashboard');
    }

  public function revenue(): void {
    require_admin();

    $mode = $_GET['mode'] ?? 'day';
    if (!in_array($mode, ['day', 'month', 'year', 'all'], true)) {
      $mode = 'day';
    }

    $today = date('Y-m-d');
    $thisMonth = date('Y-m');
    $thisYear = (int)date('Y');

    $day = $_GET['day'] ?? $today;
    $month = $_GET['month'] ?? $thisMonth;
    $year = isset($_GET['year']) ? (int)$_GET['year'] : $thisYear;

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
      $day = $today;
    }
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
      $month = $thisMonth;
    }
    if ($year < 2000 || $year > 2100) {
      $year = $thisYear;
    }

    $summary = ['total_revenue' => 0, 'total_bookings' => 0, 'total_seats' => 0];
    $breakdown = [];
    $bookings = [];

    if ($mode === 'all') {
      $summary = Booking::paidRevenueSummaryAll();
      $breakdown = Booking::paidRevenueBreakdownAllByYear();
    } elseif ($mode === 'day') {
      $summary = Booking::paidRevenueSummaryByDay($day);
      $bookings = Booking::paidBookingsForDay($day);
    } elseif ($mode === 'month') {
      $summary = Booking::paidRevenueSummaryByMonth($month);
      $breakdown = Booking::paidRevenueBreakdownByMonth($month);
    } else { // year
      $summary = Booking::paidRevenueSummaryByYear($year);
      $breakdown = Booking::paidRevenueBreakdownByYear($year);
    }

    view('admin/revenue', compact('mode', 'day', 'month', 'year', 'summary', 'breakdown', 'bookings'));
  }

    public function flights(): void {
        require_admin();
      $q = trim($_GET['q'] ?? '');
        if (isset($_GET['delete'])) {
            $id = (int)$_GET['delete'];
        Flight::delete($id);
        $qs = $q !== '' ? ('?q=' . urlencode($q)) : '';
        redirect('/admin/flights' . $qs);
        }
      $flights = Flight::allOrdered($q);
      view('admin/flights', compact('flights', 'q'));
    }

    public function flightTickets(): void {
      require_admin();
      $id = (int)($_GET['id'] ?? 0);
      $q = trim($_GET['q'] ?? '');
      if (!$id) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      $flight = Flight::find($id);
      if (!$flight) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      if (isset($_GET['delete'])) {
        $ticketId = (int)$_GET['delete'];
        if ($ticketId) {
          Ticket::delete($ticketId);
        }
        $qs = 'id=' . $id . (!empty($q) ? ('&q=' . urlencode($q)) : '');
        redirect('/admin/flights/tickets?' . $qs);
      }

      $tickets = Ticket::byFlightId($id);
      view('admin/flight_tickets', compact('flight', 'tickets', 'q'));
    }

    public function createFlightTicket(): void {
      require_admin();
      $error = '';
      $flightId = (int)($_GET['flight_id'] ?? 0);
      $q = trim($_GET['q'] ?? '');
      $form = [
        'ma_ve' => '',
        'hang_ve' => 'Thuong',
        'gia' => '',
        'so_luong_con' => '0',
      ];
      if (!$flightId) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      $flight = Flight::find($flightId);
      if (!$flight) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form['ma_ve'] = isset($_POST['ma_ve']) ? (string)$_POST['ma_ve'] : '';
        $form['hang_ve'] = (string)($_POST['hang_ve'] ?? 'Thuong');
        $form['gia'] = (string)($_POST['gia'] ?? '');
        $form['so_luong_con'] = (string)($_POST['so_luong_con'] ?? '0');

        $ma_ve = strtoupper(trim($form['ma_ve']));
        $hang_ve = $form['hang_ve'] !== '' ? $form['hang_ve'] : 'Thuong';
        $gia = $form['gia'];
        $so_luong_con = (int)$form['so_luong_con'];

        if ($ma_ve === '' || $gia === '') {
          $error = 'Vui lòng nhập mã vé và giá';
        } elseif (Ticket::findByCode($ma_ve)) {
          $error = 'Mã vé đã tồn tại, vui lòng chọn mã khác';
        } else {
          $ok = Ticket::create([
            'ma_ve' => $ma_ve,
            'chuyen_bay_id' => $flightId,
            'hang_ve' => $hang_ve,
            'gia' => $gia,
            'so_luong_con' => $so_luong_con,
          ]);
          if ($ok) {
            $qs = 'id=' . $flightId . (!empty($q) ? ('&q=' . urlencode($q)) : '');
            redirect('/admin/flights/tickets?' . $qs);
          }
          $error = 'Tạo vé thất bại';
        }
      }

      $maVe = (string)($form['ma_ve'] ?? '');
      $hangVe = (string)($form['hang_ve'] ?? 'Thuong');
      $gia = (string)($form['gia'] ?? '');
      $soLuongCon = (string)($form['so_luong_con'] ?? '0');
      view('admin/ticket_create', compact('flight', 'error', 'q', 'maVe', 'hangVe', 'gia', 'soLuongCon'));
    }

    public function editFlightTicket(): void {
      require_admin();
      $error = '';
      $ticketId = (int)($_GET['ticket_id'] ?? 0);
      $q = trim($_GET['q'] ?? '');
      if (!$ticketId) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      $ticket = Ticket::find($ticketId);
      if (!$ticket) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      $flightId = (int)($ticket['chuyen_bay_id'] ?? 0);
      $flight = $flightId ? Flight::find($flightId) : null;
      if (!$flight) {
        redirect('/admin/flights' . ($q !== '' ? ('?q=' . urlencode($q)) : ''));
      }

      $form = [
        'ma_ve' => (string)($ticket['ma_ve'] ?? ''),
        'hang_ve' => (string)($ticket['hang_ve'] ?? 'Thuong'),
        'gia' => (string)($ticket['gia'] ?? ''),
        'so_luong_con' => (string)($ticket['so_luong_con'] ?? 0),
      ];

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form['ma_ve'] = isset($_POST['ma_ve']) ? (string)$_POST['ma_ve'] : '';
        $form['hang_ve'] = (string)($_POST['hang_ve'] ?? 'Thuong');
        $form['gia'] = (string)($_POST['gia'] ?? '');
        $form['so_luong_con'] = (string)($_POST['so_luong_con'] ?? '0');

        $ma_ve = strtoupper(trim($form['ma_ve']));
        $hang_ve = $form['hang_ve'] !== '' ? $form['hang_ve'] : 'Thuong';
        $gia = $form['gia'];
        $so_luong_con = (int)$form['so_luong_con'];

        if ($ma_ve === '' || $gia === '') {
          $error = 'Vui lòng nhập mã vé và giá';
        } else {
          $existing = Ticket::findByCode($ma_ve);
          if ($existing && (int)$existing['id'] !== $ticketId) {
            $error = 'Mã vé đã tồn tại, vui lòng chọn mã khác';
          } else {
            $ok = Ticket::update($ticketId, [
              'ma_ve' => $ma_ve,
              'hang_ve' => $hang_ve,
              'gia' => $gia,
              'so_luong_con' => $so_luong_con,
            ]);
            if ($ok) {
              $qs = 'id=' . $flightId . (!empty($q) ? ('&q=' . urlencode($q)) : '');
              redirect('/admin/flights/tickets?' . $qs);
            }
            $error = 'Cập nhật vé thất bại';
          }
        }
      }

      $maVe = (string)($form['ma_ve'] ?? ($ticket['ma_ve'] ?? ''));
      $hangVe = (string)($form['hang_ve'] ?? ($ticket['hang_ve'] ?? 'Thuong'));
      $gia = (string)($form['gia'] ?? ($ticket['gia'] ?? ''));
      $soLuongCon = (string)($form['so_luong_con'] ?? ($ticket['so_luong_con'] ?? 0));
      view('admin/ticket_edit', compact('flight', 'ticket', 'error', 'q', 'maVe', 'hangVe', 'gia', 'soLuongCon'));
    }

    public function createFlight(): void {
        require_admin();
        $error = '';
        $planes = Plane::allOrdered();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $so_hieu = isset($_POST['so_hieu']) ? strtoupper(trim($_POST['so_hieu'])) : '';
          $noi_di = $_POST['noi_di'];
          $noi_den = $_POST['noi_den'];
          $gio_khoi_hanh = str_replace('T', ' ', $_POST['gio_khoi_hanh']) . ':00';
          $gio_ha_canh = str_replace('T', ' ', $_POST['gio_ha_canh']) . ':00';
          $gia_thuong = $_POST['gia_thuong'];
          $gia_thuong_gia = $_POST['gia_thuong_gia'];
          $ghe_con = $_POST['ghe_con'];
          $may_bay_id = isset($_POST['may_bay_id']) && $_POST['may_bay_id'] !== '' ? (int)$_POST['may_bay_id'] : null;
          if (Flight::findByCode($so_hieu)) {
            $error = 'Mã chuyến bay đã tồn tại, vui lòng chọn mã khác';
          } else {
            if (Flight::create(compact('so_hieu','noi_di','noi_den','gio_khoi_hanh','gio_ha_canh','gia_thuong','gia_thuong_gia','ghe_con','may_bay_id'))) {
              redirect('/admin/flights');
            } else {
              $error = 'Tạo chuyến bay thất bại';
            }
          }
        }
        view('admin/flight_create', compact('error','planes'));
    }

    public function editFlight(): void {
        require_admin();
        $error = '';
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { redirect('/admin/flights'); }

        $flight = Flight::find($id);
        if (!$flight) {
          redirect('/admin/flights');
        }
      $planes = Plane::allOrdered();

        $selectedPlaneId = (string)($flight['may_bay_id'] ?? '');
        $selectedAirline = '';
        if (!empty($planes) && $selectedPlaneId !== '') {
          foreach ($planes as $p) {
            if ((string)($p['id'] ?? '') === $selectedPlaneId) {
              $selectedAirline = (string)($p['hang_may_bay'] ?? 'Khác');
              break;
            }
          }
        }
        $gioKhoiHanhLocal = $this->toDatetimeLocalString($flight['gio_khoi_hanh'] ?? '');
        $gioHaCanhLocal = $this->toDatetimeLocalString($flight['gio_ha_canh'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $so_hieu = $_POST['so_hieu'];
          $noi_di = $_POST['noi_di'];
          $noi_den = $_POST['noi_den'];
          $gio_khoi_hanh = !empty($_POST['gio_khoi_hanh']) ? str_replace('T', ' ', $_POST['gio_khoi_hanh']) . ':00' : '';
          $gio_ha_canh = !empty($_POST['gio_ha_canh']) ? str_replace('T', ' ', $_POST['gio_ha_canh']) . ':00' : '';
          $gia_thuong = $_POST['gia_thuong'];
          $gia_thuong_gia = $_POST['gia_thuong_gia'];
          $ghe_con = $_POST['ghe_con'];
          $may_bay_id = isset($_POST['may_bay_id']) && $_POST['may_bay_id'] !== '' ? (int)$_POST['may_bay_id'] : null;
          if (empty($gio_khoi_hanh) || empty($gio_ha_canh)) {
            $error = 'Vui lòng nhập đầy đủ thời gian khởi hành và hạ cánh';
          } else {
            $ok = Flight::update($id, [
                'so_hieu' => $so_hieu,
                'noi_di' => $noi_di,
                'noi_den' => $noi_den,
                'gio_khoi_hanh' => $gio_khoi_hanh,
                'gio_ha_canh' => $gio_ha_canh,
                'gia_thuong' => $gia_thuong,
                'gia_thuong_gia' => $gia_thuong_gia,
                'ghe_con' => $ghe_con,
            'may_bay_id' => $may_bay_id,
            ]);
            if ($ok) { redirect('/admin/flights'); } else { $error = 'Cập nhật thất bại'; }
          }
        }
        view('admin/flight_edit', compact('flight','error','planes','selectedPlaneId','selectedAirline','gioKhoiHanhLocal','gioHaCanhLocal'));
    }

      public function planes(): void {
        require_admin();

        $q = trim($_GET['q'] ?? '');

        if (isset($_GET['delete'])) {
          $id = (int)$_GET['delete'];
          Plane::delete($id);
          $qs = $q !== '' ? ('?q=' . urlencode($q)) : '';
          redirect('/admin/planes' . $qs);
        }

        $planes = Plane::allOrdered($q);
        view('admin/planes', compact('planes', 'q'));
      }

      public function createPlane(): void {
        require_admin();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $ma_may_bay = isset($_POST['ma_may_bay']) ? strtoupper(trim($_POST['ma_may_bay'])) : '';
          $ten_may_bay = isset($_POST['ten_may_bay']) ? trim($_POST['ten_may_bay']) : '';
          $hang_may_bay = isset($_POST['hang_may_bay']) ? trim($_POST['hang_may_bay']) : 'Khác';

          if ($ma_may_bay === '' || $ten_may_bay === '') {
            $error = 'Vui lòng nhập đầy đủ mã và tên máy bay';
          } elseif (Plane::findByCode($ma_may_bay)) {
            $error = 'Mã máy bay đã tồn tại, vui lòng chọn mã khác';
          } else {
            if (Plane::create(compact('ma_may_bay', 'ten_may_bay', 'hang_may_bay'))) {
              redirect('/admin/planes');
            }
            $error = 'Tạo máy bay thất bại';
          }
        }

        view('admin/plane_create', compact('error'));
      }

      public function editPlane(): void {
        require_admin();
        $error = '';
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { redirect('/admin/planes'); }

        $plane = Plane::find($id);
        if (!$plane) { redirect('/admin/planes'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $ma_may_bay = isset($_POST['ma_may_bay']) ? strtoupper(trim($_POST['ma_may_bay'])) : '';
          $ten_may_bay = isset($_POST['ten_may_bay']) ? trim($_POST['ten_may_bay']) : '';
          $hang_may_bay = isset($_POST['hang_may_bay']) ? trim($_POST['hang_may_bay']) : 'Khác';

          if ($ma_may_bay === '' || $ten_may_bay === '') {
            $error = 'Vui lòng nhập đầy đủ mã và tên máy bay';
          } else {
            $existing = Plane::findByCode($ma_may_bay);
            if ($existing && (int)$existing['id'] !== $id) {
              $error = 'Mã máy bay đã tồn tại, vui lòng chọn mã khác';
            } else {
              $ok = Plane::update($id, compact('ma_may_bay', 'ten_may_bay', 'hang_may_bay'));
              if ($ok) { redirect('/admin/planes'); } else { $error = 'Cập nhật thất bại'; }
            }
          }
        }

        view('admin/plane_edit', compact('plane','error'));
      }

    public function bookings(): void {
        require_admin();

      // Export all bookings + passengers to Excel (.xls via HTML table)
      if (isset($_GET['export_all'])) {
        $result = Booking::listWithUsers('paid');
        $bookingIds = array_column($result, 'id');
        $passengersByBooking = Booking::passengersByBookingIds($bookingIds);

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="bookings_' . date('Ymd_His') . '.xls"');

        echo "\xEF\xBB\xBF";
        echo "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>";
        echo '<table border="1" cellspacing="0" cellpadding="5">';
        echo '<tr>';
        $headers = [
          'Ma dat ve', 'Nguoi dung ID', 'Ten khach hang', 'Email khach hang', 'So hieu', 'Noi di', 'Noi den',
          'Gio khoi hanh', 'Hang bay', 'May bay', 'Trang thai', 'Dat luc', 'So ghe dat', 'Tong tien',
          'Ten hanh khach', 'Dien thoai', 'Email hanh khach', 'Gioi tinh', 'Tuoi', 'Loai ve', 'Gia ve', 'So ghe'
        ];
        foreach ($headers as $h) {
          echo '<th>' . htmlspecialchars($h) . '</th>';
        }
        echo '</tr>';

        foreach ($result as $booking) {
          $passengers = $passengersByBooking[$booking['id']] ?? [[]];
          foreach ($passengers as $p) {
            $plane = trim((string)($booking['ma_may_bay'] ?? '') . (!empty($booking['ten_may_bay']) ? (' - ' . ($booking['ten_may_bay'] ?? '')) : ''));
            $status = ($booking['trang_thai'] ?? '') === 'paid'
              ? 'Da thanh toan'
              : (($booking['trang_thai'] ?? '') !== '' ? (string)$booking['trang_thai'] : '');

            $row = [
              $booking['id'] ?? '',
              $booking['nguoi_dung_id'] ?? '',
              $booking['customer_name'] ?? '',
              $booking['customer_email'] ?? '',
              $booking['so_hieu'] ?? '',
              $booking['noi_di'] ?? '',
              $booking['noi_den'] ?? '',
              $booking['gio_khoi_hanh'] ?? '',
              $booking['hang_may_bay'] ?? '',
              $plane,
              $status,
              $booking['dat_luc'] ?? '',
              $booking['so_ghe_dat'] ?? '',
              $booking['tong_tien'] ?? '',
              $p['ten_hanh_khach'] ?? '',
              $p['dien_thoai'] ?? '',
              $p['email_hanh_khach'] ?? '',
              $p['gioi_tinh'] ?? '',
              $p['tuoi'] ?? '',
              $p['loai_ve'] ?? '',
              $p['gia_ve'] ?? '',
              $p['so_ghe'] ?? '',
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

        if (isset($_GET['delete_booking'])) {
          $booking_id = (int)$_GET['delete_booking'];
          $booking_to_delete = Booking::find($booking_id);
          if ($booking_to_delete) {
            Booking::delete($booking_id);
            Flight::adjustSeats((int)$booking_to_delete['chuyen_bay_id'], (int)$booking_to_delete['so_ghe_dat']);
            if (!empty($booking_to_delete['ve_id'])) {
              Ticket::release((int)$booking_to_delete['ve_id'], (int)$booking_to_delete['so_ghe_dat']);
            }
            redirect('/admin/bookings');
          }
        }

        $result = Booking::listWithUsers('paid');
        $bookingIds = array_column($result, 'id');
        $passengersByBooking = Booking::passengersByBookingIds($bookingIds);
        view('admin/bookings', compact('result', 'passengersByBooking'));
    }

    public function customers(): void {
      require_admin();
      if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        User::deleteCustomer($id);
        redirect('/admin/customers');
      }

      $customers = User::customersOrdered();
      view('admin/customers', compact('customers'));
    }

    public function createCustomer(): void {
      require_admin();
      $error = '';

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ten = isset($_POST['ten']) ? trim($_POST['ten']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $mat_khau = isset($_POST['mat_khau']) ? trim($_POST['mat_khau']) : '';

        if ($ten === '' || $email === '' || $mat_khau === '') {
          $error = 'Vui lòng nhập đầy đủ tên, email và mật khẩu';
        } elseif (User::findByEmail($email)) {
          $error = 'Email đã tồn tại, vui lòng chọn email khác';
        } else {
          if (User::createCustomer($ten, $email, $mat_khau)) {
            redirect('/admin/customers');
          }
          $error = 'Tạo tài khoản thất bại';
        }
      }

      view('admin/customer_create', compact('error'));
    }

    public function editCustomer(): void {
      require_admin();
      $error = '';
      $id = (int)($_GET['id'] ?? 0);
      if (!$id) { redirect('/admin/customers'); }

      $customer = User::findCustomer($id);
      if (!$customer) { redirect('/admin/customers'); }

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ten = isset($_POST['ten']) ? trim($_POST['ten']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $mat_khau = isset($_POST['mat_khau']) ? trim($_POST['mat_khau']) : '';

        if ($ten === '' || $email === '' || $mat_khau === '') {
          $error = 'Vui lòng nhập đầy đủ tên, email và mật khẩu';
        } else {
          $existing = User::findByEmail($email);
          if ($existing && (int)$existing['id'] !== $id) {
            $error = 'Email đã tồn tại, vui lòng chọn email khác';
          } else {
            $ok = User::updateCustomer($id, [
              'ten' => $ten,
              'email' => $email,
              'mat_khau' => $mat_khau,
            ]);
            if ($ok) { redirect('/admin/customers'); } else { $error = 'Cập nhật thất bại'; }
          }
        }

        $customer = User::findCustomer($id) ?? $customer;
      }

      view('admin/customer_edit', compact('customer','error'));
    }
}
