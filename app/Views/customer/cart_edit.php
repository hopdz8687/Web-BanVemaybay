<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sửa đơn</title>
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
      max-width: 900px;
    }
  </style>
</head>
<body class="customer-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-1">Sửa đơn trong giỏ hàng</h2>
      <div class="text-muted">
        Chuyến bay: <strong><?=htmlspecialchars($flight['so_hieu'] ?? '')?></strong>
        &nbsp;|&nbsp; Vé: <strong><?=htmlspecialchars($ticket['ma_ve'] ?? '')?></strong>
      </div>
    </div>
    <a href="<?=base_url('/customer/cart')?>" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <form method="post" action="<?=base_url('/customer/cart/edit')?>?booking_id=<?=$booking['id']?>" class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">Số lượng</label>
      <input type="number" class="form-control" name="so_luong" min="1" max="<?=$maxQty?>" value="<?=htmlspecialchars((string)($soLuong ?? ($booking['so_ghe_dat'] ?? 1)))?>" required>
      <div class="form-text">Tối đa hiện tại: <?=$maxQty?></div>
    </div>

    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary" type="submit">
        <i class="bi bi-save"></i> Lưu
      </button>
      <a class="btn btn-outline-secondary" href="<?=base_url('/customer/cart')?>">Hủy</a>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
