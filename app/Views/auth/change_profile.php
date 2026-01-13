<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đổi mật khẩu / Tài khoản</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.customer-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .profile-container { background: transparent; border-radius: 8px; padding: 1rem; border: 1px solid rgba(255,255,255,0.06); }
  </style>
</head>
<body class="customer-bg">
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Cập nhật thông tin</h5>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
          <?php if (!empty($success_msg)): ?><div class="alert alert-success"><?=htmlspecialchars($success_msg)?></div><?php endif; ?>

          <form method="post" action="<?=base_url('/auth/change-profile')?>">
            <div class="mb-3">
              <label class="form-label">Họ và tên</label>
              <input name="name" class="form-control" value="<?=htmlspecialchars((string)($currentUserName ?? ''))?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" value="<?=htmlspecialchars((string)($currentUserEmail ?? ''))?>">
            </div>
            <hr>
            <div class="mb-3">
              <label class="form-label">Mật khẩu hiện tại (bắt buộc khi đổi email hoặc mật khẩu)</label>
              <input name="current_password" type="password" class="form-control" placeholder="Mật khẩu hiện tại">
            </div>
            <div class="mb-3">
              <label class="form-label">Mật khẩu mới</label>
              <input name="new_password" type="password" class="form-control" placeholder="Mật khẩu mới">
            </div>
            <div class="mb-3">
              <label class="form-label">Xác nhận mật khẩu mới</label>
              <input name="confirm_password" type="password" class="form-control" placeholder="Xác nhận mật khẩu mới">
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary flex-grow-1" type="submit">Lưu thay đổi</button>
              <a class="btn btn-outline-secondary" href="<?= !empty($isAdmin) ? base_url('/admin/dashboard') : base_url('/customer/dashboard') ?>">Hủy</a>
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
