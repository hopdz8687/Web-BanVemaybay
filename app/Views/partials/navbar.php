<style>
  .hamburger {
    display: inline-block;
    width: 30px;
    height: 20px;
    position: relative;
  }

  .hamburger span {
    display: block;
    height: 3px;
    background: #fff;
    margin: 3px 0;
    border-radius: 2px;
    transition: all .15s;
  }

  /* Navbar Gradient (Teal like Vietnam Airlines) */
  .navbar-gradient {
    background: linear-gradient(90deg, #0E6B7E, #0F7B8E);
  }

  /* Sidebar Styles (Teal/Cyan like Vietnam Airlines) */
  .sidebar-offcanvas {
    width: 300px;
    background: linear-gradient(180deg, #0E6B7E 0%, #0F5A6A 100%);
    color: #ffffff;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
    border-left: none;
    display: flex;
    flex-direction: column;
  }

  .sidebar-logo {
    text-align: center;
    padding: 20px 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  }

  .sidebar-logo img {
    max-width: 180px;
    height: auto;
  }

  .sidebar-offcanvas .offcanvas-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    background: transparent;
    padding: 0;
    display: none;
  }

  .sidebar-offcanvas a {
    color: inherit;
    text-decoration: none;
  }

  .sidebar-avatar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    color: #ffffff;
  }

  .sidebar-avatar img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
  }

  .sidebar-item {
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-radius: 6px;
    margin-bottom: 6px;
    transition: background-color .15s, color .15s;
    color: rgba(255, 255, 255, 0.85);
  }

  .sidebar-item i {
    width: 30px;
    text-align: center;
    font-size: 20px;
    color: rgba(255, 255, 255, 0.7);
    transition: color .15s;
  }

  .sidebar-item div {
    font-size: 15px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.85);
  }

  .sidebar-item:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
  }

  .sidebar-item.active,
  .sidebar-item.active:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    box-shadow: none;
  }

  .sidebar-item.active i,
  .sidebar-item.active div {
    color: #ffffff;
  }

  /* Global button styles (teal theme) */
  .btn-primary, .btn-primary:hover {
    background-color: #0E6B7E !important;
    border-color: #0E6B7E !important;
    color: #ffffff;
  }

  .btn-primary:focus, .btn-primary:active {
    background-color: #0A5169 !important;
    border-color: #0A5169 !important;
  }

  .btn-success, .btn-success:hover {
    background-color: #0E6B7E !important;
    border-color: #0E6B7E !important;
  }

  .btn-success:focus, .btn-success:active {
    background-color: #0A5169 !important;
    border-color: #0A5169 !important;
  }

  /* Form controls */
  .form-control:focus, .form-select:focus {
    border-color: #0E6B7E;
    box-shadow: 0 0 0 0.2rem rgba(14, 107, 126, 0.15);
  }

  .form-check-input:checked {
    background-color: #0E6B7E;
    border-color: #0E6B7E;
  }

  .form-check-input:focus {
    border-color: #0E6B7E;
    box-shadow: 0 0 0 0.2rem rgba(14, 107, 126, 0.15);
  }

  /* Badge */
  .badge-primary {
    background-color: #0E6B7E !important;
  }

  .badge-success {
    background-color: #0E6B7E !important;
  }

  /* Links */
  a:not(.nav-link):not(.btn) {
    color: #0E6B7E;
  }

  a:not(.nav-link):not(.btn):hover {
    color: #0A5169;
  }

  /* Lotusmiles Section */
  .lotusmiles-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 16px 14px;
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
  }

  .lotusmiles-title {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 12px;
  }

  .lotusmiles-btn {
    width: 100%;
    padding: 10px;
    margin-bottom: 8px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    transition: all .15s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .lotusmiles-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
  }

  /* Tables */
  .table-hover tbody tr:hover {
    background-color: rgba(14, 107, 126, 0.08);
  }

  .navbar .text-light,
  .navbar .btn-outline-light {
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.22) !important;
  }

  @media (max-width:576px) {
    .sidebar-offcanvas {
      width: 280px;
    }
  }
</style>

<?php
$current_uri = $_SERVER['REQUEST_URI'];
function is_active($path)
{
  global $current_uri;
  if (strpos($current_uri, $path) !== false) {
    return 'active';
  }
  // Handle root case
  if ($path === base_url('/') && trim(parse_url($current_uri, PHP_URL_PATH), '/') === trim(parse_url($path, PHP_URL_PATH), '/')) {
      return 'active';
  }
  return '';
}
?>

<nav class="navbar navbar-dark navbar-gradient shadow-sm mb-4">
  <div class="container-lg d-flex align-items-center">
    <button class="btn btn-transparent p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu" aria-label="Mở menu">
      <div class="hamburger"><span></span><span></span><span></span></div>
    </button>
    <a class="navbar-brand fw-bold me-auto text-white" href="<?= base_url('/') ?>">
      <i class="bi bi-airplane-fill"></i> Bán Vé Máy Bay
    </a>

    <div class="d-flex align-items-center">
      <?php if (!empty($isLoggedIn)) : ?>
        <span class="text-light me-3">Xin chào, <?= htmlspecialchars((string)($currentUserName ?? '')) ?>!</span>
        <a class="text-light text-decoration-none" href="<?= base_url('/auth/logout') ?>">Đăng xuất</a>
      <?php else : ?>
        <a class="text-light me-3 text-decoration-none" href="<?= base_url('/auth/login') ?>">Đăng nhập</a>
        <a class="text-light text-decoration-none" href="<?= base_url('/auth/register') ?>">Đăng ký</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
  <!-- Logo Vietnam Airlines -->
  <div class="sidebar-logo">
    <div style="font-size: 24px; color: #FFD700; font-weight: bold;">✈</div>
    <div style="font-size: 14px; color: #ffffff; font-weight: 600; margin-top: 8px;">Bán Vé Máy Bay</div>
  </div>

  <div class="offcanvas-header">
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">
    <!-- Menu Items -->
    <div class="flex-grow-1">
      <?php if (!empty($isLoggedIn)) : ?>
        <?php if (!empty($isAdmin)) : ?>
          <a href="<?= base_url('/admin/dashboard') ?>" class="sidebar-item <?= is_active(base_url('/admin/dashboard')) ?>"><i class="bi bi-speedometer2"></i>
            <div>Trang chủ</div>
          </a>
          <a href="<?= base_url('/admin/flights') ?>" class="sidebar-item <?= is_active(base_url('/admin/flights')) ?>"><i class="bi bi-airplane"></i>
            <div>Quản lý chuyến bay</div>
          </a>
          <a href="<?= base_url('/admin/planes') ?>" class="sidebar-item <?= is_active(base_url('/admin/planes')) ?>"><i class="bi bi-airplane-engines"></i>
            <div>Quản lý máy bay</div>
          </a>
          <a href="<?= base_url('/admin/bookings') ?>" class="sidebar-item <?= is_active(base_url('/admin/bookings')) ?>"><i class="bi bi-ticket-perforated"></i>
            <div>Đơn vé</div>
          </a>
          <a href="<?= base_url('/admin/revenue') ?>" class="sidebar-item <?= is_active(base_url('/admin/revenue')) ?>"><i class="bi bi-graph-up-arrow"></i>
            <div>Thống kê doanh thu</div>
          </a>
          <a href="<?= base_url('/admin/customers') ?>" class="sidebar-item <?= is_active(base_url('/admin/customers')) ?>"><i class="bi bi-person-vcard"></i>
            <div>Quản lý khách hàng</div>
          </a>
        <?php else : ?>
          <a href="<?= base_url('/') ?>" class="sidebar-item <?= is_active(base_url('/')) ?>"><i class="bi bi-house-door"></i>
            <div>Khám Phá</div>
          </a>
          <a href="<?= base_url('/customer/search') ?>" class="sidebar-item <?= is_active(base_url('/customer/search')) ?>"><i class="bi bi-ticket"></i>
            <div>Mua vé</div>
          </a>
          <a href="<?= base_url('/customer/cart') ?>" class="sidebar-item <?= is_active(base_url('/customer/cart')) ?>"><i class="bi bi-cart"></i>
            <div>Giỏ hàng</div>
          </a>
          <a href="<?= base_url('/customer/my-tickets') ?>" class="sidebar-item <?= is_active(base_url('/customer/my-tickets')) ?>"><i class="bi bi-ticket-detailed"></i>
            <div>Đơn vé của tôi</div>
          </a>
          
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- LOTUSMILES Section -->
    <?php if (empty($isLoggedIn)) : ?>
      <div class="lotusmiles-section">
        <div class="lotusmiles-title">LOTUSMILES</div>
        <a href="<?= base_url('/auth/login') ?>" class="lotusmiles-btn">
          <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập
        </a>
        <a href="<?= base_url('/auth/register') ?>" class="lotusmiles-btn">
          <i class="bi bi-pencil-square me-2"></i> Đăng ký
        </a>
      </div>
    <?php else : ?>
      <?php if (!empty($isAdmin)) : ?>
        <hr style="border-color: rgba(255, 255, 255, 0.15); margin: 12px 0;">
        <a href="<?= base_url('/auth/change-profile') ?>" class="sidebar-item <?= is_active(base_url('/auth/change-profile')) ?>"><i class="bi bi-person-gear"></i>
          <div>Cập nhật thông tin</div>
        </a>
        <a href="<?= base_url('/auth/logout') ?>" class="sidebar-item"><i class="bi bi-box-arrow-right"></i>
          <div>Đăng xuất</div>
        </a>
      <?php else : ?>
        <hr style="border-color: rgba(255, 255, 255, 0.15); margin: 12px 0;">
        <a href="<?= base_url('/auth/change-profile') ?>" class="sidebar-item <?= is_active(base_url('/auth/change-profile')) ?>"><i class="bi bi-person-gear"></i>
          <div>Cập nhật thông tin</div>
        </a>
        <a href="<?= base_url('/customer/delete-account') ?>" class="sidebar-item <?= is_active(base_url('/customer/delete-account')) ?>"><i class="bi bi-person-x"></i>
          <div>Xóa tài khoản</div>
        </a>
        <a href="<?= base_url('/auth/logout') ?>" class="sidebar-item"><i class="bi bi-box-arrow-right"></i>
          <div>Đăng xuất</div>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
