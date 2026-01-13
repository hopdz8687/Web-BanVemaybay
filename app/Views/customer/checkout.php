<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thanh toán</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.customer-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .page-wrap {
      background: rgba(255,255,255,0.94);
      border-radius: 8px;
      padding: 1rem;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      max-width: 1000px;
    }
  </style>
</head>
<body class="customer-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-1"><i class="bi bi-credit-card"></i> Thanh toán</h2>
      <div class="text-muted">Nhập thông tin thanh toán cho các đơn đã chọn</div>
    </div>
    <a href="<?=base_url('/customer/cart')?>" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-header bg-light">
      <strong>Đơn đã chọn</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>Mã đặt</th>
              <th>Chuyến bay</th>
              <th>Mã vé</th>
              <th>Loại vé</th>
              <th>Số lượng</th>
              <th>Tổng tiền</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($bookings ?? []) as $b): ?>
              <tr>
                <td><span class="badge bg-secondary">#<?=htmlspecialchars((string)($b['id'] ?? ''))?></span></td>
                <td>
                  <strong><?=htmlspecialchars($b['so_hieu'] ?? '')?></strong><br>
                  <small class="text-muted"><?=htmlspecialchars($b['noi_di'] ?? '')?> → <?=htmlspecialchars($b['noi_den'] ?? '')?></small>
                </td>
                <td><strong><?=htmlspecialchars(($b['ma_ve'] ?? '') !== '' ? ($b['ma_ve'] ?? '') : '—')?></strong></td>
                <td><?=htmlspecialchars(($b['hang_ve'] ?? '') !== '' ? ($b['hang_ve'] ?? '') : '—')?></td>
                <td><span class="badge bg-info"><?=htmlspecialchars((string)($b['so_ghe_dat'] ?? 0))?></span></td>
                <td><strong class="text-success"><?=number_format((float)($b['tong_tien'] ?? 0), 0)?> VND</strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      <div>
        <small class="text-muted">Tổng thanh toán</small>
        <div class="h5 mb-0"><strong><?=number_format((float)($grandTotal ?? 0), 0)?> VND</strong></div>
      </div>
    </div>
  </div>

  <form method="post" action="<?=base_url('/customer/checkout')?>" class="card">
    <div class="card-header bg-success text-white">
      <strong>Thông tin thanh toán</strong>
    </div>
    <div class="card-body">
      <input type="hidden" name="step" value="pay">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Họ tên người thanh toán</label>
          <input name="ten_thanh_toan" type="text" class="form-control form-control-lg" required value="<?=htmlspecialchars($payName ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Điện thoại</label>
          <input name="dien_thoai_thanh_toan" type="text" class="form-control form-control-lg" required value="<?=htmlspecialchars($payPhone ?? '')?>">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input name="email_thanh_toan" type="email" class="form-control form-control-lg" required value="<?=htmlspecialchars($payEmail ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Địa chỉ</label>
          <input name="dia_chi_thanh_toan" type="text" class="form-control form-control-lg" value="<?=htmlspecialchars($payAddress ?? '')?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Phương thức thanh toán</label>
          <select name="phuong_thuc_thanh_toan" class="form-select form-select-lg">
            <option value="direct" <?=($paymentMethod ?? 'direct') === 'direct' ? 'selected' : ''?>>Thanh toán trực tiếp</option>
            <option value="atm" <?=($paymentMethod ?? 'direct') === 'atm' ? 'selected' : ''?>>Thanh toán bằng thẻ ATM</option>
            <option value="momo" <?=($paymentMethod ?? 'direct') === 'momo' ? 'selected' : ''?>>Thanh toán bằng momo</option>
          </select>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex gap-2">
      <button type="submit" class="btn btn-lg btn-success flex-grow-1" onclick="return confirm('Xác nhận thanh toán?')">
        <i class="bi bi-check2-circle"></i> Thanh toán
      </button>
      <a class="btn btn-lg btn-outline-secondary" href="<?=base_url('/customer/cart')?>">Hủy</a>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
