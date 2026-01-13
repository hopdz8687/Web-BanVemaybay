<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thêm Chuyến Bay</title>
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
          <h4 class="text-dark mb-0"><i class="bi bi-plus-circle"></i> Thêm Chuyến Bay Mới</h4>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show"><strong>Loi!</strong> <?=htmlspecialchars($error)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
          <form method="post" action="<?=base_url('/admin/flights/create')?>">
            <div class="mb-3">
              <label class="form-label">Số hiệu chuyến bay</label>
              <input type="text" name="so_hieu" class="form-control form-control-lg" required>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Hãng máy bay</label>
                <select id="airlineSelect" class="form-select form-select-lg" <?=empty($planes) ? 'disabled' : ''?>>
                  <option value="">-- Chọn hãng --</option>
                  <option value="Vietnam Airlines">Vietnam Airlines</option>
                  <option value="Vietjet Air">Vietjet Air</option>
                  <option value="Bamboo Airways">Bamboo Airways</option>
                  <option value="Khác">Khác</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Máy bay</label>
                <select id="planeSelect" name="may_bay_id" class="form-select form-select-lg" <?=empty($planes) ? 'disabled' : ''?>>
                  <option value=""><?=empty($planes) ? 'Chưa có máy bay (hãy thêm máy bay trước)' : '-- Chọn máy bay --'?></option>
                  <?php if (!empty($planes)): foreach ($planes as $p): ?>
                    <option value="<?=$p['id']?>" data-airline="<?=htmlspecialchars($p['hang_may_bay'] ?? 'Khác')?>">
                      <?=htmlspecialchars($p['ma_may_bay'] . ' - ' . $p['ten_may_bay'])?>
                    </option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Điểm xuất phát</label>
                <input type="text" name="noi_di" class="form-control form-control-lg" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Điểm đến</label>
                <input type="text" name="noi_den" class="form-control form-control-lg" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Thời gian khởi hành</label>
                <input type="datetime-local" name="gio_khoi_hanh" class="form-control form-control-lg" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Thời gian đến</label>
                <input type="datetime-local" name="gio_ha_canh" class="form-control form-control-lg" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Giá vé thường (VND)</label>
                <input type="number" name="gia_thuong" class="form-control form-control-lg" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Giá vé thương gia (VND)</label>
                <input type="number" name="gia_thuong_gia" class="form-control form-control-lg" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Số ghế</label>
                <input type="number" name="ghe_con" class="form-control form-control-lg" required>
              </div>
            </div>
            <div class="d-flex gap-2 pt-3">
              <button type="submit" class="btn btn-lg btn-success flex-grow-1">
                <i class="bi bi-check-circle"></i> Tạo Chuyến Bay
              </button>
              <a href="<?=base_url('/admin/flights')?>" class="btn btn-lg btn-outline-secondary">
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

<script>
  (function(){
    var airlineSelect = document.getElementById('airlineSelect');
    var planeSelect = document.getElementById('planeSelect');
    if (!airlineSelect || !planeSelect) return;

    var allPlaneOptions = Array.prototype.slice.call(planeSelect.querySelectorAll('option'));
    var placeholder = allPlaneOptions[0];

    function filterPlanes(){
      var airline = airlineSelect.value;
      planeSelect.innerHTML = '';
      planeSelect.appendChild(placeholder);

      allPlaneOptions.slice(1).forEach(function(opt){
        var optAirline = opt.getAttribute('data-airline') || 'Khác';
        if (!airline || optAirline === airline) {
          planeSelect.appendChild(opt);
        }
      });

      planeSelect.value = '';
    }

    airlineSelect.addEventListener('change', filterPlanes);
  })();
</script>
</body>
</html>
