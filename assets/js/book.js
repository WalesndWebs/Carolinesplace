// ============================================================
// CAROLINE'S PLACE — Booking Form JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
  const divisionSel = document.getElementById('division');
  const serviceSel  = document.getElementById('service_id');
  const form        = document.getElementById('bookingForm');
  const submitBtn   = document.getElementById('submitBtn');
  const formAlert   = document.getElementById('formAlert');

  if (!divisionSel || !serviceSel) return;

  // ── Load services for a given division ──────────────────────
  function loadServices(division, selectedId, attempt = 1) {
    serviceSel.innerHTML = '<option value="">Loading...</option>';
    serviceSel.disabled = true;

    fetch('api/services.php?division=' + encodeURIComponent(division) + '&_=' + Date.now(), {
      cache: 'no-store'
    })
      .then(r => {
        if (!r.ok) throw new Error('Failed to fetch services');
        return r.json();
      })
      .then(data => {
        if (!Array.isArray(data)) throw new Error('Invalid services response');
        serviceSel.innerHTML = '<option value="">Select a service</option>';

        const grouped = new Map();
        data.forEach(s => {
          const category = s.category || 'Services';
          if (!grouped.has(category)) grouped.set(category, []);
          grouped.get(category).push(s);
        });

        grouped.forEach((items, category) => {
          const group = document.createElement('optgroup');
          group.label = category;

          items.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            if (selectedId && String(s.id) === String(selectedId)) {
              opt.selected = true;
            }
            group.appendChild(opt);
          });

          serviceSel.appendChild(group);
        });
        serviceSel.disabled = false;
      })
      .catch(() => {
        if (attempt < 2) {
          setTimeout(() => loadServices(division, selectedId, attempt + 1), 500);
          return;
        }
        serviceSel.innerHTML = '<option value="">Failed to load - refresh page</option>';
        serviceSel.disabled = false;
      });
  }

  // ── Initial load ─────────────────────────────────────────────
  const preselectedService  = divisionSel.dataset.service  || null;
  loadServices(divisionSel.value, preselectedService);

  divisionSel.addEventListener('change', () => {
    // ════════════════════════════════════════════════════════
    // SPA DIVISION: instead of loading single-service AJAX
    // dropdown, reload into the FULL SPA MENU inside book.php
    // (category chips, all services, live total)
    // ════════════════════════════════════════════════════════
    if (divisionSel.value === 'spa') {
      window.location.href = 'book.php?division=spa';
      return;
    }
    loadServices(divisionSel.value, null);
  });

  // ── Date: set min to today ────────────────────────────────────
  const dateInput = document.getElementById('preferred_date');
  if (dateInput) {
    const today = new Date();
    const yyyy  = today.getFullYear();
    const mm    = String(today.getMonth() + 1).padStart(2, '0');
    const dd    = String(today.getDate()).padStart(2, '0');
    dateInput.min = `${yyyy}-${mm}-${dd}`;
  }

  // ── Client-side validation ────────────────────────────────────
  function showError(fieldId, msg) {
    const el = document.getElementById('err_' + fieldId);
    if (el) { el.textContent = msg; el.classList.add('visible'); }
  }
  function clearErrors() {
    document.querySelectorAll('.form-error').forEach(e => {
      e.textContent = ''; e.classList.remove('visible');
    });
    if (formAlert) { formAlert.style.display = 'none'; formAlert.textContent = ''; }
  }

  function validate() {
    clearErrors();
    let ok = true;
    const fullName = document.getElementById('full_name');
    const email    = document.getElementById('email');
    const phone    = document.getElementById('phone');
    const date     = document.getElementById('preferred_date');
    const time     = document.getElementById('preferred_time');

    if (!fullName.value.trim() || fullName.value.trim().length < 2) {
      showError('full_name', 'Full name is required.'); ok = false;
    }
    if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      showError('email', 'A valid email address is required.'); ok = false;
    }
    if (!phone.value.trim() || phone.value.trim().length < 5) {
      showError('phone', 'Phone number is required.'); ok = false;
    }
    if (!serviceSel.value) {
      showError('service_id', 'Please select a service.'); ok = false;
    }
    if (!date.value) {
      showError('preferred_date', 'Please select a date.'); ok = false;
    }
    if (!time.value) {
      showError('preferred_time', 'Please select a time.'); ok = false;
    }
    return ok;
  }

  // ── Form submit via fetch ─────────────────────────────────────
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!validate()) return;

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner"></span>Processing...';
      submitBtn.classList.add('btn--loading');

      const data = new FormData(form);
      const body = {};
      data.forEach((v, k) => { body[k] = v; });

      fetch('api/book.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      })
        .then(r => r.json())
        .then(res => {
          if (res.error) throw new Error(res.error);
          // Redirect to confirmation
          window.location.href = 'confirmation.php?ref=' + encodeURIComponent(res.reference_code);
        })
        .catch(err => {
          if (formAlert) {
            formAlert.textContent = err.message || 'Something went wrong. Please try again.';
            formAlert.style.display = 'block';
          }
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Submit Request';
          submitBtn.classList.remove('btn--loading');
        });
    });
  }
});
