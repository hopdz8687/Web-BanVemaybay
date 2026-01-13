<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danh sách đặt vé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.admin-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .tickets-container { background: transparent; border-radius: 8px; padding: 1rem; border: 1px solid rgba(255,255,255,0.06); }
    .tickets-container h2 {
      font-weight: 800;
      font-size: 1.6rem;
      color: #ffffff;
      text-shadow: 0 1px 2px rgba(0,0,0,0.45);
    }
    .ticket-card { border-left: 5px solid #007bff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .ticket-card .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .ticket-card .card-body { padding: 1rem; }
    .ticket-card p { margin-bottom: .35rem; }
    .ticket-card hr { margin: .75rem 0; }
    .ticket-card .card-header h5 { font-size: 1.05rem; }
  </style>
</head>
<body class="admin-bg">
<main class="container-lg py-4 flex-fill">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white"><i class="bi bi-ticket-perforated"></i> Danh Sách Khách Đặt Vé</h2>
    <div class="btn-group">
      <a href="<?=base_url('/admin/dashboard')?>" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
      <a href="<?=base_url('/admin/bookings')?>?export_all=1" class="btn btn-success">
        <i class="bi bi-file-earmark-spreadsheet"></i> Xuất Excel
      </a>
    </div>
  </div>

  <?php if (empty($result)): ?>
      <div class="alert alert-info alert-dismissible fade show">
      <i class="bi bi-info-circle"></i> Không có đặt vé nào
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($result as $booking): ?>
        <div class="col-12 col-md-10 col-lg-8 mb-4">
          <div class="card ticket-card">
            <div class="card-header text-white">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                  <?=htmlspecialchars($booking['so_hieu'])?> -
                  <?=htmlspecialchars($booking['noi_di'])?> → <?=htmlspecialchars($booking['noi_den'])?>
                </h5>

                <div class="d-flex align-items-center gap-2">
                  <?php if (!empty($booking['trang_thai']) && $booking['trang_thai'] === 'paid'): ?>
                    <span class="badge bg-success">Đã thanh toán</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Chưa thanh toán</span>
                  <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                  <a href="<?=base_url('/admin/bookings')?>?delete_booking=<?=$booking['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa đặt vé này?')">
                    <i class="bi bi-trash"></i> Hủy vé
                  </a>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div class="row g-2 mb-2">
                <div class="col-md-6">
                  <p class="mb-1"><small class="text-muted">Khởi hành</small><br><strong><?=format_datetime($booking['gio_khoi_hanh'])?></strong></p>
                  <p class="mb-0"><small class="text-muted">Ngày đặt</small><br><strong><?=format_datetime($booking['dat_luc'])?></strong></p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><small class="text-muted">Khách hàng</small><br><strong><?=htmlspecialchars($booking['customer_name'])?></strong></p>
                  <p class="mb-0"><small class="text-muted">Email</small><br><strong><?=htmlspecialchars($booking['customer_email'])?></strong></p>
                </div>
              </div>

              <div class="row g-2 mb-2">
                <div class="col-md-6">
                  <p class="mb-1"><small class="text-muted">Số ghế đặt</small><br><strong><?=htmlspecialchars((string)($booking['so_ghe_dat'] ?? 0))?> ghế</strong></p>
                </div>
                <div class="col-md-6">
                  <p class="mb-0"><small class="text-muted">Tổng tiền</small><br><strong class="text-success"><?=number_format((float)($booking['tong_tien'] ?? 0), 0)?> VND</strong></p>
                </div>
              </div>

              <div class="row g-2 mb-2">
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
              <h6 class="fw-bold mb-2">Danh Sách Hành Khách</h6>
              <?php $passengers = $passengersByBooking[$booking['id']] ?? []; ?>
              <?php if (empty($passengers)): ?>
                <div class="alert alert-secondary mb-0">Chưa có thông tin hành khách cho đơn này.</div>
              <?php else: ?>
                <div class="row g-2">
                  <?php foreach ($passengers as $pass): ?>
                    <div class="col-12 col-md-6">
                      <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                          <div class="fw-bold">
                            Vé ghế <span class="badge bg-primary"><?=htmlspecialchars((string)($pass['so_ghe'] ?? ''))?></span>
                          </div>
                          <div>
                            <span class="badge bg-info text-dark"><?=htmlspecialchars($pass['loai_ve'] ?? 'N/A')?></span>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="mb-2">
                            <div class="text-muted small">Hành khách</div>
                            <div class="fw-semibold"><?=htmlspecialchars($pass['ten_hanh_khach'] ?? '')?></div>
                          </div>
                          <div class="row g-2">
                            <div class="col-12 col-sm-6">
                              <div class="text-muted small">Điện thoại</div>
                              <div><?=htmlspecialchars($pass['dien_thoai'] ?? '')?></div>
                            </div>
                            <div class="col-12 col-sm-6">
                              <div class="text-muted small">Email</div>
                              <div><?=htmlspecialchars($pass['email_hanh_khach'] ?? '')?></div>
                            </div>
                            <div class="col-12 col-sm-6">
                              <div class="text-muted small">Giới tính</div>
                              <div><?=htmlspecialchars($pass['gioi_tinh'] ?? 'N/A')?></div>
                            </div>
                            <div class="col-12 col-sm-6">
                              <div class="text-muted small">Độ tuổi</div>
                              <div><?=htmlspecialchars((string)($pass['tuoi'] ?? 'N/A'))?></div>
                            </div>
                          </div>
                          <hr class="my-3">
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">Giá vé</div>
                            <div class="fw-bold text-success"><?=number_format((float)($pass['gia_ve'] ?? 0), 0)?> VND</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
