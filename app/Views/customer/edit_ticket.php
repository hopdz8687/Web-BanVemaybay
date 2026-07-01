<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sửa thông tin hành khách</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.edit-ticket-bg {
      background-image: url('<?=base_url('/assets/images/anh2.png')?>');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
    }
    .card:not(.bg-gradient) { background-color: rgba(255,255,255,0.95) !important; }
    .card-header.bg-light { background-color: rgba(248,249,250,0.9) !important; }
    .container.py-4 { background: transparent; }
    .card.bg-gradient { background: linear-gradient(135deg, #0E6B7E, #1B8FA0) !important; color: #fff; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.35); }
    .card.bg-gradient .card-body { background: transparent; }
  </style>
</head>
<body class="edit-ticket-bg d-flex flex-column min-vh-100">
<main class="container py-4 flex-fill">
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card shadow">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #0E6B7E, #1B8FA0);">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-white mb-0"><i class="bi bi-pencil-square"></i> Sửa Thông Tin Hành Khách</h4>
            <small class="text-white-50">Chuyến bay: <?=htmlspecialchars($flight['so_hieu'])?></small>
          </div>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <strong>Lỗi!</strong> <?=htmlspecialchars($error)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          <form method="post">
            <h6 class="text-muted mb-4"><i class="bi bi-info-circle"></i> Cập Nhật Thông Tin Hành Khách</h6>
            <?php
            $seatsBooked = (int)$booking['so_ghe_dat'];
            for ($i = 1; $i <= $seatsBooked; $i++):
                $pass = $passengersForForm[$i - 1] ?? [];
                $seatNumber = $existingSeats[$i - 1] ?? $i;
            ?>
              <div class="card mb-3 border-start border-4" style="border-left-color: #0E6B7E !important;">
                <div class="card-header bg-light">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-person-badge"></i> Hành Khách <?=$i?></h6>
                    <span class="badge bg-secondary">Ghế #<?=$seatNumber?></span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Họ Tên</label>
                      <input name="ten_hanh_khach_<?=$i?>" type="text" class="form-control form-control-lg" value="<?=htmlspecialchars($pass['ten_hanh_khach'] ?? '')?>" placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Điện Thoại</label>
                      <input name="dien_thoai_<?=$i?>" type="text" class="form-control form-control-lg" value="<?=htmlspecialchars($pass['dien_thoai'] ?? '')?>" placeholder="Nhập số điện thoại" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Email</label>
                      <input name="email_hanh_khach_<?=$i?>" type="email" class="form-control form-control-lg" value="<?=htmlspecialchars($pass['email_hanh_khach'] ?? '')?>" placeholder="Nhập email" required>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Giới tính</label><br>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gioi_tinh_<?=$i?>" value="Nam" <?=(!isset($pass['gioi_tinh']) || $pass['gioi_tinh'] === 'Nam') ? 'checked' : ''?>>
                        <label class="form-check-label">Nam</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gioi_tinh_<?=$i?>" value="Nữ" <?=(isset($pass['gioi_tinh']) && $pass['gioi_tinh'] === 'Nữ') ? 'checked' : ''?>>
                        <label class="form-check-label">Nữ</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gioi_tinh_<?=$i?>" value="Khác" <?=(isset($pass['gioi_tinh']) && $pass['gioi_tinh'] === 'Khác') ? 'checked' : ''?>>
                        <label class="form-check-label">Khác</label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Độ tuổi</label>
                      <input name="tuoi_<?=$i?>" type="number" min="0" max="120" class="form-control form-control-lg" value="<?=htmlspecialchars($pass['tuoi'] ?? 18)?>" required>
                    </div>
                  </div>
                </div>
              </div>
            <?php endfor; ?>
            <div class="d-flex gap-2 pt-3">
              <button type="submit" class="btn btn-lg btn-primary flex-grow-1">
                <i class="bi bi-check-circle"></i> Lưu Thay Đổi
              </button>
              <a href="<?=base_url('/customer/my-tickets')?>" class="btn btn-lg btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Huỷ
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
