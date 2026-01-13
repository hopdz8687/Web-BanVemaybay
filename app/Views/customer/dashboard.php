<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tìm và đặt vé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.customer-bg {
      background: url('<?=base_url('/assets/images/anh2.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .search-section {
      background: rgba(255,255,255,0.15);
      color: white;
      padding: 40px 0;
      border-radius: 10px;
      backdrop-filter: blur(4px);
      border: 1px solid rgba(255,255,255,0.08);
    }
    .flight-card { transition: transform 0.2s, box-shadow 0.2s; border: none; }
    .flight-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    .ticket-economy { color: #1e7e34; font-weight: 700; }
    .ticket-business { color: #0d6efd; font-weight: 700; }
    .ticket-price-line { line-height: 1.15; }
  </style>
</head>
<body class="customer-bg">
<div class="container-lg py-4">
  <?php
    $search = $search ?? [];
    $noiDiVal = (string)($search['noi_di'] ?? '');
    $noiDenVal = (string)($search['noi_den'] ?? '');
    $ngayVal = (string)($search['ngay'] ?? '');
  ?>
  <div class="search-section mb-5">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h2 class="mb-2">Tìm Chuyến Bay</h2>
        <p>Đặt vé máy bay trên toàn quốc</p>
      </div>
    </div>
    <form method="get" action="<?=base_url('/customer/search')?>" class="mt-4">
      <div class="row g-2">
        <div class="col-md-3">
          <input name="noi_di" class="form-control form-control-lg" placeholder="Nơi đi" value="<?=htmlspecialchars($noiDiVal)?>">
        </div>
        <div class="col-md-3">
          <input name="noi_den" class="form-control form-control-lg" placeholder="Nơi đến" value="<?=htmlspecialchars($noiDenVal)?>">
        </div>
        <div class="col-md-3">
          <input type="date" name="ngay" class="form-control form-control-lg" value="<?=htmlspecialchars($ngayVal)?>">
        </div>
        <div class="col-md-3">
          <button class="btn btn-light btn-lg w-100">
            <i class="bi bi-search"></i> Tìm kiếm
          </button>
        </div>
      </div>
    </form>
  </div>

  <h3 class="mb-4">Chuyến Bay Nổi Bật</h3>
  <div class="row g-4">
    <?php if (empty($flights)): ?>
      <div class="col-12">
        <div class="alert alert-info">Không có chuyến bay nào.</div>
      </div>
    <?php else: ?>
      <?php foreach ($flights as $row): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card flight-card h-100">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0 fw-bold"><?=htmlspecialchars($row['so_hieu'])?></h6>
          </div>
          <div class="card-body">
            <h5 class="card-title mb-3">
              <?=htmlspecialchars($row['noi_di'])?> 
              &rarr; 
              <?=htmlspecialchars($row['noi_den'])?>
            </h5>
            <p class="card-text">
              <small class="text-muted">
                <strong>Hãng bay:</strong> <?=htmlspecialchars(($row['hang_may_bay'] ?? '') !== '' ? ($row['hang_may_bay'] ?? '') : 'Chưa gán')?>
                <br>
                <strong>Máy bay:</strong>
                <?php if (!empty($row['ma_may_bay']) || !empty($row['ten_may_bay'])): ?>
                  <?=htmlspecialchars(($row['ma_may_bay'] ?? '') . (!empty($row['ten_may_bay']) ? ' - ' . $row['ten_may_bay'] : ''))?>
                <?php else: ?>
                  <?=htmlspecialchars('Chưa gán')?>
                <?php endif; ?>
                <br>
                <strong>Khởi hành:</strong> <?=format_datetime($row['gio_khoi_hanh'])?><br>
                <strong>Hạ cánh:</strong> <?=format_datetime($row['gio_ha_canh'])?><br>
                <strong>Giá:</strong><br>
                <span class="ticket-economy ticket-price-line d-block">Thường: <?=number_format($row['gia_thuong'],0)?> VND</span>
                <span class="ticket-business ticket-price-line d-block">Thương gia: <?=number_format($row['gia_thuong_gia'],0)?> VND</span>
                <strong>Ghế còn lại:</strong> <?=$row['ghe_con']?> ghế
              </small>
            </p>
          </div>
          <div class="card-footer bg-light">
            <a href="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$row['id']?>" class="btn btn-primary w-100">
              <i class="bi bi-ticket"></i> Đặt Vé
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
