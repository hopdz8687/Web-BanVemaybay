<style>
  /* Sticky footer (no overlap): bottom on short pages, after content on long pages */
  html, body { height: 100%; }
  body { min-height: 100vh; display: flex; flex-direction: column; }
  body > .container,
  body > .container-fluid,
  body > .container-sm,
  body > .container-md,
  body > .container-lg,
  body > .container-xl,
  body > .container-xxl { flex: 1 0 auto; }
  footer.site-footer { margin-top: auto; }
  footer .container-lg { max-width: 1140px; }
</style>

<footer class="site-footer bg-light py-4 mt-5 border-top">
  <div class="container-lg">
    <div class="row">
      <div class="col-md-4">
        <h6 class="fw-bold">Bán Vé Máy Bay</h6>
        <p class="text-muted small">Hệ thống đặt vé máy bay trên toàn quốc.</p>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold">Liên hệ</h6>
        <p class="text-muted small">
          Email: vanhopmai2004@gmail.com<br>
          Điện thoại: 0888502305
        </p>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold">Liên kết</h6>
        <p class="text-muted small">
          <a href="<?=base_url('/')?>" class="text-decoration-none">Trang chủ</a><br>
        </p>
      </div>
    </div>
    <hr class="my-3">
    <div class="text-center text-muted small">
      <p>&copy; 2004 Bán Vé Máy Bay hop good boy</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
