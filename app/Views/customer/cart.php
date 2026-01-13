<?php require_once __DIR__ . '/../partials/navbar.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Giỏ hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.customer-bg {
      background: url('<?=base_url('/assets/images/anh1.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .page-wrap {
      background: rgba(255,255,255,0.94);
      border-radius: 8px;
      padding: 1rem;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
  </style>
</head>
<body class="customer-bg">
<div class="container-lg py-4 page-wrap">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-cart"></i> Giỏ hàng</h2>
    <div class="d-flex gap-2">
      <a href="<?=base_url('/customer/search')?>" class="btn btn-outline-primary">
        <i class="bi bi-search"></i> Tìm chuyến bay
      </a>
      <a href="<?=base_url('/customer/my-tickets')?>" class="btn btn-outline-success">
        <i class="bi bi-ticket"></i> Vé của tôi
      </a>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?=htmlspecialchars($message)?></div>
  <?php endif; ?>

  <?php if (empty($cartBookings)): ?>
    <div class="alert alert-warning">
      Giỏ hàng đang trống.
    </div>
  <?php else: ?>
    <form method="post" action="<?=base_url('/customer/checkout')?>">
      <input type="hidden" name="step" value="select">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width: 40px;"><input class="form-check-input" type="checkbox" id="chkAll"></th>
              <th>Mã đặt</th>
              <th>Chuyến bay</th>
              <th>Mã vé</th>
              <th>Loại vé</th>
              <th>Số lượng</th>
              <th>Tổng tiền</th>
              <th style="width: 230px;">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartBookings as $b): ?>
              <tr>
                <td>
                  <input class="form-check-input chkOne" type="checkbox" name="booking_ids[]" value="<?=htmlspecialchars((string)$b['id'])?>" data-amount="<?=htmlspecialchars((string)((float)($b['tong_tien'] ?? 0)))?>">
                </td>
                <td><span class="badge bg-secondary">#<?=htmlspecialchars((string)$b['id'])?></span></td>
                <td>
                  <strong><?=htmlspecialchars($b['so_hieu'] ?? '')?></strong><br>
                  <small class="text-muted"><?=htmlspecialchars($b['noi_di'] ?? '')?> → <?=htmlspecialchars($b['noi_den'] ?? '')?></small>
                </td>
                <td><strong><?=htmlspecialchars(($b['ma_ve'] ?? '') !== '' ? ($b['ma_ve'] ?? '') : '—')?></strong></td>
                <td><?=htmlspecialchars(($b['hang_ve'] ?? '') !== '' ? ($b['hang_ve'] ?? '') : '—')?></td>
                <td><span class="badge bg-info"><?=htmlspecialchars((string)($b['so_ghe_dat'] ?? 0))?></span></td>
                <td><strong class="text-success"><?=number_format((float)($b['tong_tien'] ?? 0), 0)?> VND</strong></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="<?=base_url('/customer/cart/edit')?>?booking_id=<?=$b['id']?>">
                    <i class="bi bi-pencil"></i> Sửa
                  </a>
                  <a class="btn btn-sm btn-danger" href="<?=base_url('/customer/cart')?>?remove=<?=$b['id']?>" onclick="return confirm('Xóa khỏi giỏ hàng?')">
                    <i class="bi bi-trash"></i> Xóa
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
          <small class="text-muted">Tổng thanh toán (đã chọn)</small>
          <div class="h5 mb-0"><strong id="selectedTotal">0 VND</strong></div>
          <div class="small text-muted">Tổng trong giỏ: <strong><?=number_format((float)($cartGrandTotal ?? 0), 0)?> VND</strong></div>
        </div>
        <button id="btnCheckout" type="submit" class="btn btn-lg btn-success" onclick="return confirm('Thanh toán các đơn đã chọn?')" disabled>
          <i class="bi bi-credit-card"></i> Thanh toán
        </button>
      </div>
    </form>

    <script>
      (function(){
        var all = document.getElementById('chkAll');
        if(!all) return;
        var ones = Array.prototype.slice.call(document.querySelectorAll('.chkOne'));
        var totalEl = document.getElementById('selectedTotal');
        var btn = document.getElementById('btnCheckout');

        function formatVND(n){
          try { return Number(n).toLocaleString('en-US') + ' VND'; } catch(e) { return n + ' VND'; }
        }

        function recalc(){
          var sum = 0;
          var checkedCount = 0;
          ones.forEach(function(c){
            if(c.checked){
              checkedCount++;
              sum += Number(c.getAttribute('data-amount') || 0);
            }
          });
          if(totalEl) totalEl.textContent = formatVND(sum);
          if(btn) btn.disabled = checkedCount === 0;
        }

        all.addEventListener('change', function(){
          ones.forEach(function(c){ c.checked = all.checked; });
          recalc();
        });

        ones.forEach(function(c){
          c.addEventListener('change', function(){
            // keep chkAll in sync
            var every = ones.length > 0 && ones.every(function(x){ return x.checked; });
            all.checked = every;
            recalc();
          });
        });

        recalc();
      })();
    </script>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
