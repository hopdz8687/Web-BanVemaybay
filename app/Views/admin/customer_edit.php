<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sửa khách hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-image: url('<?=base_url('/assets/images/anh1.png')?>');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height:100vh;
    }
    .card:not(.bg-gradient){ background-color: rgba(255,255,255,0.95) !important; }
    .card.bg-gradient{ background: linear-gradient(135deg, #3a7bd5, #1f3b8f) !important; color: #fff; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.25); }
    .container.py-4{ background: transparent; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2);">
          <h4 class="text-dark mb-0"><i class="bi bi-pencil-square"></i> Sửa Khách Hàng</h4>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><strong>Lỗi!</strong> <?=htmlspecialchars($error)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
          <form method="post" action="<?=base_url('/admin/customers/edit')?>?id=<?=$customer['id']?>">
            <div class="mb-3">
              <label class="form-label">Tên</label>
              <input type="text" name="ten" class="form-control form-control-lg" value="<?=htmlspecialchars($customer['ten'] ?? '')?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control form-control-lg" value="<?=htmlspecialchars($customer['email'] ?? '')?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Mật khẩu</label>
              <input type="text" name="mat_khau" class="form-control form-control-lg" value="<?=htmlspecialchars($customer['mat_khau'] ?? '')?>" required>
            </div>
            <div class="d-flex gap-2 pt-3">
              <button type="submit" class="btn btn-lg btn-primary flex-grow-1">
                <i class="bi bi-check-circle"></i> Lưu Thay Đổi
              </button>
              <a href="<?=base_url('/admin/customers')?>" class="btn btn-lg btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Hủy
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
