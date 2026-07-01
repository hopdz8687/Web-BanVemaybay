<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vé của tôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.customer-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .tickets-container { background: rgba(245, 248, 243, 0.96); border-radius: 8px; padding: 1rem; border: 1px solid #e0e8dc; }
    .tickets-container h2 {
      font-weight: 600;
      font-size: 1.6rem;
      color: #0E6B7E;
      text-shadow: none;
    }
    .ticket-card { border-left: 4px solid #0E6B7E; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px; background: #ffffff; }
    .ticket-card .card-header { background: linear-gradient(90deg, #f5f8f3, #ffffff); border-bottom: 1px solid #e0e8dc; color: #0E6B7E; }
  </style>
</head>
<body class="customer-bg d-flex flex-column min-vh-100">
<main class="container-lg py-4 flex-fill">
  <!-- Thông báo thanh toán thành công -->
  <?php if (!empty($_SESSION['payment_success'])): ?>
    <?php 
      $success = $_SESSION['payment_success'];
      unset($_SESSION['payment_success']);
      $ticketList = !empty($success['ticket_numbers']) ? implode(', ', $success['ticket_numbers']) : 'N/A';
    ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-left: 5px solid #28a745;">
      <div class="d-flex align-items-start">
        <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem; flex-shrink: 0;"></i>
        <div class="flex-grow-1">
          <h5 class="alert-heading mb-2">✓ Đặt vé thành công!</h5>
          <p class="mb-2">
            <strong>Số lượng vé:</strong> <?=htmlspecialchars($success['count'])?> vé
          </p>
          <p class="mb-2">
            <strong>Mã số vé:</strong> <span class="badge bg-info"><?=htmlspecialchars($ticketList)?></span>
          </p>
          <p class="mb-0">
            <small class="text-muted">Thời gian thanh toán: <?=htmlspecialchars($success['timestamp'])?></small>
          </p>
          <p class="mb-0 small mt-2">
            <em class="text-success">Xem chi tiết vé bên dưới</em>
          </p>
        </div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-dark"><i class="bi bi-ticket-perforated"></i> Vé của tôi</h2>
    <div class="btn-group">
      <a href="<?=base_url('/customer/dashboard')?>" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
      <a href="<?=base_url('/customer/my-tickets')?>?export=1" class="btn btn-success">
        <i class="bi bi-file-earmark-spreadsheet"></i> Xuất Excel
      </a>
    </div>
  </div>

  <?php if (empty($bookings)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <i class="bi bi-info-circle"></i> Bạn chưa đặt vé nào. <a href="<?=base_url('/customer/dashboard')?>">Tìm chuyến bay</a>
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($bookings as $booking): ?>
        <div class="col-md-6 col-lg-8 mb-4">
          <div class="card ticket-card">
            <div class="card-header text-dark">
                <div class="d-flex justify-content-between align-items-start mb-3" style="background: linear-gradient(135deg, #0E6B7E, #1B8FA0); color: white; padding: 0.75rem 1rem; border-radius: 6px 6px 0 0;">
                  <div>
                    <h5 class="mb-1">
                      <?=htmlspecialchars($booking['so_hieu'])?> - 
                      <?=htmlspecialchars($booking['noi_di'])?> → <?=htmlspecialchars($booking['noi_den'])?>
                    </h5>
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã thanh toán</span>
                  </div>
                  <div class="d-flex gap-2">
                    <?php if (!$booking['da_xac_nhan']): ?>
                    <a href="<?=base_url('/customer/edit-ticket')?>?booking_id=<?=$booking['id']?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i> Sửa thông tin
                    </a>
                    <?php else: ?>
                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Đã xác nhận - không thể sửa">
                      <i class="bi bi-pencil"></i> Sửa thông tin
                    </button>
                    <?php endif; ?>
                    <a href="<?=base_url('/customer/my-tickets')?>?delete_booking=<?=$booking['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa vé này?')">
                      <i class="bi bi-trash"></i> Hủy vé
                    </a>
                  </div>
                </div>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <p class="mb-1"><small class="text-muted">Ngày khởi hành</small><br><strong><?=format_datetime($booking['gio_khoi_hanh'])?></strong></p>
                  <p class="mb-0"><small class="text-muted">Ngày đặt</small><br><strong><?=format_datetime($booking['dat_luc'])?></strong></p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><small class="text-muted">Số ghế đặt</small><br><strong><?=$booking['so_ghe_dat']?> ghe</strong></p>
                  <p class="mb-0"><small class="text-muted">Tổng tiền</small><br><strong class="text-success"><?=number_format($booking['tong_tien'], 0)?> VND</strong></p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="text-muted small"><strong class="text-dark">Hãng bay:</strong> <?=htmlspecialchars(($booking['hang_may_bay'] ?? '') !== '' ? ($booking['hang_may_bay'] ?? '') : 'Chưa gán')?></div>
                </div>
                <div class="col-md-6">
                  <div class="text-muted small"><strong class="text-dark">Máy bay:</strong>
                    <?php if (!empty($booking['ma_may_bay']) || !empty($booking['ten_may_bay'])): ?>
                      <?=htmlspecialchars(($booking['ma_may_bay'] ?? '') . (!empty($booking['ten_may_bay']) ? ' - ' . ($booking['ten_may_bay'] ?? '') : ''))?>
                    <?php else: ?>
                      <?=htmlspecialchars('Chưa gán')?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <hr>
              <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Danh Sách Hành Khách</h6>
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#ticketDetailsModal<?=$booking['id']?>">
                  <i class="bi bi-eye"></i> Xem Chi Tiết Vé
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Modal Chi Tiết Vé -->
  <?php foreach ($bookings as $booking): ?>
    <?php $passengers = $passengersByBooking[$booking['id']] ?? []; ?>
    <div class="modal fade" id="ticketDetailsModal<?=$booking['id']?>" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="background: linear-gradient(90deg, #0E6B7E, #1B8FA0); color: white;">
            <h5 class="modal-title">
              <i class="bi bi-ticket-perforated"></i> Chi Tiết Vé - <?=htmlspecialchars($booking['so_hieu'])?>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php if (empty($passengers)): ?>
              <div class="alert alert-secondary">Chưa có thông tin hành khách cho đơn này.</div>
            <?php else: ?>
              <div class="row g-3">
                <?php foreach ($passengers as $pass): ?>
                  <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-left: 4px solid #0E6B7E;">
                      <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="fw-bold">
                          Vé ghế <span class="badge" style="background-color: #0E6B7E;"><?=htmlspecialchars($pass['so_ghe'])?></span>
                        </div>
                        <div>
                          <span class="badge" style="background-color: #6B9B6F; color: #fff;"><?=htmlspecialchars($pass['loai_ve'] ?? 'N/A')?></span>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <div class="text-muted small">Hành khách</div>
                              <div class="fw-semibold"><?=htmlspecialchars($pass['ten_hanh_khach'] ?? '')?></div>
                            </div>
                            <div class="mb-3">
                              <div class="text-muted small">Điện thoại</div>
                              <div><?=htmlspecialchars($pass['dien_thoai'] ?? '')?></div>
                            </div>
                            <div class="mb-3">
                              <div class="text-muted small">Giới tính</div>
                              <div><?=htmlspecialchars($pass['gioi_tinh'] ?? 'N/A')?></div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <div class="text-muted small">Email</div>
                              <div><?=htmlspecialchars($pass['email_hanh_khach'] ?? '')?></div>
                            </div>
                            <div class="mb-3">
                              <div class="text-muted small">Độ tuổi</div>
                              <div><?=htmlspecialchars((string)($pass['tuoi'] ?? 'N/A'))?></div>
                            </div>
                            <div class="mb-3">
                              <div class="text-muted small">Giá vé</div>
                              <div class="fw-bold text-success"><?=number_format((float)($pass['gia_ve'] ?? 0), 0)?> VND</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</main>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap Modal sẽ tự động hoạt động với data-bs-toggle
  });
</script>
</body>
</html>
