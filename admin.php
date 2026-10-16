<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/database/db.php';

// Never let the browser (or its back/forward cache) serve a cached copy
// of this page — every visit (including the Back button) must hit the
// server fresh.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals(ADMIN_PASSWORD, (string)($_POST['password'] ?? ''))) {
        // Single-use flag: redirect to a plain GET so the dashboard URL
        // never sits behind a resubmittable POST.
        $_SESSION['admin_one_time'] = true;
        header('Location: admin.php');
        exit;
    }
    $loginError = 'Incorrect password.';
}

// The dashboard renders once per successful login. The flag is consumed
// immediately, so a later visit to this same GET URL — the Back button,
// a refresh, or retyping the address — never finds it set and always
// falls back to the login form.
$isLoggedIn = !empty($_SESSION['admin_one_time']);
unset($_SESSION['admin_one_time']);

$leads = [];
$dbError = '';

if ($isLoggedIn && DB_ENABLED) {
    try {
        $conn = db_connect();
        $result = $conn->query('SELECT * FROM leads ORDER BY created_at DESC LIMIT 500');
        while ($row = $result->fetch_assoc()) {
            $leads[] = $row;
        }
        $conn->close();
    } catch (\Throwable $e) {
        $dbError = 'Could not load leads: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shiv Kailasa | Admin</title>
<style>
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: system-ui, -apple-system, Segoe UI, sans-serif;
        background: #0e0c0a;
        color: #e9e2d3;
    }
    .wrap { max-width: 1320px; margin: 0 auto; padding: 32px 24px; }
    a { color: #c9a96a; }
    .login-box {
        max-width: 360px;
        margin: 120px auto;
        background: rgba(165, 161, 154, .06);
        border: 1px solid #ffffff2e;
        padding: 32px;
        border-radius: 4px;
    }
    .login-box h1 { font-size: 20px; margin: 0 0 20px; font-weight: 500; }
    .login-box input {
        width: 100%;
        padding: 10px 12px;
        background: #171410;
        border: 1px solid #ffffff3d;
        color: #e9e2d3;
        border-radius: 3px;
        font-size: 14px;
    }
    .pw-wrap {
        position: relative;
        margin-bottom: 14px;
    }
    .pw-wrap input {
        margin-bottom: 0;
        padding-right: 40px;
    }
    .pw-wrap .pw-toggle {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: auto;
        height: auto;
        background: none;
        border: 0;
        border-radius: 0;
        color: #a5a19a;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: none;
    }
    .pw-wrap .pw-toggle:hover { color: #c9a96a; }
    .login-box button {
        width: 100%;
        padding: 11px;
        background: #c9a96a;
        border: 0;
        color: #0e0c0a;
        font-weight: 600;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .error { color: #d98b7a; font-size: 13px; margin-bottom: 14px; }
    header.bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 12px;
    }
    header.bar h1 { font-size: 22px; font-weight: 500; margin: 0; }
    .count { color: #a5a19a; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td {
        text-align: left;
        padding: 10px 12px;
        border-bottom: 1px solid #ffffff1a;
        white-space: nowrap;
    }
    th {
        color: #a5a19a;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: .08em;
        font-weight: 500;
    }
    td.wrap-cell { white-space: normal; max-width: 220px; }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .badge.hot { background: rgba(201,169,106,.18); color: #d8b97c; border: 1px solid rgba(201,169,106,.4); }
    .badge.future { background: rgba(165,161,154,.12); color: #a5a19a; border: 1px solid #ffffff2e; }
    .badge.slot { background: rgba(126,170,120,.15); color: #8fc98a; border: 1px solid rgba(126,170,120,.4); }
    .muted { color: #6f695d; }
    .table-wrap { overflow-x: auto; border: 1px solid #ffffff1a; border-radius: 4px; }
    .empty { padding: 60px 20px; text-align: center; color: #a5a19a; }
</style>
</head>
<body>
<script>
    // Force this page out of the back/forward cache. Modern Chrome no
    // longer treats Cache-Control: no-store as disqualifying bfcache, so
    // without this, pressing Back can restore the dashboard from memory
    // without ever asking the server again.
    window.addEventListener('unload', function () {});
</script>

<?php if (!$isLoggedIn): ?>
<div class="login-box">
    <h1>Admin login</h1>
    <?php if ($loginError): ?><div class="error"><?= htmlspecialchars($loginError) ?></div><?php endif; ?>
    <form method="post">
        <div class="pw-wrap">
            <input type="password" id="pwInput" name="password" placeholder="Password" autofocus>
            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password">
                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>
        <button type="submit">Sign in</button>
    </form>
    <script>
        (function () {
            var input = document.getElementById('pwInput');
            var toggle = document.getElementById('pwToggle');
            var icon = document.getElementById('eyeIcon');
            var eyeOpen = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle>';
            var eyeOff = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.35 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';

            toggle.addEventListener('click', function () {
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                icon.innerHTML = showing ? eyeOpen : eyeOff;
                toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        })();
    </script>
</div>
<?php else: ?>
<div class="wrap">
    <header class="bar">
        <h1>Leads &amp; site visits</h1>
        <div>
            <span class="count"><?= count($leads) ?> record<?= count($leads) === 1 ? '' : 's' ?></span>
            &nbsp; · &nbsp;
            <a href="admin.php">Log out</a>
        </div>
    </header>

    <?php if ($dbError): ?>
        <div class="error"><?= htmlspecialchars($dbError) ?></div>
    <?php elseif (!DB_ENABLED): ?>
        <div class="error">Database is disabled (DB_ENABLED=false).</div>
    <?php elseif (empty($leads)): ?>
        <div class="table-wrap"><div class="empty">No submissions yet.</div></div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Received</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Type</th>
                <th>Purpose / Config</th>
                <th>Preferred location</th>
                <th>Current city</th>
                <th>Visit slot</th>
                <th>Tag</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td><?= htmlspecialchars($lead['created_at']) ?></td>
                <td><?= htmlspecialchars($lead['name']) ?></td>
                <td><a href="tel:<?= htmlspecialchars($lead['phone']) ?>"><?= htmlspecialchars($lead['phone']) ?></a></td>
                <td><?= htmlspecialchars($lead['email'] ?: '—') ?></td>
                <td><?= htmlspecialchars($lead['form_type']) ?></td>
                <td><?= htmlspecialchars(trim(($lead['purpose'] ?? '') . ' ' . ($lead['configuration'] ?? '')) ?: '—') ?></td>
                <td><?= htmlspecialchars($lead['preferred_location'] ?: '—') ?></td>
                <td><?= htmlspecialchars($lead['current_city'] ?: '—') ?></td>
                <td>
                    <?php if (!empty($lead['visit_day']) || !empty($lead['visit_time'])): ?>
                        <span class="badge slot">
                            <?= htmlspecialchars(trim(($lead['visit_day'] ?? '') . ' ' . ($lead['visit_time'] ?? ''))) ?>
                        </span>
                    <?php else: ?>
                        <span class="muted">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($lead['lead_tag'] === 'MIHAN'): ?>
                        <span class="badge hot">MIHAN</span>
                    <?php elseif ($lead['lead_tag']): ?>
                        <span class="badge future">Future</span>
                    <?php else: ?>
                        <span class="muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="wrap-cell"><?= htmlspecialchars($lead['message'] ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

</body>
</html>
