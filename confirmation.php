<?php
$pageTitle = "Booking Confirmed";
$root      = './';

require_once __DIR__ . '/api/db.php';

$ref    = trim($_GET['ref']    ?? '');
$source = trim($_GET['source'] ?? '');

$isSpa = ($source === 'spa') || (strpos($ref, 'SPA-') === 0);

if ($isSpa) {

    if (!$ref || !preg_match('/^SPA-[A-Z0-9]{8}$/', $ref)) {
        header('Location: index.php');
        exit;
    }

    $db   = getDB();

    $stmt = $db->prepare("SELECT * FROM spa_bookings WHERE reference_code = ?");
    $stmt->execute([$ref]);
    $b = $stmt->fetch();

    if (!$b) {
        header('Location: index.php');
        exit;
    }

    $itemsStmt = $db->prepare("
        SELECT * FROM spa_booking_items
        WHERE booking_id = ?
        ORDER BY id ASC
    ");
    $itemsStmt->execute([(int)$b['id']]);
    $items = $itemsStmt->fetchAll();

    $firstName = explode(' ', trim($b['full_name']))[0];
    $dateFormatted = date('F j, Y', strtotime($b['preferred_date']));
    $grandTotal = (float)$b['total_amount_ngn'];

    include __DIR__ . '/includes/header.php';
?>

<div class="confirmation">
  <div class="confirmation__card" style="max-width:680px;">

    <div class="confirmation__icon">&#10003;</div>

    <span class="section__label" style="display:block;text-align:center;margin-bottom:8px;">Spa Booking Received</span>
    <h1 style="font-family:var(--font-serif);font-size:2rem;text-align:center;margin-bottom:8px;">
      Thank You, <?= htmlspecialchars($firstName) ?>
    </h1>
    <p style="color:var(--muted);text-align:center;font-weight:300;margin-bottom:24px;">
      Your spa booking has been received. Our concierge team will be in touch shortly to confirm your appointment.
    </p>

    <div style="text-align:center;">
      <div style="font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
        Your Reference Code
      </div>
      <div class="confirmation__ref"><?= htmlspecialchars($b['reference_code']) ?></div>
      <p style="font-size:0.75rem;color:var(--muted);margin-top:8px;">
        Please save this code to track your booking.
      </p>
    </div>

    <div class="confirmation__details" style="margin:32px 0 24px;">
      <div class="confirmation__row">
        <span class="confirmation__key">Preferred Date</span>
        <span class="confirmation__val"><?= htmlspecialchars($dateFormatted) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Preferred Time</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['preferred_time']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Name</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['full_name']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Email</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['email']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Phone</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['phone']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Status</span>
        <span class="confirmation__val" style="text-transform:capitalize;"><?= htmlspecialchars($b['status']) ?></span>
      </div>
    </div>

    <div style="margin-bottom:8px;">
      <div style="font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
        Booking Items
      </div>

      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
          <thead>
            <tr style="border-bottom:1px solid var(--border-soft);">
              <th style="text-align:left;padding:10px 8px;font-size:0.62rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:400;">Service</th>
              <th style="text-align:left;padding:10px 8px;font-size:0.62rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:400;">Option</th>
              <th style="text-align:right;padding:10px 8px;font-size:0.62rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:400;">Unit</th>
              <th style="text-align:center;padding:10px 8px;font-size:0.62rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:400;">Qty</th>
              <th style="text-align:right;padding:10px 8px;font-size:0.62rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:400;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr style="border-bottom:1px solid var(--border-soft);">
                <td style="padding:10px 8px;font-weight:500;"><?= htmlspecialchars($it['service_name']) ?></td>
                <td style="padding:10px 8px;color:var(--muted);"><?= htmlspecialchars($it['option_label']) ?></td>
                <td style="padding:10px 8px;text-align:right;font-variant-numeric:tabular-nums;"><?= priceFmt($it['unit_price_ngn']) ?></td>
                <td style="padding:10px 8px;text-align:center;font-variant-numeric:tabular-nums;"><?= (int)$it['quantity'] ?></td>
                <td style="padding:10px 8px;text-align:right;font-weight:600;font-variant-numeric:tabular-nums;"><?= priceFmt($it['line_total_ngn']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4" style="padding:16px 8px 8px;text-align:right;font-size:0.68rem;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">Grand Total</td>
              <td style="padding:16px 8px 8px;text-align:right;font-weight:700;font-size:1.15rem;font-family:var(--font-serif);color:var(--primary);font-variant-numeric:tabular-nums;"><?= priceFmt($grandTotal) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:16px;">
      <a href="index.php" class="btn btn--outline" style="flex:1;min-width:140px;text-align:center;">Return Home</a>
      <a href="spa.php"   class="btn btn--primary" style="flex:1;min-width:140px;text-align:center;">New Spa Booking</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
} else {

if (!$ref || !preg_match('/^CP-[A-Z0-9]{8}$/', $ref)) {
    header('Location: index.php');
    exit;
}

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM bookings WHERE reference_code = ?");
$stmt->execute([$ref]);
$b    = $stmt->fetch();

if (!$b) {
    header('Location: index.php');
    exit;
}

$divisionLabel = $b['division'] === 'clubhouse' ? 'The Club House' : 'N Lounge & Spa';
$dateFormatted = date('F j, Y', strtotime($b['preferred_date']));

include 'includes/header.php';
?>

<div class="confirmation">
  <div class="confirmation__card">

    <div class="confirmation__icon">&#10003;</div>

    <span class="section__label" style="display:block;text-align:center;margin-bottom:8px;">Booking Received</span>
    <h1 style="font-family:var(--font-serif);font-size:2rem;text-align:center;margin-bottom:8px;">
      Thank You, <?= htmlspecialchars(explode(' ', $b['full_name'])[0]) ?>
    </h1>
    <p style="color:var(--muted);text-align:center;font-weight:300;margin-bottom:24px;">
      Your request has been received. Our concierge team will be in touch shortly.
    </p>

    <div style="text-align:center;">
      <div style="font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
        Your Reference Code
      </div>
      <div class="confirmation__ref"><?= htmlspecialchars($b['reference_code']) ?></div>
      <p style="font-size:0.75rem;color:var(--muted);margin-top:8px;">
        Please save this code to track your booking.
      </p>
    </div>

    <div class="confirmation__details">
      <div class="confirmation__row">
        <span class="confirmation__key">Division</span>
        <span class="confirmation__val"><?= htmlspecialchars($divisionLabel) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Service</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['service_name']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Date</span>
        <span class="confirmation__val"><?= htmlspecialchars($dateFormatted) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Time</span>
        <span class="confirmation__val"><?= htmlspecialchars($b['preferred_time']) ?></span>
      </div>
      <div class="confirmation__row">
        <span class="confirmation__key">Status</span>
        <span class="confirmation__val" style="text-transform:capitalize;"><?= htmlspecialchars($b['status']) ?></span>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-top:8px;">
      <a href="index.php" class="btn btn--outline" style="flex:1;min-width:140px;text-align:center;">Return Home</a>
      <a href="spa_menu.php"  class="btn btn--primary" style="flex:1;min-width:140px;text-align:center;">New Booking</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php } ?>
