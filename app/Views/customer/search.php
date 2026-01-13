<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kết quả tìm kiếm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.search-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .search-container {
      background: rgba(255,255,255,0.95);
      border-radius: 8px;
      padding: 1rem;
      border: 1px solid rgba(0,0,0,0.06);
      color: #212529;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      /* ensure this main content area grows so footer stays at bottom on short pages */
      flex: 1 0 auto;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }
    .search-container h2 { font-weight: 500; color: inherit; }
    .ticket-economy { color: #1e7e34; font-weight: 700; display: inline-block; }
    .ticket-business { color: #0d6efd; font-weight: 700; display: inline-block; }
  </style>
</head>
<body class="search-bg">
<div class="container-lg py-4 search-container">
  <div class="mb-4">
    <h2><i class="bi bi-search"></i> Kết Quả Tìm Kiếm Chuyến Bay</h2>
    <p class="text-muted">
      <?php if ($noi_di || $noi_den || $ngay): ?>
        Kết quả tìm kiếm: 
        <?php if ($noi_di): ?><strong><?=htmlspecialchars($noi_di)?></strong><?php endif; ?>
        <?php if ($noi_den): ?>→ <strong><?=htmlspecialchars($noi_den)?></strong><?php endif; ?>
        <?php if ($ngay): ?> vào <strong><?=htmlspecialchars($ngay)?></strong><?php endif; ?>
      <?php endif; ?>
    </p>
    <a href="<?=base_url('/customer/dashboard')?>" class="btn btn-danger btn-sm">
      <i class="bi bi-arrow-left"></i> Quay Lại
    </a>
  </div>

  <?php if (empty($flights)): ?>
    <div class="alert alert-warning alert-dismissible fade show">
      <i class="bi bi-exclamation-triangle"></i> Không tìm thấy chuyến bay nào phù hợp với tiêu chí tìm kiếm.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>Chuyến bay</th>
            <th>Hãng bay</th>
            <th>Máy bay</th>
            <th>Tuyến bay</th>
            <th>Khởi hành</th>
            <th>Giá</th>
            <th>Ghế còn</th>
            <th>Thao Tac</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($flights as $row): ?>
            <tr>
              <td><strong><?=htmlspecialchars($row['so_hieu'])?></strong></td>
              <td><?=htmlspecialchars(($row['hang_may_bay'] ?? '') !== '' ? ($row['hang_may_bay'] ?? '') : 'Chưa gán')?></td>
              <td>
                <?php if (!empty($row['ma_may_bay']) || !empty($row['ten_may_bay'])): ?>
                  <?=htmlspecialchars(($row['ma_may_bay'] ?? '') . (!empty($row['ten_may_bay']) ? ' - ' . $row['ten_may_bay'] : ''))?>
                <?php else: ?>
                  <?=htmlspecialchars('Chưa gán')?>
                <?php endif; ?>
              </td>
              <td>
                <i class="bi bi-geo-alt"></i> <?=htmlspecialchars($row['noi_di'])?> 
                <span class="mx-0">→</span>
                <?=htmlspecialchars($row['noi_den'])?>
              </td>
              <td><?=format_datetime($row['gio_khoi_hanh'])?></td>
              <td>
                <div><span class="ticket-economy">Thường: <?=number_format($row['gia_thuong'],0)?> VND</span></div>
                <div><span class="ticket-business">Thương gia: <?=number_format($row['gia_thuong_gia'],0)?> VND</span></div>
              </td>
              <td><span class="badge bg-info"><?=$row['ghe_con']?></span></td>
              <td>
                <a class="btn btn-sm btn-primary" href="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$row['id']?>">
                  <i class="bi bi-ticket-detailed"></i> Đặt Vé
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
