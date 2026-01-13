<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đặt vé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-image: url('<?=base_url('/assets/images/anh2.png')?>');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height:100vh;
    }
    .card:not(.bg-gradient){ background-color: rgba(255,255,255,0.95) !important; }
    .card-header.bg-light{ background-color: rgba(248,249,250,0.9) !important; }
    .container.py-4{ background: transparent; }
    .card.bg-gradient{ background: linear-gradient(135deg, #3a7bd5, #1f3b8f) !important; color: #fff; border: none; box-shadow: 0 8px 24px rgba(0,0,0,0.35); }
    .card.bg-gradient .card-body{ background: transparent; }
    /* Match ticket label typography to form labels/inputs */
    .ticket-economy,
    .ticket-business {
      color: #212529; /* default body text color */
      font-weight: 400; /* normal weight like form labels */
      font-size: 1rem; /* match form label/input size */
      line-height: 1.5;
      display: inline-block;
      vertical-align: middle;
    }
    /* Ensure radio/check labels inside passenger cards use same typography */
    .card .form-check-label.ticket-economy,
    .card .form-check-label.ticket-business,
    .card:not(.bg-gradient) .form-check-label.ticket-economy,
    .card:not(.bg-gradient) .form-check-label.ticket-business {
      color: #212529 !important;
      font-weight: 400 !important;
      font-size: 1rem !important;
      line-height: 1.5 !important;
    }
    /* Keep header (gradient) ticket labels styled like the small 'Còn: 45 ghế' text */
    .card.bg-gradient .ticket-economy,
    .card.bg-gradient .ticket-business {
      color: rgba(255,255,255,0.95) !important;
      font-weight: 400 !important;
      font-size: 0.875rem !important; /* match small text */
      line-height: 1.2 !important;
      vertical-align: baseline;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="mb-4">
    <h2><i class="bi bi-ticket-detailed"></i> Đặt Vé Chuyến Bay</h2>
    <div class="card bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
      <div class="card-body text-white">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h4 class="mb-0"><?=htmlspecialchars($flight['so_hieu'])?></h4>
            <small><?=htmlspecialchars($flight['noi_di'])?> → <?=htmlspecialchars($flight['noi_den'])?></small>
          </div>
            <div class="col-md-6 text-end">
            <p class="mb-0">Khởi hành: <strong><?=format_datetime($flight['gio_khoi_hanh'])?></strong></p>
            <small>
              <span class="ticket-economy">Giá vé thường: <?=number_format($flight['gia_thuong'],0)?> VND</span>
              &nbsp;|&nbsp;
              <span class="ticket-business">Giá vé thương gia: <?=number_format($flight['gia_thuong_gia'],0)?> VND</span>
              &nbsp;|&nbsp; Còn: <?=$flight['ghe_con']?> ghế
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8 mx-auto">
      <?php $step = $step ?? 'chon_ve'; ?>
      <?php if ($step === 'chon_ve'): ?>
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-1-circle"></i> Bước 1: Xem & Chọn Vé</h5>
          </div>
          <div class="card-body p-4">
            <?php if (empty($tickets)): ?>
              <div class="alert alert-warning mb-0">
                Chuyến bay này hiện chưa có loại vé để đặt. Vui lòng quay lại sau.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th>Mã vé</th>
                      <th>Hạng vé</th>
                      <th>Giá</th>
                      <th>Số lượng còn</th>
                      <th style="width: 150px;">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach (($tickets ?? []) as $t): ?>
                      <tr>
                        <td><strong><?=htmlspecialchars($t['ma_ve'] ?? '')?></strong></td>
                        <td><?=htmlspecialchars($t['hang_ve'] ?? '')?></td>
                        <td><strong><?=number_format((float)($t['gia'] ?? 0), 0)?> VND</strong></td>
                        <td><span class="badge bg-info"><?=htmlspecialchars((string)($t['so_luong_con'] ?? 0))?></span></td>
                        <td>
                          <form method="post" action="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$flight_id?>" class="m-0">
                            <input type="hidden" name="step" value="chon_ve">
                            <input type="hidden" name="chuyen_bay_id" value="<?=$flight_id?>">
                            <input type="hidden" name="ticket_id" value="<?=htmlspecialchars((string)($t['id'] ?? 0))?>">
                            <button type="submit" class="btn btn-sm btn-primary" <?=((int)($t['so_luong_con'] ?? 0) <= 0 || (int)$flight['ghe_con'] <= 0) ? 'disabled' : ''?>>
                              <i class="bi bi-check2-circle"></i> Chọn
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
            <div class="d-flex gap-2">
              <a href="<?=base_url('/customer/dashboard')?>" class="btn btn-lg btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Hủy
              </a>
            </div>
          </div>
        </div>

      <?php elseif ($step === 'chon_so_luong'): ?>
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-2-circle"></i> Bước 2: Chọn Số Lượng</h5>
          </div>
          <div class="card-body p-4">
            <?php $ticketId = (int)($selectedTicketId ?? 0); ?>
            <form method="post" action="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$flight_id?>">
              <input type="hidden" name="step" value="chon_so_luong">
              <input type="hidden" name="chuyen_bay_id" value="<?=$flight_id?>">
              <input type="hidden" name="ticket_id" value="<?=$ticketId?>">
              <div class="mb-3">
                <label class="form-label"><strong>Số lượng muốn đặt</strong></label>
                <input name="so_luong" type="number" min="1" value="<?=htmlspecialchars((string)($selectedQty ?? 1))?>" class="form-control form-control-lg" required>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-lg btn-primary flex-grow-1">
                  <i class="bi bi-arrow-right-circle"></i> Tiếp tục
                </button>
                <a href="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$flight_id?>" class="btn btn-lg btn-outline-secondary">
                  <i class="bi bi-arrow-left"></i> Quay lại
                </a>
              </div>
            </form>
          </div>
        </div>

      <?php else: ?>
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-3-circle"></i> Bước 3: Nhập Thông Tin Hành Khách</h5>
          </div>
          <div class="card-body p-4">
            <form method="post" action="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$flight_id?>">
              <input type="hidden" name="step" value="xac_nhan">
              <input type="hidden" name="chuyen_bay_id" value="<?=$flight_id?>">
              <?php $ticketId = (int)($selectedTicketId ?? 0); ?>
              <?php $soLuong = (int)($selectedQty ?? 1); ?>
              <input type="hidden" name="ticket_id" value="<?=$ticketId?>">
              <input type="hidden" name="so_luong" value="<?=$soLuong?>">

              <div class="card mb-3 border-start border-4" style="border-left-color: #667eea !important;">
                <div class="card-header bg-light">
                  <h6 class="mb-0"><i class="bi bi-people"></i> Thông tin hành khách</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Loại vé</label>
                    <div>
                      <span class="badge bg-secondary"><?=htmlspecialchars((string)($selectedClass ?? 'Thuong'))?></span>
                      &nbsp;—&nbsp;
                      <strong><?=number_format((float)($selectedPrice ?? 0), 0)?> VND</strong>
                    </div>
                  </div>

                  <?php for ($i = 0; $i < $soLuong; $i++): ?>
                    <div class="border rounded p-3 mb-3">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Hành khách #<?=($i+1)?></strong>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Họ tên</label>
                          <input name="hk_ten[]" type="text" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Điện thoại</label>
                          <input name="hk_dien_thoai[]" type="text" class="form-control form-control-lg" required>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Email</label>
                          <input name="hk_email[]" type="email" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-3 mb-3">
                          <label class="form-label">Giới tính</label>
                          <select name="hk_gioi_tinh[]" class="form-select form-select-lg">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                          </select>
                        </div>
                        <div class="col-md-3 mb-3">
                          <label class="form-label">Tuổi</label>
                          <input name="hk_tuoi[]" type="number" min="0" class="form-control form-control-lg" value="18" required>
                        </div>
                      </div>
                    </div>
                  <?php endfor; ?>
                </div>
              </div>
              <div class="alert alert-info mb-4">
                <div class="row">
                  <div class="col-md-6">
                    <small class="text-muted">SỐ VÉ ĐẶT</small>
                    <p class="h6"><span class="badge bg-info"><?=$soLuong?> vé</span></p>
                  </div>
                    <div class="col-md-6 text-end">
                    <small class="text-muted">TỔNG TIỀN</small>
                    <p class="h5 text-success mb-0"><strong><?=number_format((float)($total ?? 0), 0)?> VND</strong></p>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-lg btn-success flex-grow-1">
                  <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                </button>
                <a href="<?=base_url('/customer/book')?>?chuyen_bay_id=<?=$flight_id?>" class="btn btn-lg btn-outline-secondary">
                  <i class="bi bi-arrow-left"></i> Quay lại
                </a>
              </div>
              </form>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
