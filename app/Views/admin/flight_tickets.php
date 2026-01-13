<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vé của chuyến bay</title>
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
    }
  </style>
</head>
<body class="admin-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Vé của chuyến bay</h2>
      <div class="text-muted">Mã chuyến bay: <strong><?=htmlspecialchars($flight['so_hieu'] ?? '')?></strong></div>
    </div>
    <div>
      <a href="<?=base_url('/admin/flights/tickets/create')?>?flight_id=<?=$flight['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>" class="btn btn-success me-2">
        <i class="bi bi-plus-circle"></i> Thêm vé
      </a>
      <a href="<?=base_url('/admin/flights')?><?=!empty($q) ? ('?q=' . urlencode($q)) : ''?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th style="width: 90px;">STT</th>
          <th>Mã vé</th>
          <th>Mã chuyến bay</th>
          <th>Hạng vé</th>
          <th>Giá</th>
          <th>Số lượng còn</th>
          <th style="width: 200px;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($tickets)): ?>
          <tr>
            <td colspan="6" class="text-center">
              Chưa có vé cho chuyến bay này.
            </td>
          </tr>
        <?php else: $stt = 1; foreach ($tickets as $row): ?>
          <tr>
            <td><span class="badge bg-secondary"><?=$stt++?></span></td>
            <td><strong><?=htmlspecialchars($row['ma_ve'])?></strong></td>
            <td><?=htmlspecialchars($row['ma_chuyen_bay'] ?? ($flight['so_hieu'] ?? ''))?></td>
            <td><?=htmlspecialchars($row['hang_ve'])?></td>
            <td><strong><?=number_format((float)$row['gia'], 0)?> VND</strong></td>
            <td><span class="badge bg-info"><?=htmlspecialchars((string)$row['so_luong_con'])?></span></td>
            <td>
              <a class="btn btn-sm btn-primary" href="<?=base_url('/admin/flights/tickets/edit')?>?ticket_id=<?=$row['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>">
                <i class="bi bi-pencil"></i> Sửa
              </a>
              <a class="btn btn-sm btn-danger" href="<?=base_url('/admin/flights/tickets')?>?id=<?=$flight['id']?>&delete=<?=$row['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>" onclick="return confirm('Xóa vé này?')">
                <i class="bi bi-trash"></i> Xóa
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
