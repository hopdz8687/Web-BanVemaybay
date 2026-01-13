<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thống kê doanh thu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.admin-bg {
      background: url('<?=base_url('/assets/images/anh2.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .card-soft {
      border: none;
      background: rgba(255,255,255,0.95);
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .table thead th { white-space: nowrap; }
  </style>
</head>
<body class="admin-bg">
<div class="container-lg py-4">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h3 class="fw-bold mb-1">Thống kê doanh thu</h3>
    </div>
    <a class="btn btn-outline-primary" href="<?=base_url('/admin/dashboard')?>">Về trang chủ</a>
  </div>

  <div class="card card-soft mb-3">
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-sm <?=($mode==='all'?'btn-primary':'btn-outline-primary')?>" href="<?=base_url('/admin/revenue?mode=all')?>">Tất cả</a>
        <a class="btn btn-sm <?=($mode==='day'?'btn-primary':'btn-outline-primary')?>" href="<?=base_url('/admin/revenue?mode=day&day=' . urlencode($day))?>">Theo ngày</a>
        <a class="btn btn-sm <?=($mode==='month'?'btn-primary':'btn-outline-primary')?>" href="<?=base_url('/admin/revenue?mode=month&month=' . urlencode($month))?>">Theo tháng</a>
        <a class="btn btn-sm <?=($mode==='year'?'btn-primary':'btn-outline-primary')?>" href="<?=base_url('/admin/revenue?mode=year&year=' . urlencode((string)$year))?>">Theo năm</a>
      </div>

      <form class="row g-2 align-items-end" method="get" action="<?=base_url('/admin/revenue')?>">
        <input type="hidden" name="mode" value="<?=htmlspecialchars($mode)?>">

        <?php if ($mode === 'all'): ?>
          <div class="col-sm-7">
            <div class="text-muted">Đang xem tổng doanh thu mọi thời gian</div>
          </div>
        <?php elseif ($mode === 'day'): ?>
          <div class="col-sm-4">
            <label class="form-label">Chọn ngày</label>
            <input type="date" class="form-control" name="day" value="<?=htmlspecialchars($day)?>">
          </div>
        <?php elseif ($mode === 'month'): ?>
          <div class="col-sm-4">
            <label class="form-label">Chọn tháng</label>
            <input type="month" class="form-control" name="month" value="<?=htmlspecialchars($month)?>">
          </div>
        <?php else: ?>
          <div class="col-sm-4">
            <label class="form-label">Chọn năm</label>
            <input type="number" class="form-control" name="year" min="2000" max="2100" value="<?=htmlspecialchars((string)$year)?>">
          </div>
        <?php endif; ?>

        <?php if ($mode !== 'all'): ?>
          <div class="col-sm-3">
            <button class="btn btn-success w-100" type="submit">Xem thống kê</button>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card card-soft h-100">
        <div class="card-body">
          <div class="text-muted">Tổng doanh thu</div>
          <div class="fs-4 fw-bold text-success">
            <?=number_format((float)($summary['total_revenue'] ?? 0), 0, ',', '.')?> đ
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-soft h-100">
        <div class="card-body">
          <div class="text-muted">Số đơn đã thanh toán</div>
          <div class="fs-4 fw-bold">
            <?= (int)($summary['total_bookings'] ?? 0) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-soft h-100">
        <div class="card-body">
          <div class="text-muted">Tổng ghế (vé) đã đặt</div>
          <div class="fs-4 fw-bold">
            <?= (int)($summary['total_seats'] ?? 0) ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($mode === 'all'): ?>
    <div class="card card-soft">
      <div class="card-header bg-transparent fw-semibold">Doanh thu theo năm (tất cả thời gian)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Năm</th>
                <th>Doanh thu</th>
                <th>Số đơn</th>
                <th>Số ghế (vé)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($breakdown)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
              <?php else: ?>
                <?php foreach ($breakdown as $row): ?>
                  <tr>
                    <td><?= (int)($row['period_year'] ?? 0) ?></td>
                    <td class="fw-semibold text-success"><?= number_format((float)($row['total_revenue'] ?? 0), 0, ',', '.') ?> đ</td>
                    <td><?= (int)($row['total_bookings'] ?? 0) ?></td>
                    <td><?= (int)($row['total_seats'] ?? 0) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  <?php elseif ($mode === 'day'): ?>
    <div class="card card-soft">
      <div class="card-header bg-transparent fw-semibold">Chi tiết đơn đã thanh toán trong ngày <?=htmlspecialchars($day)?></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Khách hàng</th>
                <th>Email</th>
                <th>Chuyến</th>
                <th>Tuyến</th>
                <th>Ghế (vé)</th>
                <th>Tổng tiền</th>
                <th>Thanh toán lúc</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bookings)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Không có đơn paid trong ngày này</td></tr>
              <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                  <tr>
                    <td><?= (int)$b['id'] ?></td>
                    <td><?= htmlspecialchars($b['customer_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($b['customer_email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($b['so_hieu'] ?? '') ?></td>
                    <td><?= htmlspecialchars(($b['noi_di'] ?? '') . ' → ' . ($b['noi_den'] ?? '')) ?></td>
                    <td><?= (int)($b['so_ghe_dat'] ?? 0) ?></td>
                    <td class="fw-semibold text-success"><?= number_format((float)($b['tong_tien'] ?? 0), 0, ',', '.') ?> đ</td>
                    <td><?= htmlspecialchars(format_datetime($b['thanh_toan_luc'] ?? $b['dat_luc'] ?? '')) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php elseif ($mode === 'month'): ?>
    <div class="card card-soft">
      <div class="card-header bg-transparent fw-semibold">Doanh thu theo ngày trong tháng <?=htmlspecialchars($month)?></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Ngày</th>
                <th>Doanh thu</th>
                <th>Số đơn</th>
                <th>Số ghế (vé)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($breakdown)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
              <?php else: ?>
                <?php foreach ($breakdown as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['period_day'] ?? '') ?></td>
                    <td class="fw-semibold text-success"><?= number_format((float)($row['total_revenue'] ?? 0), 0, ',', '.') ?> đ</td>
                    <td><?= (int)($row['total_bookings'] ?? 0) ?></td>
                    <td><?= (int)($row['total_seats'] ?? 0) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="card card-soft">
      <div class="card-header bg-transparent fw-semibold">Doanh thu theo tháng trong năm <?=htmlspecialchars((string)$year)?></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Tháng</th>
                <th>Doanh thu</th>
                <th>Số đơn</th>
                <th>Số ghế (vé)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($breakdown)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
              <?php else: ?>
                <?php foreach ($breakdown as $row): ?>
                  <tr>
                    <td><?= (int)($row['period_month'] ?? 0) ?></td>
                    <td class="fw-semibold text-success"><?= number_format((float)($row['total_revenue'] ?? 0), 0, ',', '.') ?> đ</td>
                    <td><?= (int)($row['total_bookings'] ?? 0) ?></td>
                    <td><?= (int)($row['total_seats'] ?? 0) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
