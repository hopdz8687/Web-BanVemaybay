<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý máy bay</title>
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
    <h2><i class="bi bi-airplane-engines"></i> Quản Lý Máy Bay</h2>
    <div>
      <a href="<?=base_url('/admin/planes/create')?>" class="btn btn-success me-2">
        <i class="bi bi-plus-circle"></i> Thêm Máy Bay
      </a>
      <a href="<?=base_url('/admin/dashboard')?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
    </div>
  </div>

  <form class="row g-2 align-items-center mb-3" method="get" action="<?=base_url('/admin/planes')?>">
    <div class="col-12 col-md-6">
      <div class="input-group">
        <input
          type="text"
          class="form-control"
          name="q"
          value="<?=htmlspecialchars($q ?? '')?>"
          placeholder="tìm kiếm theo mã máy bay"
          aria-label="Tìm kiếm theo mã máy bay"
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
          <th style="width: 90px;">STT</th>
          <th>Hãng máy bay</th>
          <th>Mã máy bay</th>
          <th>Tên máy bay</th>
          <th style="width: 220px;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($planes)): ?>
          <tr><td colspan="5" class="text-center">Chưa có máy bay</td></tr>
        <?php else: $stt = 1; foreach ($planes as $row): ?>
          <tr>
            <td><span class="badge bg-secondary"><?=$stt++?></span></td>
            <td><?=htmlspecialchars($row['hang_may_bay'] ?? 'Khác')?></td>
            <td><strong><?=htmlspecialchars($row['ma_may_bay'])?></strong></td>
            <td><?=htmlspecialchars($row['ten_may_bay'])?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="<?=base_url('/admin/planes/edit')?>?id=<?=$row['id']?>">
                <i class="bi bi-pencil"></i> Sửa
              </a>
              <a class="btn btn-sm btn-danger" href="<?=base_url('/admin/planes')?>?delete=<?=$row['id']?><?=!empty($q) ? '&q=' . urlencode($q) : ''?>" onclick="return confirm('Xóa máy bay này?')">
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
