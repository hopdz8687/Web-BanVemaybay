<style>
  .hamburger{display:inline-block;width:30px;height:20px;position:relative}
  .hamburger span{display:block;height:3px;background:#fff;margin:3px 0;border-radius:2px;transition:all .15s}
  /* sidebar: white background, dark text */
  .sidebar-offcanvas{width:300px;background:#ffffff;color:#212529;box-shadow:4px 0 24px rgba(0,0,0,0.12)}
  .sidebar-offcanvas .offcanvas-header{border-bottom:1px solid rgba(0,0,0,0.08)}
  .sidebar-offcanvas a{color:inherit;text-decoration:none}
  .sidebar-offcanvas .offcanvas-title{color:#212529}
  .sidebar-avatar{display:flex;align-items:center;gap:12px;padding:14px 0}
  .sidebar-avatar img{width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,0,0,0.10)}
  .sidebar-item{padding:12px 10px;display:flex;align-items:center;gap:14px;border-radius:6px;margin-bottom:6px}
  .sidebar-item i{width:30px;text-align:center;font-size:18px;color:#212529}
  .sidebar-item div{font-size:15px;color:#212529}
  .sidebar-item:hover{background:rgba(0,0,0,0.05);padding-left:14px}
  .sidebar-footer{position:absolute;bottom:18px;left:18px;right:18px;color:rgba(33,37,41,0.85)}
  /* ensure header greeting remains white */
  .navbar .text-light, .navbar .btn-outline-light{color:#ffffff !important;border-color:rgba(255,255,255,0.18) !important}
  @media (max-width:576px){.sidebar-offcanvas{width:260px}}
</style>

<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
  <div class="container-lg d-flex align-items-center">
    <button class="btn btn-transparent p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu" aria-label="Mở menu">
      <div class="hamburger"><span></span><span></span><span></span></div>
    </button>
    <a class="navbar-brand fw-bold me-auto text-white" href="<?=base_url('/')?>">
      <i class="bi bi-airplane-fill"></i> Bán Vé Máy Bay
    </a>

    <div class="d-flex align-items-center">
      <?php if (!empty($isLoggedIn)): ?>
        <span class="text-light me-3">Xin chào, <?=htmlspecialchars((string)($currentUserName ?? ''))?>!</span>
        <a class="text-light text-decoration-none" href="<?=base_url('/auth/logout')?>">Đăng xuất</a>
      <?php else: ?>
        <a class="text-light me-3 text-decoration-none" href="<?=base_url('/auth/login')?>">Đăng nhập</a>
        <a class="text-light text-decoration-none" href="<?=base_url('/auth/register')?>">Đăng ký</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
  <div class="offcanvas-header">
    <div class="d-flex align-items-center">
      <div class="me-2">
        <img src="<?=base_url('/assets/images/avatar.png')?>" alt="Avatar" onerror="this.style.display='none'">
      </div>
      <div>
        <div class="fw-bold"><?php if (!empty($isLoggedIn)) echo htmlspecialchars((string)($currentUserName ?? '')); else echo 'Khách'; ?></div>
        <div class="small text-muted">Tài khoản</div>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body position-relative">
    <?php if (!empty($isLoggedIn)): ?>
      <?php if (!empty($isAdmin)): ?>
        <a href="<?=base_url('/admin/dashboard')?>" class="d-flex sidebar-item"><i class="bi bi-speedometer2"></i><div>Trang chủ</div></a>
        <a href="<?=base_url('/admin/flights')?>" class="d-flex sidebar-item"><i class="bi bi-airplane"></i><div>Quản lý chuyến bay</div></a>
        <a href="<?=base_url('/admin/planes')?>" class="d-flex sidebar-item"><i class="bi bi-airplane-engines"></i><div>Quản lý máy bay</div></a>
        <a href="<?=base_url('/admin/bookings')?>" class="d-flex sidebar-item"><i class="bi bi-ticket-perforated"></i><div>Đơn vé</div></a>
        <a href="<?=base_url('/admin/revenue')?>" class="d-flex sidebar-item"><i class="bi bi-graph-up-arrow"></i><div>Thống kê doanh thu</div></a>
        <a href="<?=base_url('/admin/customers')?>" class="d-flex sidebar-item"><i class="bi bi-person-vcard"></i><div>Quản lý tài khoản khách hàng</div></a>
        <a href="<?=base_url('/auth/change-profile')?>" class="d-flex sidebar-item"><i class="bi bi-person-lock"></i><div>Cập nhật thông tin</div></a>
      <?php else: ?>
        <a href="<?=base_url('/')?>" class="d-flex sidebar-item"><i class="bi bi-house-door"></i><div>Trang chủ</div></a>
        <a href="<?=base_url('/customer/search')?>" class="d-flex sidebar-item"><i class="bi bi-search"></i><div>Tìm chuyến bay</div></a>
        <a href="<?=base_url('/customer/cart')?>" class="d-flex sidebar-item"><i class="bi bi-cart"></i><div>Giỏ hàng</div></a>
        <a href="<?=base_url('/customer/my-tickets')?>" class="d-flex sidebar-item"><i class="bi bi-ticket"></i><div>Vé của tôi</div></a>
        <a href="<?=base_url('/auth/change-profile')?>" class="d-flex sidebar-item"><i class="bi bi-person-lock"></i><div>Cập nhật thông tin</div></a>
        <a href="<?=base_url('/customer/delete-account')?>" class="d-flex sidebar-item"><i class="bi bi-person-x"></i><div>Xóa tài khoản</div></a>
      <?php endif; ?>

      <a href="<?=base_url('/auth/logout')?>" class="d-flex sidebar-item"><i class="bi bi-box-arrow-right"></i><div>Đăng xuất</div></a>
    <?php else: ?>
      <a href="<?=base_url('/auth/login')?>" class="d-flex sidebar-item"><i class="bi bi-box-arrow-in-right"></i><div>Đăng nhập</div></a>
      <a href="<?=base_url('/auth/register')?>" class="d-flex sidebar-item"><i class="bi bi-pencil-square"></i><div>Đăng ký</div></a>
    <?php endif; ?>

    
  </div>
</div>
