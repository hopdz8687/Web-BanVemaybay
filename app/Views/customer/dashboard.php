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
      background:
        linear-gradient(180deg, rgba(8, 24, 41, 0.22), rgba(8, 24, 41, 0.08)),
        url('<?=base_url('/assets/images/anh2.png')?>') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }

    .hero-shell {
      position: relative;
      overflow: hidden;
      width: min(100%, 1250px);
      min-height: 100px;
      margin: 0 auto;
      border-radius: 28px;
      border: 1px solid rgba(255, 255, 255, 0.28);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 24px 60px rgba(4, 35, 61, 0.18);
      backdrop-filter: blur(8px);
      display: flex;
    }

    .hero-copy {
      flex: 0 0 42%;
      padding: 24px 22px 20px 26px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      color: #fff;
      background:
        linear-gradient(135deg, rgba(7, 34, 63, 0.92), rgba(18, 90, 155, 0.55));
    }

    .hero-kicker {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.14);
      border: 1px solid rgba(255, 255, 255, 0.18);
      font-size: 0.88rem;
      font-weight: 600;
      margin-bottom: 10px;
      width: fit-content;
    }

    .flight-code {
      display: inline-block;
      padding: 6px 10px;
      font-size: 0.9rem;
      color: #ffffff;
      background: linear-gradient(135deg, #0E6B7E, #1B8FA0);
      border-radius: 8px;
      font-weight: 700;
      box-shadow: 0 6px 14px rgba(11, 45, 60, 0.12);
      border: 1px solid rgba(255,255,255,0.08);
      margin-bottom: 14px;
    }

    .hero-title {
      font-size: clamp(1.35rem, 2vw, 2.15rem);
      line-height: 1.06;
      font-weight: 800;
      margin-bottom: 8px;
      max-width: 360px;
    }

    .hero-subtitle {
      font-size: 0.84rem;
      line-height: 1.45;
      max-width: 360px;
      color: rgba(255, 255, 255, 0.92);
      margin-bottom: 10px;
    }

    .hero-stats {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 10px;
    }

    .hero-stat {
      padding: 7px 11px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.16);
      backdrop-filter: blur(10px);
      font-weight: 600;
      font-size: 0.78rem;
    }

    .hero-search {
      width: 100%;
    }

    .hero-search-panel {
      padding: 12px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.84);
      border: 1px solid rgba(255, 255, 255, 0.6);
      box-shadow: 0 18px 40px rgba(5, 25, 45, 0.18);
      backdrop-filter: blur(12px);
    }

    .hero-search .form-label {
      margin-bottom: 4px;
      font-size: 0.74rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .hero-search .form-control,
    .hero-search .btn {
      height: 46px;
      border-radius: 14px;
      font-size: 0.94rem;
    }

    .hero-search .form-control {
      border: 1px solid rgba(14, 107, 126, 0.18);
      box-shadow: none;
    }

    .hero-search .form-control:focus {
      border-color: #0E6B7E;
      box-shadow: 0 0 0 0.2rem rgba(14, 107, 126, 0.12);
    }

    .hero-search .btn {
      font-weight: 700;
    }

    .hero-media {
      flex: 1 1 auto;
      min-width: 0;
      position: relative;
    }

    .hero-carousel,
    .hero-carousel .carousel-inner,
    .hero-carousel .carousel-item {
      height: 100%;
      min-height: 270px;
    }

    .hero-carousel .carousel-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }

    .hero-indicators {
      position: absolute;
      right: 16px;
      bottom: 12px;
      left: auto;
      margin: 0;
      z-index: 3;
      gap: 8px;
    }

    .hero-indicators [data-bs-target] {
      width: 28px;
      height: 6px;
      border-radius: 999px;
      background-color: rgba(255, 255, 255, 0.55);
      border: 0;
      opacity: 1;
    }

    .hero-indicators .active {
      background-color: #ffffff;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 7%;
      z-index: 3;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.35));
    }

    .flight-card {
      transition: transform 0.2s, box-shadow 0.2s;
      border: none;
    }

    .section-heading {
      display: inline-flex;
      align-items: center;
      padding: 12px 22px 12px 24px;
      border-radius: 20px;
      background: linear-gradient(135deg, #0E6B7E, #1B8FA0);
      color: #ffffff;
      box-shadow: 0 12px 24px rgba(11, 45, 60, 0.18);
      border: 1px solid rgba(255, 255, 255, 0.18);
      margin: 0 0 24px 0;
      width: fit-content;
    }

    .section-heading h3 {
      margin: 0;
      font-size: 1.05rem;
      font-weight: 700;
      letter-spacing: 0.01em;
    }

    .flight-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .featured-section {
      width: min(100%, 1120px);
      margin: 0 auto;
    }

    .ticket-economy { color: #1e7e34; font-weight: 700; }
    .ticket-business { color: #0d6efd; font-weight: 700; }
    .ticket-price-line { line-height: 1.15; }

    @media (max-width: 991.98px) {
      .hero-shell {
        width: 100%;
        min-height: 440px;
        flex-direction: column;
      }

      .hero-copy {
        flex-basis: auto;
        padding: 20px;
      }

      .hero-title,
      .hero-subtitle {
        max-width: none;
      }

      .hero-media,
      .hero-carousel,
      .hero-carousel .carousel-inner,
      .hero-carousel .carousel-item {
        min-height: 220px;
      }

      .hero-search-panel {
        padding: 10px;
      }
    }

    @media (max-width: 767.98px) {
      .hero-shell {
        min-height: 500px;
      }

      .hero-copy {
        padding: 18px;
      }

      .hero-title {
        font-size: 1.6rem;
      }

      .hero-subtitle {
        display: none;
      }

      .hero-stats {
        display: none;
      }

      .hero-search .form-control,
      .hero-search .btn {
        height: 44px;
      }

      .hero-indicators {
        display: none;
      }

      .featured-section {
        width: 100%;
      }
    }
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

  <div class="hero-shell mb-5">
    <div class="hero-copy">
      <div class="hero-kicker">
        <i class="bi bi-stars"></i>
        Ưu đãi đặc biệt hôm nay
      </div>
      <h2 class="hero-title">Tìm vé bay nhanh, đẹp, và dễ đặt</h2>
      <p class="hero-subtitle">Khám phá các chuyến bay nội địa với giao diện tìm kiếm gọn, nằm ngay dưới phần chữ và bố cục ngang giống banner mẫu bạn gửi.</p>
      <div class="hero-stats">
        <div class="hero-stat">Giá tốt mỗi ngày</div>
        <div class="hero-stat">Đặt vé trong vài bước</div>
      </div>
      <div class="hero-search">
        <div class="hero-search-panel">
          <form method="get" action="<?=base_url('/customer/search')?>">
            <div class="row g-2 align-items-end">
              <div class="col-12 col-sm-3">
                <label class="form-label small text-muted fw-semibold">Nơi đi</label>
                <input name="noi_di" class="form-control" placeholder="Nơi đi" value="<?=htmlspecialchars($noiDiVal)?>">
              </div>
              <div class="col-12 col-sm-3">
                <label class="form-label small text-muted fw-semibold">Nơi đến</label>
                <input name="noi_den" class="form-control" placeholder="Nơi đến" value="<?=htmlspecialchars($noiDenVal)?>">
              </div>
              <div class="col-12 col-sm-3">
                <label class="form-label small text-muted fw-semibold">Ngày bay</label>
                <input type="date" name="ngay" class="form-control" value="<?=htmlspecialchars($ngayVal)?>">
              </div>
              <div class="col-12 col-sm-3">
                <button class="btn btn-primary w-100">
                  <i class="bi bi-search"></i> Tìm kiếm
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="hero-media">
      <div id="customerHero" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4200" data-bs-pause="false">
        <div class="carousel-indicators hero-indicators">
          <button type="button" data-bs-target="#customerHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#customerHero" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#customerHero" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="<?=base_url('/assets/images/anh2.png')?>" alt="Ưu đãi vé máy bay">
          </div>
          <div class="carousel-item">
            <img src="<?=base_url('/assets/images/anh1.png')?>" alt="Khám phá chuyến bay">
          </div>
          <div class="carousel-item">
            <img src="<?=base_url('/assets/images/anh10.jpeg')?>" alt="Đặt vé trực tuyến">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#customerHero" data-bs-slide="prev" aria-label="Ảnh trước">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#customerHero" data-bs-slide="next" aria-label="Ảnh tiếp theo">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>
  </div>

  <div class="featured-section">
  <div class="section-heading">
    <h3>Chuyến Bay Nổi Bật</h3>
  </div>
  <div class="row g-4">
    <?php if (empty($flights)): ?>
      <div class="col-12">
        <div class="alert alert-info">Không có chuyến bay nào.</div>
      </div>
    <?php else: ?>
      <?php foreach ($flights as $row): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card flight-card h-100">
          <div class="card-body">
            <div class="flight-code"><?=htmlspecialchars($row['so_hieu'])?></div>
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
              <i class="bi bi-ticket"></i> Đặt vé
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
