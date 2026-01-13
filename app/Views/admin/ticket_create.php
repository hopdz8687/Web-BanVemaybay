<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thêm vé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.admin-bg {
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
<body class="admin-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-1">Thêm vé</h2>
      <div class="text-muted">Chuyến bay: <strong><?=htmlspecialchars($flight['so_hieu'] ?? '')?></strong></div>
    </div>
    <div>
      <a class="btn btn-outline-primary" href="<?=base_url('/admin/flights/tickets')?>?id=<?=$flight['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
    </div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <form method="post" action="<?=base_url('/admin/flights/tickets/create')?>?flight_id=<?=$flight['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>" class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">Mã vé</label>
      <input name="ma_ve" class="form-control" required value="<?=htmlspecialchars($maVe)?>" placeholder="VD: VN101-THUONG">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label">Hạng vé</label>
      <select name="hang_ve" class="form-select">
        <option value="Thuong" <?=($hangVe ?? 'Thuong') === 'Thuong' ? 'selected' : ''?>>Thường</option>
        <option value="Thuong gia" <?=($hangVe ?? 'Thuong') === 'Thuong gia' ? 'selected' : ''?>>Thương gia</option>
      </select>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label">Giá</label>
      <input type="number" step="0.01" min="0" name="gia" class="form-control" required value="<?=htmlspecialchars((string)($gia ?? ''))?>" placeholder="VD: 1200000">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label">Số lượng còn</label>
      <input type="number" min="0" name="so_luong_con" class="form-control" value="<?=htmlspecialchars((string)($soLuongCon ?? '0'))?>">
    </div>

    <div class="col-12">
      <button class="btn btn-success" type="submit">
        <i class="bi bi-plus-circle"></i> Thêm vé
      </button>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
