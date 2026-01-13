<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Xóa tài khoản</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-lg py-4">
  <div class="mb-3">
    <h3 class="fw-bold">Xóa tài khoản</h3>
    <div class="text-muted">Hành động này sẽ vô hiệu hóa tài khoản của bạn và đăng xuất ngay lập tức.</div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <div class="card border-danger">
    <div class="card-body">
      <h5 class="card-title text-danger">Cảnh báo</h5>
      <p class="card-text mb-3">
        Sau khi xóa, bạn sẽ không thể đăng nhập lại bằng email hiện tại.
      </p>

      <form method="post" action="<?=base_url('/customer/delete-account')?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản?');">
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="yes" id="confirm" name="confirm">
          <label class="form-check-label" for="confirm">
            Tôi hiểu và muốn xóa tài khoản
          </label>
        </div>

        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary" href="<?=base_url('/customer/dashboard')?>">Hủy</a>
          <button class="btn btn-danger" type="submit">Xóa tài khoản</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
