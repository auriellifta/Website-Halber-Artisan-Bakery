// ============================================================
// MOBILE MENU
// ============================================================
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  const ham  = document.getElementById('hamburger');
  if (menu) menu.classList.toggle('open');
  if (ham)  ham.classList.toggle('open');
}

// ============================================================
// PAGE TRANSITION
// ============================================================
function initPageTransition() {
  const overlay = document.getElementById('pageTransition');
  if (!overlay) return;

  overlay.style.opacity    = '1';
  overlay.style.transition = 'opacity 0.5s ease';
  requestAnimationFrame(() => requestAnimationFrame(() => {
    overlay.style.opacity = '0';
    overlay.style.pointerEvents = 'none';
  }));

  document.querySelectorAll('a[href]').forEach(link => {
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('http') ||
        href.startsWith('mailto') || href.startsWith('tel') ||
        href.includes('wa.me') || link.target === '_blank') return;

    link.addEventListener('click', function(e) {
      e.preventDefault();
      overlay.style.transition = 'opacity 0.35s ease';
      overlay.style.opacity    = '1';
      overlay.style.pointerEvents = 'all';
      const target = this.getAttribute('href');
      setTimeout(() => { window.location.href = target; }, 350);
    });
  });
}

// ============================================================
// SCROLL REVEAL
// ============================================================
function initScrollReveal() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll(
    '.why-card, .value-card, .stat-item, .lokasi-row, .ulasan-card'
  ).forEach(el => { el.classList.add('reveal'); observer.observe(el); });
}

// ============================================================
// NAVBAR SCROLL
// ============================================================
function initNavbarScroll() {
  const nav = document.getElementById('navbar');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });
}

// ============================================================
// INIT (non-menu pages)
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  initPageTransition();
  initScrollReveal();
  initNavbarScroll();
});

// ============================================================
// MENU PAGE — Data, Cart Panel, Form, Payment, WhatsApp Checkout
// ============================================================

const menuDataV4 = [
  { id:1,  cat:'roti-sisir', name:'Roti Sisir Original',             img:'assets/sisirOri.jpg', price:37000, desc:'Roti sisir klasik, lembut dan gurih' },
  { id:2,  cat:'roti-sisir', name:'Roti Sisir Butter Cream Mocha',   img:'assets/sisirBcream.jpg', price:47000, desc:'Roti sisir butter cream dengan sentuhan mocha' },
  { id:3,  cat:'roti-sisir', name:'Roti Sisir Cream Cheese',         img:'assets/sisirCcream.jpg', price:57000, desc:'Roti sisir dengan topping cream cheese lembut' },
  { id:4,  cat:'roti-sisir', name:'Roti Sisir Coklat Keju',          img:'assets/sisirCoklatK.jpg', price:60000, desc:'Roti sisir perpaduan coklat manis dan keju gurih' },
  { id:5, cat:'roti-sisir', name:'Roti Sisir Smoked Beef & Cheese',  img:'assets/sisirBeef.jpg', price:77000, desc:'Roti sisir premium isian smoked beef dan keju leleh', badge:'Best Seller'},
  { id:6, cat:'roti-manis', name:'Roti Manis Abon',                  img:'assets/manisAbon.jpg', price:20000, desc:'Roti manis lembut dengan taburan abon gurih' },
  { id:7, cat:'roti-manis', name:'Roti Manis Sosis',                 img:'assets/manisSosis.jpg', price:17000, desc:'Roti manis dengan isian sosis juicy' },
  { id:8, cat:'roti-manis', name:'Roti Manis Pisang Coklat',         img:'assets/manisPiscok.jpg', price:17000, desc:'Roti manis isian pisang dan coklat lezat' },
  { id:9, cat:'roti-manis', name:'Roti Manis Double Choco',          img:'assets/manisChoco.jpg', price:17000, desc:'Roti manis double chocolate untuk pecinta coklat' },
  { id:10, cat:'roti-manis', name:'Roti Manis Blueberry Cheese',     img:'assets/manisBKeju.jpg', price:17000, desc:'Roti manis perpaduan blueberry segar dan cream cheese' },
  { id:11, cat:'pizza', name:'Pizza Smoked Beef',                    img:'assets/pizzaBeef.jpg', desc:'Smoked beef, keju mozzarella, paprika, bombay di atas saus tomat khas Halber', sizes:{ Small:60000, Medium:80000, Large:120000 } },
  { id:12, cat:'pizza', name:'Pizza Sosis',                          img:'assets/pizzaSosis.jpg', desc:'Sosis premium, keju mozzarella leleh, paprika, bombay dengan saus tomat', sizes:{ Small:60000, Medium:80000, Large:120000 } },
  { id:13, cat:'pizza', name:'Pizza Keju',                           img:'assets/pizzaKeju.jpg', desc:'Perpaduan mozzarella, cheddar, dan keju parmesan di atas kulit tipis renyah', sizes:{ Small:60000, Medium:80000, Large:120000 } },
  { id:14, cat:'pizza', name:'Pizza Tuna Pedas',                     img:'assets/pizzaTuna.jpg', desc:'Tuna pedas, keju mozzarella, paprika, bombay – cocok untuk pencinta rasa pedas', badge:'Best Seller', sizes:{ Small:60000, Medium:80000, Large:120000 } },
  { id:15, cat:'bread', name:'Garlic Cheese Bread',                  img:'assets/breadCgarlic.jpg', price:27000, desc:'Roti garlic cheese bread dengan bawang putih dan keju leleh', badge:'Best Seller'},
  { id:16, cat:'bread', name:'Country Bread',                        img:'assets/breadCountry.jpg', price:45000, desc:'Sourdough country bread dengan tekstur renyah dan cita rasa khas' },
  { id:17, cat:'bread', name:'Soft Sourdough Chocolate',             img:'assets/breadSourdoughCoklat.jpg', price:30000, desc:'Soft sourdough dengan isian coklat premium yang meleleh' },
  { id:18, cat:'bread', name:'Shokupan',                             img:'assets/breadShokupan.jpg', price:60000, desc:'Japanese milk bread – satu loaf penuh, lembut seperti kapas' },
  { id:19, cat:'bread', name:'Roti Gandum Original',                 img:'assets/breadGandum.jpg', price:60000, desc:'Roti gandum original satu loaf penuh – sehat dan bergizi' },
  { id:20, cat:'bread', name:'Milkbun Vanilla',                      img:'assets/breadMilkbun.jpg', price:42000, desc:'Milkbun lembut dengan krim vanilla yang harum dan manis' },
  { id:21, cat:'minuman', name:'Belgian Chocolate',                  img:'assets/minumBelgium.jpg', desc:'Coklat belgia premium – tersedia panas, dingin, atau dalam botol', sizes:{ 'Hot ':19000, 'Ice ':19000, 'Bottle ':23000 } },
  { id:22, cat:'minuman', name:'Matcha',                             img:'assets/minumMatcha.jpg', desc:'Matcha premium earthy dan harum – tersedia panas, dingin, atau dalam botol', sizes:{ 'Hot ':23000, 'Ice ':23000, 'Bottle ':27000 } },
  { id:23, cat:'minuman', name:'Aren Latte',                         img:'assets/minumAren.jpg', desc:'Kopi latte gula aren asli – manis alami dan wangi, tersedia 3 pilihan sajian', sizes:{ 'Hot ':19000, 'Ice ':19000, 'Bottle ':23000 } },
  { id:24, cat:'minuman', name:'Caramel Latte',                      img:'assets/minumCaramel.jpg', desc:'Latte karamel manis yang lezat – tersedia panas, dingin, atau dalam botol', sizes:{ 'Hot ':23000, 'Ice ':23000, 'Bottle ':27000 } },
  { id:25, cat:'minuman', name:'Butterscotch Seasalt',               img:'assets/minumButterscotch.jpg', desc:'Kopi butterscotch seasalt dingin – manis gurih dengan sentuhan garam', sizes:{ 'Ice ':25000 } }
];

const menuFiltersV4 = [
  { key:'all',        label:'Semua' },
  { key:'roti-sisir', label:'Roti Sisir' },
  { key:'roti-manis', label:'Roti Manis' },
  { key:'pizza',      label:'Pizza' },
  { key:'bread',      label:'Sourdough' },
  { key:'minuman',    label:'Minuman' },
];

const paymentOptsV4 = [
  { key:'Transfer BCA',     icon:'🏦' },
  { key:'Transfer BRI',     icon:'🏦' },
  { key:'Transfer Mandiri', icon:'🏦' },
  { key:'QRIS',             icon:'📱' },
  { key:'COD / Tunai',      icon:'💵' },
];

let cartV4 = JSON.parse(localStorage.getItem('halber_cart_v4') || '[]');
let activeFilterV4 = 'all';
let selectedPaymentV4 = '';
let toastTimerV4 = null;

const fmtV4 = n => 'Rp ' + n.toLocaleString('id-ID');

function buildFiltersV4() {
  const wrap = document.getElementById('hbFilterWrap');
  if (!wrap) return;
  wrap.innerHTML = '';
  menuFiltersV4.forEach(f => {
    const btn = document.createElement('button');
    btn.className = 'hb-filter-btn' + (f.key === activeFilterV4 ? ' active' : '');
    btn.textContent = f.label;
    btn.onclick = () => { activeFilterV4 = f.key; buildFiltersV4(); renderMenuV4(); };
    wrap.appendChild(btn);
  });
}

function buildPaymentV4() {
  const grid = document.getElementById('hbPaymentGrid');
  if (!grid) return;
  grid.innerHTML = '';
  paymentOptsV4.forEach(opt => {
    const div = document.createElement('div');
    div.className = 'hb-payment-opt' + (selectedPaymentV4 === opt.key ? ' selected' : '');
    div.innerHTML = `<span>${opt.icon}</span>${opt.key}`;
    div.onclick = () => { selectedPaymentV4 = opt.key; buildPaymentV4(); };
    grid.appendChild(div);
  });
}

function renderMenuV4() {
  const grid = document.getElementById('hbMenuGrid');
  if (!grid) return;
  grid.innerHTML = '';
  const list = activeFilterV4 === 'all' ? menuDataV4 : menuDataV4.filter(i => i.cat === activeFilterV4);
  list.forEach(item => {
    const card = document.createElement('div');
    card.className = 'hb-card';

    if (item.sizes) {
      // Pizza card with size dropdown
      const sizeKeys = Object.keys(item.sizes);
      const defaultSize = sizeKeys[0];
      const selectId = 'size-sel-' + item.id;
      const priceId  = 'size-price-' + item.id;
      const sizeLabel = item.cat === 'minuman' ? 'Sajian' : 'Ukuran';
      const optionsHtml = sizeKeys.map(s =>
        `<option value="${s}">${s}</option>`
      ).join('');

      card.innerHTML = `
        <div class="hb-card-img">
          <img src="${item.img}" alt="${item.name}" loading="lazy">
          ${item.badge ? `<span class="hb-card-badge">${item.badge}</span>` : ''}
        </div>
        <div class="hb-card-body">
          <div class="hb-card-name">${item.name}</div>
          <div class="hb-card-desc">${item.desc}</div>
          <div class="hb-pizza-size-row">
            <label class="hb-size-label">${sizeLabel}</label>
            <select class="hb-size-select" id="${selectId}">
              ${optionsHtml}
            </select>
          </div>
          <div class="hb-card-footer">
            <div class="hb-card-price" id="${priceId}">${fmtV4(item.sizes[defaultSize])}</div>
            <button class="hb-add-btn" id="addbtn-${item.id}" onclick="addPizzaToCartV4(${item.id})" title="Tambah ke Keranjang">+</button>
          </div>
        </div>`;
      grid.appendChild(card);

      // Live price update on select change
      const sel = document.getElementById(selectId);
      const priceEl = document.getElementById(priceId);
      sel.addEventListener('change', () => {
        priceEl.textContent = fmtV4(item.sizes[sel.value]);
      });
    } else {
      // Regular card
      card.innerHTML = `
        <div class="hb-card-img">
          <img src="${item.img}" alt="${item.name}" loading="lazy">
          ${item.badge ? `<span class="hb-card-badge">${item.badge}</span>` : ''}
        </div>
        <div class="hb-card-body">
          <div class="hb-card-name">${item.name}</div>
          <div class="hb-card-desc">${item.desc}</div>
          <div class="hb-card-footer">
            <div class="hb-card-price">${fmtV4(item.price)}</div>
            <button class="hb-add-btn" onclick="addToCartV4(${item.id})" title="Tambah ke Keranjang">+</button>
          </div>
        </div>`;
      grid.appendChild(card);
    }
  });
}

function addToCartV4(id) {
  const item = menuDataV4.find(m => m.id === id);
  const ex = cartV4.find(c => c.id === id);
  if (ex) ex.qty += 1;
  else cartV4.push({ ...item, qty: 1 });
  saveCartV4(); updateCartUIV4();
  showToastV4(item.name + ' ditambahkan!');
}

function addPizzaToCartV4(id) {
  const item = menuDataV4.find(m => m.id === id);
  const sel = document.getElementById('size-sel-' + id);
  const size = sel ? sel.value : Object.keys(item.sizes)[0];
  const price = item.sizes[size];
  const cartKey = id + '_' + size;
  const ex = cartV4.find(c => c.cartKey === cartKey);
  if (ex) ex.qty += 1;
  else cartV4.push({ ...item, price, size, cartKey, qty: 1, name: item.name + ' (' + size + ')' });
  saveCartV4(); updateCartUIV4();
  showToastV4(item.name + ' ' + size + ' ditambahkan!');
}

function updateQtyV4(cartKey, delta) {
  const ex = cartV4.find(c => (c.cartKey || String(c.id)) === cartKey);
  if (!ex) return;
  ex.qty += delta;
  if (ex.qty <= 0) cartV4 = cartV4.filter(c => (c.cartKey || String(c.id)) !== cartKey);
  saveCartV4(); updateCartUIV4();
}

function removeItemV4(cartKey) {
  cartV4 = cartV4.filter(c => (c.cartKey || String(c.id)) !== cartKey);
  saveCartV4(); updateCartUIV4();
}

function saveCartV4() {
  localStorage.setItem('halber_cart_v4', JSON.stringify(cartV4));
}

function updateCartUIV4() {
  const totalItems = cartV4.reduce((s, i) => s + i.qty, 0);
  const totalPrice = cartV4.reduce((s, i) => s + i.price * i.qty, 0);

  const badge = document.getElementById('hbCartBadge');
  const totalEl = document.getElementById('hbCartTotal');
  const btn = document.getElementById('hbCheckoutBtn');
  if (badge) badge.textContent = totalItems;
  if (totalEl) totalEl.textContent = fmtV4(totalPrice);
  if (btn) btn.disabled = cartV4.length === 0;

  const container = document.getElementById('hbCartItems');
  if (!container) return;
  if (cartV4.length === 0) {
    container.innerHTML = '<div class="hb-cart-empty">Keranjang belanja Anda masih kosong.</div>';
    return;
  }
  container.innerHTML = '';
  cartV4.forEach(item => {
    const key = item.cartKey || String(item.id);
    const row = document.createElement('div');
    row.className = 'hb-cart-item';
    row.innerHTML = `
      <img class="hb-cart-item-thumb" src="${item.img}" alt="${item.name}">
      <div class="hb-cart-item-info">
        <div class="hb-cart-item-name">${item.name}</div>
        <div class="hb-cart-item-price">${fmtV4(item.price * item.qty)}</div>
      </div>
      <div class="hb-qty-ctrl">
        <button class="hb-qty-btn" data-key="${key}" data-delta="-1">−</button>
        <span class="hb-qty-val">${item.qty}</span>
        <button class="hb-qty-btn" data-key="${key}" data-delta="1">+</button>
      </div>
      <button class="hb-item-del" data-key="${key}" title="Hapus">✕</button>`;
    container.appendChild(row);
  });
}

function initCartListenerV4() {
  const panel = document.getElementById('hbCartPanel');
  if (!panel || panel._cartListenerInit) return;
  panel._cartListenerInit = true;
  panel.addEventListener('click', function(e) {
    const qtyBtn = e.target.closest('.hb-qty-btn');
    const delBtn = e.target.closest('.hb-item-del');
    if (qtyBtn) updateQtyV4(qtyBtn.dataset.key, parseInt(qtyBtn.dataset.delta));
    if (delBtn) removeItemV4(delBtn.dataset.key);
  });
}

function openCartV4() {
  const panel = document.getElementById('hbCartPanel');
  const overlay = document.getElementById('hbCartOverlay');
  if (panel) panel.classList.add('open');
  if (overlay) overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  // Show exit buttons, hide open button
  const navCartBtn = document.getElementById('hbNavCartBtn');
  const navExitBtn = document.getElementById('hbNavExitBtn');
  const mobileExitBtn = document.getElementById('mobileExitBtn');
  if (navCartBtn) navCartBtn.style.display = 'none';
  if (navExitBtn) navExitBtn.style.display = 'flex';
  if (mobileExitBtn) mobileExitBtn.style.display = 'flex';
}

function closeCartV4() {
  const panel = document.getElementById('hbCartPanel');
  const overlay = document.getElementById('hbCartOverlay');
  if (panel) panel.classList.remove('open');
  if (overlay) overlay.classList.remove('open');
  document.body.style.overflow = '';
  // Hide exit buttons, show open button
  const navCartBtn = document.getElementById('hbNavCartBtn');
  const navExitBtn = document.getElementById('hbNavExitBtn');
  const mobileExitBtn = document.getElementById('mobileExitBtn');
  if (navCartBtn) navCartBtn.style.display = 'flex';
  if (navExitBtn) navExitBtn.style.display = 'none';
  if (mobileExitBtn) mobileExitBtn.style.display = 'none';
}

function checkoutWhatsAppV4() {
  const nama    = (document.getElementById('hbInputNama')    || {}).value || '';
  const hp      = (document.getElementById('hbInputHp')      || {}).value || '';
  const catatan = (document.getElementById('hbInputCatatan') || {}).value || '';
  if (!nama.trim() || !hp.trim()) { showToastV4('Lengkapi nama dan nomor HP!'); return; }
  if (!selectedPaymentV4)         { showToastV4('Pilih metode pembayaran!');     return; }
  if (cartV4.length === 0)        { showToastV4('Keranjang masih kosong!');      return; }

  const wa = '6285267347856';
  let msg = 'Halo Halber Artisan Bakery! 🍞\n\nSaya ingin memesan:\n\n';
  let total = 0;
  cartV4.forEach((item, i) => {
    const sub = item.price * item.qty;
    total += sub;
    msg += `${i + 1}. ${item.name} x${item.qty} = ${fmtV4(sub)}\n`;
  });
  msg += `\n*Total: ${fmtV4(total)}*\n\n`;
  msg += `Nama: ${nama.trim()}\nNo. HP: ${hp.trim()}\n`;
  if (catatan.trim()) msg += `Catatan: ${catatan.trim()}\n`;
  msg += `Pembayaran: ${selectedPaymentV4}\n\nTerima kasih! 🙏`;
  window.open('https://wa.me/' + wa + '?text=' + encodeURIComponent(msg), '_blank');
}

function showToastV4(msg) {
  const el = document.getElementById('hbToast');
  if (!el) return;
  el.textContent = msg;
  el.classList.add('show');
  clearTimeout(toastTimerV4);
  toastTimerV4 = setTimeout(() => el.classList.remove('show'), 2500);
}

// Init on menu page
document.addEventListener('DOMContentLoaded', () => {
  if (!document.getElementById('hbMenuGrid')) return;
  buildFiltersV4();
  buildPaymentV4();
  renderMenuV4();
  updateCartUIV4();
  initCartListenerV4();
});
