import express from 'express';
import cookieSession from 'cookie-session';
import path from 'path';
import { fileURLToPath } from 'url';
import {
  getCategoryData,
  getCategories,
  getServices,
  getOptions,
  findService,
  findOption,
  getBookings,
  getBookingByRef,
  getBookingById,
  createBooking,
  updateBookingStatus,
  getSpaStats,
  authenticateAdmin,
  priceFmt
} from './data.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;

// Setup template engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(
  cookieSession({
    name: 'carolines_session',
    keys: ['caroline_secret_key_luxury_sanctuary_2026'],
    maxAge: 24 * 60 * 60 * 1000
  })
);

// Static assets
app.use('/assets', express.static(path.join(__dirname, 'assets')));
app.use(express.static(path.join(__dirname, 'public')));

// Admin Auth Middleware
function requireAdmin(req, res, next) {
  if (req.session && req.session.admin) {
    return next();
  }
  return res.redirect('/admin/login');
}

// ─────────────────────────────────────────────────────────────
// PUBLIC PAGES (Clean & PHP URL Compatibility)
// ─────────────────────────────────────────────────────────────

app.get(['/', '/index', '/index.php'], (req, res) => {
  res.render('index', {
    current: 'index',
    pageTitle: "Where Refined Living Meets Purposeful Connection",
    pageDesc: "Caroline's Place — A private sanctuary for women who lead, inspire, and create lasting impact."
  });
});

app.get(['/clubhouse', '/clubhouse.php'], (req, res) => {
  res.render('clubhouse', {
    current: 'clubhouse',
    pageTitle: "The Club House — Executive Sanctuary",
    pageDesc: "The Club House at Caroline's Place: Private lounges, boardroom, conference facilities, cigar bar, and curated events."
  });
});

app.get(['/spa', '/spa.php'], (req, res) => {
  res.render('spa', {
    current: 'spa',
    pageTitle: "N Lounge & Spa — Wellness & Beauty",
    pageDesc: "The Nail Lounge & Spa at Caroline's Place: Pedicures, manicures, massages, facial aesthetics, and restorative therapies."
  });
});

app.get(['/spa_menu', '/spa_menu.php'], (req, res) => {
  const catData = getCategoryData();
  res.render('spa_menu', {
    current: 'spa_menu',
    catData,
    priceFmt,
    pageTitle: "Spa Menu & Online Reservations",
    pageDesc: "Select bespoke treatments and build your luxury wellness package at Caroline's Place."
  });
});

app.post(['/spa_menu/review', '/spa_menu', '/spa_menu.php'], (req, res) => {
  const selected = Array.isArray(req.body['svc_selected[]'])
    ? req.body['svc_selected[]']
    : (req.body['svc_selected']
        ? (Array.isArray(req.body['svc_selected']) ? req.body['svc_selected'] : [req.body['svc_selected']])
        : (req.body['svc_selected[]'] ? [req.body['svc_selected[]']] : []));

  // Extract option and quantity maps
  const svcOption = req.body.svc_option || {};
  const svcQty = req.body.svc_qty || {};

  const lines = [];
  let total = 0;

  for (const sid of selected) {
    const sIdNum = Number(sid);
    const svc = findService(sIdNum);
    if (!svc) continue;

    const optId = svcOption[sid] ? Number(svcOption[sid]) : null;
    let opt = optId ? findOption(optId) : null;
    if (!opt && svc.options && svc.options.length > 0) {
      opt = svc.options[0];
    }

    const unitPrice = opt ? Number(opt.price_ngn) : Number(svc.price_ngn || 0);
    const qty = Math.max(1, parseInt(svcQty[sid], 10) || 1);
    const lineTotal = unitPrice * qty;
    total += lineTotal;

    lines.push({
      sid: svc.id,
      service_name: svc.name,
      option_id: opt ? opt.id : '',
      option_label: opt ? opt.option_label : 'Standard',
      unit_price: unitPrice,
      qty,
      line_total: lineTotal
    });
  }

  if (lines.length === 0) {
    return res.redirect('/spa_menu');
  }

  const timeSlots = [
    '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
    '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM',
    '05:00 PM', '06:00 PM', '07:00 PM'
  ];

  res.render('spa_review', {
    current: 'spa_menu',
    lines,
    total,
    timeSlots,
    priceFmt,
    pageTitle: "Review Your Spa Reservation"
  });
});

app.get(['/confirmation', '/confirmation.php'], (req, res) => {
  const ref = req.query.ref;
  let booking = null;

  if (ref) {
    booking = getBookingByRef(ref);
  }

  // Fallback to most recent booking if ref wasn't provided or found
  if (!booking) {
    const all = getBookings('all');
    if (all.length > 0) {
      booking = all[0];
    }
  }

  if (!booking) {
    return res.redirect('/spa_menu');
  }

  res.render('confirmation', {
    current: 'spa',
    booking,
    items: booking.items || [],
    priceFmt,
    pageTitle: "Reservation Confirmation — " + booking.reference_code
  });
});

// ─────────────────────────────────────────────────────────────
// ADMIN PORTAL
// ─────────────────────────────────────────────────────────────

app.get('/admin', (req, res) => {
  res.redirect('/admin/dashboard');
});

app.get(['/admin/login', '/admin/login.php'], (req, res) => {
  if (req.session && req.session.admin) {
    return res.redirect('/admin/dashboard');
  }
  res.render('admin/login', {
    error: null,
    username: ''
  });
});

app.post(['/admin/login', '/admin/login.php'], (req, res) => {
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';

  const user = authenticateAdmin(username, password);
  if (user) {
    req.session.admin = user;
    return res.redirect('/admin/dashboard');
  }

  res.render('admin/login', {
    error: 'Invalid username or password. Please verify your concierge credentials.',
    username
  });
});

app.get(['/admin/logout', '/admin/logout.php', '/api/logout', '/api/logout.php'], (req, res) => {
  req.session = null;
  res.redirect('/admin/login');
});

app.get(['/admin/dashboard', '/admin/dashboard.php'], requireAdmin, (req, res) => {
  const status = req.query.status || 'all';
  const bookings = getBookings(status);
  const stats = getSpaStats();

  res.render('admin/dashboard', {
    admin: req.session.admin,
    stats,
    bookings,
    currentStatus: status,
    priceFmt
  });
});

app.get(['/admin/spa_products', '/admin/spa_products.php'], requireAdmin, (req, res) => {
  const catData = getCategoryData();
  const totalServices = getServices().length;

  res.render('admin/spa_products', {
    admin: req.session.admin,
    catData,
    totalServices,
    priceFmt
  });
});

// ─────────────────────────────────────────────────────────────
// REST APIs (AJAX & EXTERNAL CLIENTS)
// ─────────────────────────────────────────────────────────────

// Spa Booking Submission
app.post(['/api/book_spa', '/api/book_spa.php', '/api/book', '/api/book.php'], (req, res) => {
  try {
    const rawSids = req.body['line_service_id[]'] || req.body['line_service_id'] || [];
    const rawOpts = req.body['line_option_id[]'] || req.body['line_option_id'] || [];
    const rawUnits = req.body['line_unit[]'] || req.body['line_unit'] || [];
    const rawQtys = req.body['line_qty[]'] || req.body['line_qty'] || [];

    const sids = Array.isArray(rawSids) ? rawSids : [rawSids];
    const opts = Array.isArray(rawOpts) ? rawOpts : [rawOpts];
    const units = Array.isArray(rawUnits) ? rawUnits : [rawUnits];
    const qtys = Array.isArray(rawQtys) ? rawQtys : [rawQtys];

    const lineItems = [];
    for (let i = 0; i < sids.length; i++) {
      if (!sids[i]) continue;
      lineItems.push({
        serviceId: Number(sids[i]),
        optionId: opts[i] ? Number(opts[i]) : null,
        unitPrice: Number(units[i] || 0),
        quantity: Number(qtys[i] || 1)
      });
    }

    const { booking } = createBooking(
      {
        fullName: req.body.full_name || 'Valued Guest',
        email: req.body.email || '',
        phone: req.body.phone || '',
        preferredDate: req.body.preferred_date || new Date().toISOString().split('T')[0],
        preferredTime: req.body.preferred_time || '10:00 AM',
        notes: req.body.notes || ''
      },
      lineItems
    );

    const isJson = req.xhr || (req.headers.accept && req.headers.accept.includes('application/json'));
    if (isJson) {
      return res.json({
        ok: true,
        reference_code: booking.reference_code,
        redirect: `/confirmation?ref=${booking.reference_code}`
      });
    }

    res.redirect(`/confirmation?ref=${booking.reference_code}`);
  } catch (err) {
    console.error('Booking submission error:', err);
    res.status(500).send('Unable to complete reservation. Please try again.');
  }
});

// Admin Booking Status & Notes update
app.post(['/api/update-booking', '/api/update-booking.php'], requireAdmin, (req, res) => {
  const id = req.body.id || req.body.booking_id;
  const status = req.body.status;
  const adminNotes = req.body.admin_notes;

  if (!id) {
    return res.status(400).json({ success: false, error: 'Missing booking ID' });
  }

  const ok = status ? updateBookingStatus(id, status) : true;
  if (adminNotes !== undefined) {
    const b = getBookingById(id);
    if (b) b.admin_notes = adminNotes;
  }

  res.json({ success: ok, id: Number(id) });
});

// Dashboard Telemetry Stats
app.get(['/api/stats', '/api/stats.php'], (req, res) => {
  res.json(getSpaStats());
});

// Category & Service Listing
app.get(['/api/services', '/api/services.php'], (req, res) => {
  res.json(getCategoryData());
});

// Modal Details for Specific Booking
app.get(['/api/admin_spa_items', '/api/admin_spa_items.php'], (req, res) => {
  const id = req.query.booking_id || req.query.id;
  if (!id) {
    return res.status(400).json({ ok: false, error: 'Missing booking id' });
  }

  const booking = getBookingById(id);
  if (!booking) {
    return res.status(404).json({ ok: false, error: 'Booking not found' });
  }

  const formattedItems = (booking.items || []).map((it) => ({
    ...it,
    unit_price_formatted: priceFmt(it.unit_price_ngn),
    line_total_formatted: priceFmt(it.line_total_ngn)
  }));

  res.json({
    ok: true,
    item_count: formattedItems.length,
    calculated_total: booking.total_amount_ngn,
    calculated_total_formatted: priceFmt(booking.total_amount_ngn),
    booking,
    items: formattedItems
  });
});

// Healthcheck
app.get(['/healthz', '/health', '/api/health'], (req, res) => {
  res.status(200).json({ status: 'ok', uptime: process.uptime(), time: new Date().toISOString() });
});

// 404 handler - redirect to home page
app.use((req, res) => {
  res.status(404).redirect('/');
});

// Global error handler
app.use((err, req, res, next) => {
  console.error('Unhandled request error:', err);
  if (!res.headersSent) {
    res.status(500).send('Internal Server Error');
  }
});

// Process-level resilience
process.on('uncaughtException', (err) => {
  console.error('Uncaught Exception:', err);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection:', reason);
});

// Start Server
const server = app.listen(PORT, '0.0.0.0', () => {
  console.log(`Caroline's Place Web Application running on http://0.0.0.0:${PORT}`);
});

server.on('error', (err) => {
  console.error('Server error on listen:', err);
});
