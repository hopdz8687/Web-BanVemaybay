<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý tài khoản khách hàng</title>
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
    <h2><i class="bi bi-people"></i> Quản Lý Tài Khoản Khách Hàng</h2>
    <div>
      <a href="<?=base_url('/admin/customers/create')?>" class="btn btn-success me-2">
        <i class="bi bi-plus-circle"></i> Thêm Khách Hàng
      </a>
      <a href="<?=base_url('/admin/dashboard')?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Quay Lại
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th style="width: 90px;">STT</th>
          <th>Tên</th>
          <th>Email</th>
          <th>Mật khẩu</th>
          <th style="width: 220px;">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($customers)): ?>
          <tr><td colspan="5" class="text-center">Chưa có khách hàng</td></tr>
        <?php else: $stt = 1; foreach ($customers as $row): ?>
          <tr>
            <td><span class="badge bg-secondary"><?=$stt++?></span></td>
            <td><?=htmlspecialchars($row['ten'] ?? '')?></td>
            <td><?=htmlspecialchars($row['email'] ?? '')?></td>
            <td><?=htmlspecialchars($row['mat_khau'] ?? '')?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="<?=base_url('/admin/customers/edit')?>?id=<?=$row['id']?>">
                <i class="bi bi-pencil"></i> Sửa
              </a>
              <a class="btn btn-sm btn-danger" href="<?=base_url('/admin/customers')?>?delete=<?=$row['id']?>" onclick="return confirm('Xóa tài khoản khách hàng này?')">
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
