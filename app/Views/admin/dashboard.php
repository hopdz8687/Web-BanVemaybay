<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trang chủ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root {
      --admin-teal: #0e6b7e;
      --admin-teal-dark: #063e4e;
      --admin-gold: #f4b740;
      --admin-ink: #102733;
      --admin-muted: #657783;
      --admin-line: rgba(255, 255, 255, .28);
    }

    body.admin-bg {
      min-height: 100vh;
      background: url('<?= base_url('/assets/images/anh2.png') ?>') center / cover fixed no-repeat;
      color: var(--admin-ink);
      overflow-x: hidden;
    }

    body.admin-bg::before {
      content: "";
      position: fixed;
      inset: 0;
      pointer-events: none;
      background: radial-gradient(circle at 88% 4%, rgba(255, 255, 255, .24), transparent 28%);
      z-index: -1;
    }

    .navbar {
      margin-bottom: 0 !important;
    }

    .admin-shell {
      width: min(1180px, calc(100% - 32px));
      margin: 0 auto;
      padding: 44px 0 58px;
    }

    .hero-panel {
      position: relative;
      overflow: hidden;
      border: 1px solid var(--admin-line);
      border-radius: 8px;
      background: rgba(255, 255, 255, .76);
      box-shadow: 0 22px 70px rgba(4, 37, 49, .28);
      backdrop-filter: blur(18px);
      animation: riseIn .55s ease both;
    }

    .hero-panel::after {
      content: "";
      position: absolute;
      inset: auto -12% -42% 45%;
      height: 260px;
      background: linear-gradient(90deg, rgba(14, 107, 126, .14), rgba(244, 183, 64, .2));
      transform: rotate(-8deg);
    }

    .hero-content {
      position: relative;
      z-index: 1;
      padding: clamp(26px, 5vw, 48px);
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 12px;
      border: 1px solid rgba(14, 107, 126, .16);
      border-radius: 999px;
      background: rgba(255, 255, 255, .7);
      color: var(--admin-teal-dark);
      font-size: 13px;
      font-weight: 700;
    }

    .hero-title {
      margin: 18px 0 10px;
      color: var(--admin-ink);
      font-size: clamp(32px, 5vw, 56px);
      font-weight: 800;
      letter-spacing: 0;
      line-height: 1.05;
    }

    .hero-subtitle {
      max-width: 620px;
      color: #48606d;
      font-size: 17px;
      line-height: 1.7;
      margin-bottom: 26px;
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .btn-dashboard {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      min-height: 46px;
      padding: 10px 18px;
      border-radius: 8px;
      border: 1px solid transparent;
      font-weight: 700;
      text-decoration: none;
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .btn-dashboard:hover {
      transform: translateY(-2px);
    }

    .btn-dashboard.primary {
      background: linear-gradient(135deg, var(--admin-teal), #1293a8);
      color: #fff !important;
      box-shadow: 0 14px 28px rgba(14, 107, 126, .28);
    }

    .btn-dashboard.primary i {
      color: #fff !important;
    }

    .btn-dashboard.light {
      background: rgba(255, 255, 255, .72);
      border-color: rgba(14, 107, 126, .2);
      color: var(--admin-teal-dark);
    }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      margin: 22px 0;
    }

    .stat-card,
    .action-card,
    .status-panel {
      border: 1px solid rgba(255, 255, 255, .5);
      border-radius: 8px;
      background: rgba(255, 255, 255, .86);
      box-shadow: 0 18px 45px rgba(4, 37, 49, .16);
      backdrop-filter: blur(14px);
      animation: riseIn .55s ease both;
    }

    .stat-card {
      display: block;
      padding: 18px;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
      text-decoration: none;
      color: inherit;
    }

    .stat-card:hover,
    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 24px 55px rgba(4, 37, 49, .22);
      border-color: rgba(14, 107, 126, .3);
    }

    .stat-card:hover,
    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 24px 55px rgba(4, 37, 49, .22);
    }

    .stat-icon {
      width: 44px;
      height: 44px;
      display: grid;
      place-items: center;
      border-radius: 8px;
      color: #fff;
      background: linear-gradient(135deg, var(--admin-teal), #19a4b6);
      box-shadow: 0 12px 20px rgba(14, 107, 126, .22);
      margin-bottom: 14px;
      font-size: 20px;
    }

    .stat-label {
      color: var(--admin-muted);
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .stat-value {
      color: var(--admin-ink);
      font-size: 24px;
      font-weight: 800;
      line-height: 1.2;
    }

    .section-heading {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 18px;
      margin: 32px 0 16px;
      color: #fff;
    }

    .section-heading h2 {
      margin: 0;
      font-size: 26px;
      font-weight: 800;
      text-shadow: 0 8px 24px rgba(0, 0, 0, .18);
    }

    .section-heading p {
      margin: 6px 0 0;
      color: rgba(255, 255, 255, .78);
    }

    .action-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .action-card {
      position: relative;
      min-height: 220px;
      padding: 24px;
      overflow: hidden;
      color: inherit;
      text-decoration: none;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .action-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--accent), rgba(255, 255, 255, 0));
    }

    .action-card::after {
      content: "";
      position: absolute;
      right: -46px;
      bottom: -46px;
      width: 140px;
      height: 140px;
      border-radius: 999px;
      background: color-mix(in srgb, var(--accent) 18%, transparent);
    }

    .action-icon {
      width: 54px;
      height: 54px;
      display: grid;
      place-items: center;
      border-radius: 8px;
      background: color-mix(in srgb, var(--accent) 14%, white);
      color: var(--accent);
      font-size: 26px;
      margin-bottom: 22px;
    }

    .action-title {
      color: var(--admin-ink);
      font-size: 20px;
      font-weight: 800;
      margin-bottom: 10px;
    }

    .action-text {
      color: var(--admin-muted);
      line-height: 1.65;
      margin-bottom: 22px;
    }

    .action-link {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--accent);
      font-weight: 800;
    }

    .status-panel {
      margin-top: 18px;
      padding: 22px 24px;
    }

    .status-row {
      display: grid;
      grid-template-columns: 1fr auto;
      align-items: center;
      gap: 16px;
      padding: 14px 0;
      border-bottom: 1px solid rgba(14, 107, 126, .1);
    }

    .status-row:last-child {
      border-bottom: 0;
    }

    .status-name {
      font-weight: 800;
      color: var(--admin-ink);
      margin-bottom: 3px;
    }

    .status-desc {
      color: var(--admin-muted);
      font-size: 14px;
    }

    .status-badge {
      border-radius: 999px;
      padding: 7px 11px;
      background: rgba(14, 107, 126, .1);
      color: var(--admin-teal-dark);
      font-size: 13px;
      font-weight: 800;
      white-space: nowrap;
    }

    @keyframes riseIn {
      from {
        opacity: 0;
        transform: translateY(18px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 991.98px) {
      .stat-grid,
      .action-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 575.98px) {
      .admin-shell {
        width: min(100% - 22px, 1180px);
        padding-top: 22px;
      }

      .hero-content,
      .action-card,
      .status-panel {
        padding: 20px;
      }

      .stat-grid,
      .action-grid {
        grid-template-columns: 1fr;
      }

      .section-heading {
        display: block;
      }

      .status-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="admin-bg">
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<main class="admin-shell">
  <section class="hero-panel">
    <div class="hero-content">
      <span class="eyebrow"><i class="bi bi-stars"></i> Trang chủ</span>
      <h1 class="hero-title">Điều phối chuyến bay gọn gàng hơn.</h1>
      <p class="hero-subtitle">
        Theo dõi vận hành, quản lý chuyến bay, đơn vé và doanh thu trong một không gian hiện đại, rõ ràng và dễ thao tác.
      </p>
      <div class="hero-actions">
        <a href="<?= base_url('/admin/flights') ?>" class="btn-dashboard primary">
          <i class="bi bi-airplane"></i> Quản lý chuyến bay
        </a>
        <a href="<?= base_url('/admin/revenue') ?>" class="btn-dashboard light">
          <i class="bi bi-graph-up-arrow"></i> Xem doanh thu
        </a>
      </div>
    </div>
  </section>

  <section class="stat-grid" aria-label="Tổng quan nhanh">
    <a href="<?= base_url('/admin/flights') ?>" class="stat-card" style="animation-delay: .04s">
      <div class="stat-icon"><i class="bi bi-airplane-engines"></i></div>
      <div class="stat-label">Chuyến bay</div>
      <div class="stat-value">Quản lý</div>
    </a>
    <a href="<?= base_url('/admin/bookings') ?>" class="stat-card" style="animation-delay: .08s">
      <div class="stat-icon"><i class="bi bi-ticket-perforated"></i></div>
      <div class="stat-label">Đơn vé</div>
      <div class="stat-value">Theo dõi</div>
    </a>
    <a href="<?= base_url('/admin/customers') ?>" class="stat-card" style="animation-delay: .12s">
      <div class="stat-icon"><i class="bi bi-people"></i></div>
      <div class="stat-label">Khách hàng</div>
      <div class="stat-value">Chăm sóc</div>
    </a>
    <a href="<?= base_url('/admin/revenue') ?>" class="stat-card" style="animation-delay: .16s">
      <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
      <div class="stat-label">Doanh thu</div>
      <div class="stat-value">Báo cáo</div>
    </a>
  </section>

  <div class="section-heading">
    <div>
      <h2>Truy cập nhanh</h2>
      <p>Các khu vực quản trị thường dùng trong hệ thống bán vé máy bay.</p>
    </div>
  </div>

  <section class="action-grid">
    <a href="<?= base_url('/admin/flights') ?>" class="action-card" style="--accent:#0e6b7e; animation-delay:.05s">
      <div class="action-icon"><i class="bi bi-airplane"></i></div>
      <div class="action-title">Quản lý chuyến bay</div>
      <p class="action-text">Tạo, sửa, xóa chuyến bay, cập nhật số ghế và giá vé theo từng hành trình.</p>
      <span class="action-link">Mở danh sách <i class="bi bi-arrow-right"></i></span>
    </a>

    <a href="<?= base_url('/admin/bookings') ?>" class="action-card" style="--accent:#1a9b75; animation-delay:.1s">
      <div class="action-icon"><i class="bi bi-bookmark-check"></i></div>
      <div class="action-title">Đơn vé</div>
      <p class="action-text">Xem khách hàng đã đặt vé, trạng thái đơn và thông tin đặt chỗ cần xử lý.</p>
      <span class="action-link">Xem đơn vé <i class="bi bi-arrow-right"></i></span>
    </a>

    <a href="<?= base_url('/admin/revenue') ?>" class="action-card" style="--accent:#d99513; animation-delay:.15s">
      <div class="action-icon"><i class="bi bi-bar-chart-line"></i></div>
      <div class="action-title">Thống kê doanh thu</div>
      <p class="action-text">Phân tích doanh thu theo ngày, tháng, năm để nắm nhịp kinh doanh nhanh hơn.</p>
      <span class="action-link">Xem thống kê <i class="bi bi-arrow-right"></i></span>
    </a>

    <a href="<?= base_url('/admin/planes') ?>" class="action-card" style="--accent:#4f6fd8; animation-delay:.2s">
      <div class="action-icon"><i class="bi bi-airplane-engines"></i></div>
      <div class="action-title">Quản lý máy bay</div>
      <p class="action-text">Cập nhật đội bay, thông tin máy bay và dữ liệu phục vụ lập lịch khai thác.</p>
      <span class="action-link">Quản lý đội bay <i class="bi bi-arrow-right"></i></span>
    </a>

    <a href="<?= base_url('/admin/customers') ?>" class="action-card" style="--accent:#c45f85; animation-delay:.25s">
      <div class="action-icon"><i class="bi bi-person-vcard"></i></div>
      <div class="action-title">Khách hàng</div>
      <p class="action-text">Quản lý hồ sơ khách hàng và hỗ trợ tra cứu thông tin khi cần chăm sóc.</p>
      <span class="action-link">Xem khách hàng <i class="bi bi-arrow-right"></i></span>
    </a>

    <a href="<?= base_url('/auth/change-profile') ?>" class="action-card" style="--accent:#7357c8; animation-delay:.3s">
      <div class="action-icon"><i class="bi bi-person-gear"></i></div>
      <div class="action-title">Tài khoản quản trị</div>
      <p class="action-text">Cập nhật thông tin cá nhân và giữ hồ sơ quản trị viên luôn chính xác.</p>
      <span class="action-link">Cập nhật hồ sơ <i class="bi bi-arrow-right"></i></span>
    </a>
  </section>

  <section class="status-panel">
    <div class="status-row">
      <div>
        <div class="status-name">Luồng đặt vé</div>
        <div class="status-desc">Sẵn sàng tiếp nhận đơn mới và kiểm tra danh sách đặt chỗ.</div>
      </div>
      <span class="status-badge"><i class="bi bi-check2-circle"></i> Hoạt động</span>
    </div>
    <div class="status-row">
      <div>
        <div class="status-name">Quản trị dữ liệu</div>
        <div class="status-desc">Cập nhật thông tin chuyến bay, máy bay và giá vé theo nhu cầu vận hành.</div>
      </div>
      <span class="status-badge"><i class="bi bi-lightning-charge"></i> Nhanh</span>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
