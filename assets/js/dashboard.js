// ============================================================
// CAROLINE'S PLACE — Admin Dashboard JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
  const apiBase = window.location.pathname.split('/').includes('admin') ? '../api/' : 'api/';

  // ── Filters ──────────────────────────────────────────────────
  const divFilter    = document.getElementById('divisionFilter');
  const statFilter   = document.getElementById('statusFilter');
  const refreshBtn   = document.getElementById('refreshBtn');

  function applyFilters() {
    const params = new URLSearchParams(window.location.search);
    if (divFilter.value  !== 'all') params.set('division', divFilter.value);
    else params.delete('division');
    if (statFilter.value !== 'all') params.set('status', statFilter.value);
    else params.delete('status');
    window.location.search = params.toString();
  }

  if (divFilter)  divFilter.addEventListener('change',  applyFilters);
  if (statFilter) statFilter.addEventListener('change', applyFilters);
  if (refreshBtn) refreshBtn.addEventListener('click',  () => window.location.reload());

  // ── Inline status / payment updates ──────────────────────────
  document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', function () {
      const id   = this.dataset.id;
      const type = this.dataset.type || 'legacy';
      updateBooking(id, { status: this.value, type }, this);
    });
  });

  document.querySelectorAll('.payment-select').forEach(sel => {
    sel.addEventListener('change', function () {
      const id   = this.dataset.id;
      const type = this.dataset.type || 'legacy';
      updateBooking(id, { payment_status: this.value, type }, this);
    });
  });

  function updateBooking(id, payload, el) {
    const original = el.value;
    el.disabled = true;

    fetch(apiBase + 'update-booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: parseInt(id), ...payload }),
      credentials: 'same-origin',
    })
      .then(r => r.json())
      .then(res => {
        if (res.error) {
          el.value = original;
          alert('Update failed: ' + res.error);
        } else {
          // update status colour classes
          if (payload.status) {
            el.className = 'status-select status--' + payload.status + (el.dataset.type === 'spa' ? ' status--spa' : '');
          }
          if (payload.payment_status) {
            el.className = 'payment-select payment--' + payload.payment_status;
          }
          // refresh stats row (both legacy + spa sections reloaded full page for simplicity & accuracy)
          window.setTimeout(() => window.location.reload(), 300);
        }
      })
      .catch(() => {
        el.value = original;
        alert('Network error. Please try again.');
      })
      .finally(() => { el.disabled = false; });
  }

  // ── Refresh stats without page reload ────────────────────────
  function refreshStats() {
    fetch(apiBase + 'stats.php', { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        const map = {
          statTotal:     data.total,
          statPending:   data.pending,
          statConfirmed: data.confirmed,
          statCompleted: data.completed,
        };
        Object.entries(map).forEach(([id, val]) => {
          const el = document.getElementById(id);
          if (el) el.textContent = val;
        });
      })
      .catch(() => {}); // silent — page still works
  }

  // ── Details modal ─────────────────────────────────────────────
  const modal   = document.getElementById('detailModal');
  const modalBg = document.getElementById('modalBackdrop');

  window.openDetail = function (bookingJson) {
    const b = JSON.parse(decodeURIComponent(bookingJson));
    if (!modal) return;

    modal.querySelector('#mRef').textContent       = b.reference_code;
    modal.querySelector('#mName').textContent      = b.full_name;
    modal.querySelector('#mEmail').textContent     = b.email;
    modal.querySelector('#mPhone').textContent     = b.phone;
    modal.querySelector('#mDivision').textContent  = b.division === 'clubhouse' ? 'The Club House' : 'N Lounge & Spa';
    modal.querySelector('#mService').textContent   = b.service_name;
    modal.querySelector('#mDate').textContent      = formatDate(b.preferred_date);
    modal.querySelector('#mTime').textContent      = b.preferred_time;
    modal.querySelector('#mNotes').textContent     = b.notes || 'No notes provided.';
    modal.querySelector('#mAdminNotes').value      = b.admin_notes || '';
    modal.querySelector('#mSaveBtn').dataset.id    = b.id;

    if (modalBg) modalBg.classList.add('open');
  };

  window.closeDetail = function () {
    if (modalBg) modalBg.classList.remove('open');
  };

  // Close on backdrop click
  if (modalBg) {
    modalBg.addEventListener('click', function (e) {
      if (e.target === modalBg) closeDetail();
    });
  }

  // Close on Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDetail();
  });

  // Save admin notes
  const saveBtn = document.getElementById('mSaveBtn');
  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      const id    = parseInt(this.dataset.id);
      const notes = document.getElementById('mAdminNotes').value;
      this.textContent = 'Saving…';
      this.disabled    = true;

      fetch(apiBase + 'update-booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, admin_notes: notes }),
        credentials: 'same-origin',
      })
        .then(r => r.json())
        .then(res => {
          this.textContent = res.error ? 'Error — try again' : 'Notes Saved ✓';
          setTimeout(() => {
            this.textContent = 'Save Notes';
            this.disabled    = false;
          }, 2000);
        })
        .catch(() => {
          this.textContent = 'Error — try again';
          this.disabled    = false;
        });
    });
  }

  // ── Logout ────────────────────────────────────────────────────
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      fetch(apiBase + 'logout.php', { method: 'POST', credentials: 'same-origin' })
        .then(() => { window.location.href = 'login.php'; })
        .catch(() => { window.location.href = 'login.php'; });
    });
  }

  // ── SPA Bookings — inline "View Services" accordion ─────────
  const spaViewBtns = document.querySelectorAll('.spa-view-items-btn');
  spaViewBtns.forEach(btn => {
    btn.addEventListener('click', async function () {
      const bookingId = parseInt(this.dataset.id);
      const row       = document.getElementById('spa-items-' + bookingId);
      if (!row) return;

      const isOpen = row.style.display !== 'none';
      if (isOpen) {
        // just collapse
        row.style.display = 'none';
        this.innerHTML = '🛒 View Services';
        this.classList.remove('action-btn--active');
        return;
      }

      // Expanding — load items if not yet loaded
      const panel = row.querySelector('.spa-items-panel');
      const loadedAttr = panel.getAttribute('data-loaded');
      row.style.display = 'table-row';
      this.innerHTML = '🔽 Hide Services';
      this.classList.add('action-btn--active');

      if (loadedAttr === '1') return; // already loaded

      panel.querySelector('.spa-items-loading').textContent = '⏳ Loading booked services…';
      try {
        const resp = await fetch(apiBase + 'admin_spa_items.php?booking_id=' + bookingId, { credentials: 'same-origin' });
        const data = await resp.json();
        if (!resp.ok || !data.ok) throw new Error(data.error || 'Failed to load services');

        // Build the items table
        const booking = data.booking;
        let html = `
          <div style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
              <strong style="color:var(--primary); font-family:var(--font-serif); font-size:1.05rem;">
                👤 Client Breakdown — ${data.item_count} service(s) ordered
              </strong>
              <div style="font-size:0.78rem; color:var(--muted); margin-top:2px;">
                ${booking.email ? escapeHtml(booking.email) + ' · ' : ''}${escapeHtml(booking.preferred_date)} ${escapeHtml(booking.preferred_time)}
                ${booking.notes ? ' · 📝 ' + escapeHtml(booking.notes) : ''}
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted);">Grand Total (matches user's live total)</div>
              <div style="font-family:var(--font-serif); font-weight:700; font-size:1.35rem; color:#8B6F2E;">
                ₦${Number(booking.total_amount_ngn).toLocaleString('en-NG')}
              </div>
              <div style="font-size:0.72rem; color:var(--muted);">Calculated items subtotal: ${data.calculated_total_formatted}</div>
            </div>
          </div>
          <table style="width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden;">
            <thead>
              <tr style="background:#EADFC7;">
                <th style="text-align:left; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">#</th>
                <th style="text-align:left; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">Service</th>
                <th style="text-align:left; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">Variant / Option</th>
                <th style="text-align:right; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">Qty</th>
                <th style="text-align:right; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">Unit Price</th>
                <th style="text-align:right; padding:10px 14px; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; color:#5a4a22;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
        `;
        data.items.forEach((item, i) => {
          html += `
            <tr style="border-top:1px solid rgba(139,111,46,0.12);">
              <td style="padding:12px 14px; font-size:0.82rem; color:var(--muted);">${i + 1}</td>
              <td style="padding:12px 14px; font-size:0.88rem; font-weight:600; color:var(--fg);">${escapeHtml(item.service_name)}</td>
              <td style="padding:12px 14px; font-size:0.82rem; color:var(--fg);">${escapeHtml(item.option_label)}</td>
              <td style="padding:12px 14px; font-size:0.85rem; text-align:right; font-weight:600;">${item.quantity}×</td>
              <td style="padding:12px 14px; font-size:0.85rem; text-align:right; color:#6b5a30;">${item.unit_price_formatted}</td>
              <td style="padding:12px 14px; font-size:0.88rem; text-align:right; font-weight:700; color:var(--primary);">${item.line_total_formatted}</td>
            </tr>
          `;
        });
        html += `
            </tbody>
          </table>
        `;

        panel.innerHTML = html;
        panel.setAttribute('data-loaded', '1');
      } catch (err) {
        panel.innerHTML = `
          <div style="padding:20px; text-align:center; color:#b3261e; font-size:0.85rem;">
            ❌ ${escapeHtml(err.message || 'Error loading services')}
          </div>
        `;
      }
    });
  });

  // ── Helpers ───────────────────────────────────────────────────
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
  function formatDate(str) {
    if (!str) return '';
    const d = new Date(str + 'T00:00:00');
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
  }
});
