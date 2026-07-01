<?php
$baseApi = base_url('/api/v1');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>API Demo - Flights</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-1: #0f1b2d;
      --bg-2: #1b2e4b;
      --accent: #f5b82e;
      --text: #e9eef6;
      --muted: #b5c3d9;
      --card: #111c2e;
      --stroke: #2a3b57;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
      color: var(--text);
      background:
        radial-gradient(1200px 600px at 10% -10%, #233b5f 0%, transparent 60%),
        radial-gradient(900px 500px at 110% 0%, #2a4b6e 0%, transparent 55%),
        linear-gradient(160deg, var(--bg-1), var(--bg-2));
      min-height: 100vh;
    }

    .wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 20px 60px;
    }

    header {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 28px;
    }

    h1 {
      font-size: 32px;
      margin: 0;
      letter-spacing: -0.02em;
    }

    .subtitle {
      color: var(--muted);
      font-size: 15px;
    }

    .panel {
      background: var(--card);
      border: 1px solid var(--stroke);
      border-radius: 16px;
      padding: 18px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }

    .toolbar {
      display: grid;
      grid-template-columns: repeat(3, 1fr) auto;
      gap: 12px;
      align-items: end;
    }

    label {
      font-size: 12px;
      color: var(--muted);
      display: block;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid var(--stroke);
      background: #0b1423;
      color: var(--text);
    }

    button {
      padding: 10px 16px;
      border-radius: 10px;
      border: none;
      background: var(--accent);
      color: #1b1606;
      font-weight: 600;
      cursor: pointer;
    }

    .ghost {
      background: transparent;
      color: var(--text);
      border: 1px solid var(--stroke);
    }

    .status {
      margin-top: 12px;
      color: var(--muted);
      font-size: 14px;
      min-height: 20px;
    }

    .grid {
      margin-top: 24px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
    }

    .card {
      background: #0e1728;
      border: 1px solid var(--stroke);
      border-radius: 14px;
      padding: 14px;
      position: relative;
      overflow: hidden;
      animation: rise 0.35s ease both;
    }

    .card h3 {
      margin: 0 0 6px;
      font-size: 18px;
    }

    .meta {
      color: var(--muted);
      font-size: 13px;
      margin-bottom: 8px;
    }

    .badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 999px;
      background: #15233a;
      border: 1px solid var(--stroke);
      font-size: 12px;
      color: var(--muted);
    }

    @keyframes rise {
      from { transform: translateY(8px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 720px) {
      .toolbar {
        grid-template-columns: 1fr;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .card { animation: none; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <h1>Flights API Demo</h1>
      <div class="subtitle">Simple front-end that calls /api/v1/flights and /api/v1/flights/search.</div>
    </header>

    <section class="panel">
      <form id="searchForm" class="toolbar">
        <div>
          <label for="noi_di">Noi di</label>
          <input id="noi_di" name="noi_di" placeholder="Ha Noi">
        </div>
        <div>
          <label for="noi_den">Noi den</label>
          <input id="noi_den" name="noi_den" placeholder="Da Nang">
        </div>
        <div>
          <label for="ngay">Ngay</label>
          <input id="ngay" name="ngay" type="date">
        </div>
        <div>
          <button type="submit">Search</button>
        </div>
      </form>
      <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="ghost" id="loadAll" type="button">Load all</button>
        <span class="status" id="status"></span>
      </div>
    </section>

    <section class="grid" id="grid"></section>
  </div>

  <script>
    const baseApi = <?php echo json_encode($baseApi); ?>;
    const grid = document.getElementById('grid');
    const statusEl = document.getElementById('status');
    const form = document.getElementById('searchForm');
    const loadAllBtn = document.getElementById('loadAll');

    function setStatus(text) {
      statusEl.textContent = text || '';
    }

    function render(items) {
      grid.innerHTML = '';
      if (!items.length) {
        grid.innerHTML = '<div class="card"><h3>No data</h3><div class="meta">No flights found.</div></div>';
        return;
      }
      items.forEach((f) => {
        const card = document.createElement('div');
        card.className = 'card';
        const title = document.createElement('h3');
        title.textContent = f.so_hieu || 'Unknown flight';
        const meta = document.createElement('div');
        meta.className = 'meta';
        meta.textContent = (f.noi_di || '-') + ' -> ' + (f.noi_den || '-');
        const time = document.createElement('div');
        time.className = 'meta';
        time.textContent = 'Khoi hanh: ' + (f.gio_khoi_hanh || '-');
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.textContent = 'Ghe con: ' + (f.ghe_con ?? '-');
        card.appendChild(title);
        card.appendChild(meta);
        card.appendChild(time);
        card.appendChild(badge);
        grid.appendChild(card);
      });
    }

    async function fetchJson(url) {
      setStatus('Loading...');
      const res = await fetch(url);
      const data = await res.json();
      if (!res.ok || !data.ok) {
        throw new Error(data.message || 'Request failed');
      }
      return data.data || [];
    }

    async function loadAll() {
      try {
        const items = await fetchJson(baseApi + '/flights?limit=12');
        render(items);
        setStatus('Loaded ' + items.length + ' flights');
      } catch (err) {
        setStatus(err.message);
      }
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const params = new URLSearchParams();
      const noiDi = document.getElementById('noi_di').value.trim();
      const noiDen = document.getElementById('noi_den').value.trim();
      const ngay = document.getElementById('ngay').value.trim();
      if (noiDi) params.set('noi_di', noiDi);
      if (noiDen) params.set('noi_den', noiDen);
      if (ngay) params.set('ngay', ngay);

      try {
        const items = await fetchJson(baseApi + '/flights/search?' + params.toString());
        render(items);
        setStatus('Found ' + items.length + ' flights');
      } catch (err) {
        setStatus(err.message);
      }
    });

    loadAllBtn.addEventListener('click', loadAll);
    loadAll();
  </script>
</body>
</html>
