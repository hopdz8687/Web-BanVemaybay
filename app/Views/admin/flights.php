<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý chuyến bay</title>
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
    .ticket-economy { color: #1e7e34; font-weight: 700; }
    .ticket-business { color: #0d6efd; font-weight: 700; }
  </style>
</head>
<body class="admin-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-airplane"></i> Quản Lý Chuyến Bay</h2>
    <div>
      <a href="<?=base_url('/admin/flights/create')?>" class="btn btn-success me-2">
        <i class="bi bi-plus-circle"></i> Thêm Chuyến
      </a>
      <a href="<?=base_url('/admin/dashboard')?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
    </div>
  </div>

  <form class="row g-2 align-items-center mb-3" method="get" action="<?=base_url('/admin/flights')?>">
    <div class="col-12 col-md-6">
      <div class="input-group">
        <input
          type="text"
          class="form-control"
          name="q"
          value="<?=htmlspecialchars($q ?? '')?>"
          placeholder="tìm kiếm theo mã chuyến bay"
          aria-label="Tìm kiếm theo mã chuyến bay"
          autocomplete="off"
        >
        <button class="btn btn-primary" type="submit">Tìm</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Chuyến bay</th>
          <th>Hãng</th>
          <th>Máy bay</th>
          <th>Tuyến bay</th>
          <th>Khởi hành</th>
          <th>Giá</th>
          <th>Ghế</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($flights)): ?>
          <tr><td colspan="9" class="text-center">Không có chuyến bay</td></tr>
        <?php else: foreach ($flights as $row): ?>
          <tr>
            <td><span class="badge bg-secondary"><?=$row['id']?></span></td>
            <td><strong><?=htmlspecialchars($row['so_hieu'])?></strong></td>
            <td><?=htmlspecialchars($row['hang_may_bay'] ?? '-')?></td>
            <td>
              <?php if (!empty($row['ten_may_bay']) || !empty($row['ma_may_bay'])): ?>
                <strong><?=htmlspecialchars($row['ma_may_bay'] ?? '')?></strong>
                <?=!empty($row['ten_may_bay']) ? ' - ' . htmlspecialchars($row['ten_may_bay']) : ''?>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?=htmlspecialchars($row['noi_di'])?> → <?=htmlspecialchars($row['noi_den'])?></td>
            <td><?=format_datetime($row['gio_khoi_hanh'])?></td>
            <td>
              <div><small class="text-muted">Thường:</small> <strong class="ticket-economy"><?=number_format($row['gia_thuong'],0)?> VND</strong></div>
              <div><small class="text-muted">Thương gia:</small> <strong class="ticket-business"><?=number_format($row['gia_thuong_gia'],0)?> VND</strong></div>
            </td>
            <td><span class="badge bg-info"><?=$row['ghe_con']?></span></td>
            <td>
              <a class="btn btn-sm btn-info me-1" href="<?=base_url('/admin/flights/tickets')?>?id=<?=$row['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>">
                <i class="bi bi-ticket-perforated"></i> Xem vé
              </a>
              <a class="btn btn-sm btn-primary" href="<?=base_url('/admin/flights/edit')?>?id=<?=$row['id']?>">
                <i class="bi bi-pencil"></i> Sửa
              </a>
              <a class="btn btn-sm btn-danger" href="<?=base_url('/admin/flights')?>?delete=<?=$row['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>" onclick="return confirm('Xóa?')">
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
