<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bảng quản trị</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.admin-bg {
      background: url('<?=base_url('/assets/images/anh2.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .dashboard-card {
      border: none;
      background: #ffffff;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.18s;
      color: #212529;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .card-icon { font-size: 3rem; }
    .dashboard-card .card-title, .dashboard-card .card-text { color: #212529; }
  </style>
</head>
<body class="admin-bg">
<div class="container-lg py-5">
  <div class="mb-5">
    <h2 class="text-dark fw-bold mb-2"><i class="bi bi-speedometer2"></i>Trang chủ</h2>
    <p class="text-muted">Quản lý chuyến bay và hành khách</p>
  </div>
  
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card dashboard-card h-100">
        <div class="card-body text-center p-5">
          <div class="card-icon text-primary mb-3">
            <i class="bi bi-airplane"></i>
          </div>
          <h5 class="card-title fw-bold mb-2">Quản Lý Chuyến Bay</h5>
          <p class="card-text text-muted mb-4">Tạo, sửa, xóa chuyến bay, quản lý số ghế và giá vé</p>
          <a href="<?=base_url('/admin/flights')?>" class="btn btn-primary btn-lg">
            <i class="bi bi-list"></i> Quản Lý
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card dashboard-card h-100">
        <div class="card-body text-center p-5">
          <div class="card-icon text-success mb-3">
            <i class="bi bi-people"></i>
          </div>
          <h5 class="card-title fw-bold mb-2">Đơn vé</h5>
          <p class="card-text text-muted mb-4">Xem danh sách khách hàng đã đặt vé, quản lý đặt vé</p>
          <a href="<?=base_url('/admin/bookings')?>" class="btn btn-success btn-lg">
            <i class="bi bi-bookmark-check"></i> Danh Sách
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card dashboard-card h-100">
        <div class="card-body text-center p-5">
          <div class="card-icon text-warning mb-3">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
          <h5 class="card-title fw-bold mb-2">Thống Kê Doanh Thu</h5>
          <p class="card-text text-muted mb-4">Xem doanh thu theo ngày, theo tháng và theo năm</p>
          <a href="<?=base_url('/admin/revenue')?>" class="btn btn-warning btn-lg text-white">
            <i class="bi bi-bar-chart"></i> Xem thống kê
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
