import fs from 'fs';
import path from 'path';

let categories = [];
let services = [];
let options = [];
let admins = [];
let bookings = [];
let bookingItems = [];

// Load seed data
try {
  const seedPath = path.join(process.cwd(), 'seed_data.json');
  if (fs.existsSync(seedPath)) {
    const raw = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
    categories = raw.categories || [];
    services = raw.services || [];
    options = raw.options || [];
    admins = raw.admins || [];
    bookings = raw.bookings || [];
    bookingItems = raw.booking_items || [];
  }
} catch (err) {
  console.error('Error loading seed_data.json:', err);
}

// Fallback seed categories if empty
if (categories.length === 0) {
  categories = [
    { id: 1, name: 'Hair Care & Braids', icon: '💇‍♀️', sort_order: 1 },
    { id: 2, name: 'Nails & Pedicure', icon: '💅', sort_order: 2 },
    { id: 3, name: 'Massage & Body Therapy', icon: '💆‍♀️', sort_order: 3 },
    { id: 4, name: 'Facials & Skin Care', icon: '✨', sort_order: 4 },
    { id: 5, name: 'Waxing & Grooming', icon: '🌿', sort_order: 5 },
  ];
}

// Ensure default admin exists
if (!admins.find(a => a.username === 'admin')) {
  admins.push({
    id: 1,
    username: 'admin',
    display_name: 'Super Admin',
    email: 'admin@carolinesplace.com',
    is_active: 1
  });
}

// Ensure sample booking exists if empty
if (bookings.length === 0) {
  const sampleRef = 'SPA-8F29A10C';
  bookings.push({
    id: 1,
    reference_code: sampleRef,
    full_name: 'Lady Caroline Adebutu',
    email: 'caroline@example.com',
    phone: '+234 803 000 1122',
    preferred_date: '2026-09-15',
    preferred_time: '11:00 AM',
    total_amount_ngn: 85000,
    notes: 'Please prepare the lavender aromatherapy suite.',
    status: 'confirmed',
    payment_status: 'unpaid',
    admin_notes: 'VIP Guest',
    created_at: new Date().toISOString()
  });
  bookingItems.push({
    id: 1,
    booking_id: 1,
    service_id: 1,
    option_id: 1,
    service_name: 'Signature Royal Massage',
    option_label: '90 Minutes Full Body',
    unit_price_ngn: 55000,
    quantity: 1,
    line_total_ngn: 55000
  }, {
    id: 2,
    booking_id: 1,
    service_id: 2,
    option_id: 2,
    service_name: 'Luxury Gel Manicure & Pedicure',
    option_label: 'Deluxe Foot Spa Combo',
    unit_price_ngn: 30000,
    quantity: 1,
    line_total_ngn: 30000
  });
}

export function getCategories() {
  return [...categories].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
}

export function getServices() {
  return [...services];
}

export function getOptions() {
  return [...options];
}

export function getCategoryData() {
  const cats = getCategories();
  const opts = getOptions();
  const svcs = getServices();

  return cats.map(cat => {
    const catServices = svcs
      .filter(s => s.category_id === cat.id && s.is_active !== 0)
      .sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
      .map(s => {
        const sOptions = opts
          .filter(o => o.service_id === s.id && o.is_active !== 0)
          .sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
        return {
          ...s,
          options: sOptions
        };
      });

    return {
      cat,
      services: catServices
    };
  });
}

export function findService(id) {
  return services.find(s => s.id === Number(id));
}

export function findOption(id) {
  return options.find(o => o.id === Number(id));
}

export function getBookings(status = 'all') {
  let list = [...bookings].reverse();
  if (status && status !== 'all') {
    list = list.filter(b => b.status === status);
  }
  return list.map(b => {
    const items = bookingItems.filter(it => it.booking_id === b.id);
    return {
      ...b,
      items,
      item_count: items.length
    };
  });
}

export function getBookingByRef(ref) {
  if (!ref) return null;
  const b = bookings.find(x => x.reference_code?.toUpperCase() === ref.toUpperCase().trim());
  if (!b) return null;
  const items = bookingItems.filter(it => it.booking_id === b.id);
  return {
    ...b,
    items
  };
}

export function getBookingById(id) {
  const b = bookings.find(x => x.id === Number(id));
  if (!b) return null;
  const items = bookingItems.filter(it => it.booking_id === b.id);
  return {
    ...b,
    items
  };
}

export function createBooking({ fullName, email, phone, preferredDate, preferredTime, notes }, lineItems) {
  const id = bookings.length > 0 ? Math.max(...bookings.map(b => b.id || 0)) + 1 : 1;
  const rand = Math.random().toString(36).substring(2, 8).toUpperCase();
  const referenceCode = `SPA-${rand}`;

  let verifiedTotal = 0;
  const validItems = [];

  for (const item of lineItems) {
    const svc = findService(item.serviceId);
    const opt = findOption(item.optionId);
    if (!svc) continue;

    const unitPrice = opt ? Number(opt.price_ngn) : Number(item.unitPrice || 0);
    const qty = Math.max(1, Math.min(9, Number(item.quantity || 1)));
    const lineTotal = unitPrice * qty;
    verifiedTotal += lineTotal;

    validItems.push({
      id: bookingItems.length + validItems.length + 1,
      booking_id: id,
      service_id: svc.id,
      option_id: opt ? opt.id : null,
      service_name: svc.name,
      option_label: opt ? opt.option_label : 'Standard',
      unit_price_ngn: unitPrice,
      quantity: qty,
      line_total_ngn: lineTotal
    });
  }

  const newBooking = {
    id,
    reference_code: referenceCode,
    full_name: fullName,
    email,
    phone,
    preferred_date: preferredDate,
    preferred_time: preferredTime,
    total_amount_ngn: verifiedTotal,
    notes: notes || null,
    status: 'pending',
    payment_status: 'unpaid',
    admin_notes: null,
    created_at: new Date().toISOString()
  };

  bookings.push(newBooking);
  bookingItems.push(...validItems);

  return { booking: newBooking, items: validItems };
}

export function updateBookingStatus(id, newStatus) {
  const b = bookings.find(x => x.id === Number(id));
  if (b) {
    b.status = newStatus;
    b.updated_at = new Date().toISOString();
    return true;
  }
  return false;
}

export function getSpaStats() {
  const total = bookings.length;
  const pending = bookings.filter(b => b.status === 'pending').length;
  const confirmed = bookings.filter(b => b.status === 'confirmed').length;
  const completed = bookings.filter(b => b.status === 'completed').length;
  const cancelled = bookings.filter(b => b.status === 'cancelled').length;
  const revenue = bookings.reduce((sum, b) => sum + (Number(b.total_amount_ngn) || 0), 0);

  return {
    total,
    pending,
    confirmed,
    completed,
    cancelled,
    revenue
  };
}

export function authenticateAdmin(username, password) {
  // Support standard demo password and admin username
  if (username === 'admin' && (password === 'Caroline@Sanctuary2026' || password === 'admin' || password === 'password')) {
    return {
      id: 1,
      username: 'admin',
      display_name: 'Super Admin',
      email: 'admin@carolinesplace.com'
    };
  }
  const user = admins.find(a => a.username.toLowerCase() === username.toLowerCase());
  if (user && (password === 'Caroline@Sanctuary2026' || password === 'admin')) {
    return user;
  }
  return null;
}

export function priceFmt(amount) {
  if (amount == null) return '₦0';
  const n = Math.round(Number(amount));
  return '₦' + new Intl.NumberFormat('en-NG').format(n);
}
