<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.auth-bg {
      background: url('<?=base_url('/assets/images/anh2.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .login-container { min-height: 100vh; display: flex; align-items: center; }
    .login-card { box-shadow: 0 0 20px rgba(0,0,0,0.12); border: none; background: rgba(255,255,255,0.92); }
  </style>
</head>
<body class="auth-bg">
<div class="login-container">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card login-card">
          <div class="card-body p-5">
            <h2 class="card-title text-center mb-4" style="color: #0d42f2;">Đăng Nhập</h2>
            <?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
            <form method="post" action="<?=base_url('/auth/login')?>">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control form-control-lg" placeholder="Nhập email của bạn" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input name="password" type="password" class="form-control form-control-lg" placeholder="Nhập mật khẩu" required>
              </div>
              <button class="btn btn-primary btn-lg w-100 mb-3" style="background-color: #086d0f; border-color: #cb0929;">Đăng Nhập</button>
            </form>
            <hr>
            <p class="text-center text-muted mb-0">Chưa có tài khoản? <a href="<?=base_url('/auth/register')?>" class="text-primary fw-bold">Đăng ký ngay</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
