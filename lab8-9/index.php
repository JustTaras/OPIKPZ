<?php
// ----------------------------------------------------
// ПІДКЛЮЧЕННЯ ДО БАЗИ ДАНИХ
// ----------------------------------------------------
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ComputerClubDB";

$mysqli = new mysqli($host, $user, $password, $dbname);
if ($mysqli->connect_error) {
    die("Помилка підключення до бази даних: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// Повідомлення для інтерфейсу
$clientMessage   = "";
$computerMessage = "";
$sessionMessage  = "";
$paymentMessage  = "";

// ----------------------------------------------------
// ОБРОБКА ФОРМ
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // ----- ДОДАВАННЯ КЛІЄНТА -----
    if ($_POST['action'] === 'add_client') {
        $full_name = trim($_POST['full_name'] ?? "");
        $phone     = trim($_POST['phone'] ?? "");
        $email     = trim($_POST['email'] ?? "");

        if ($full_name === "" || $phone === "") {
            $clientMessage = "Помилка: ПІБ та телефон є обов'язковими.";
        } else {
            $stmt = $mysqli->prepare("
                INSERT INTO Clients (full_name, phone, email, status, bonus_points, registration_date)
                VALUES (?, ?, ?, 'active', 0, CURRENT_DATE)
            ");
            if ($stmt) {
                $stmt->bind_param("sss", $full_name, $phone, $email);
                if ($stmt->execute()) {
                    $clientMessage = "Клієнта успішно додано.";
                } else {
                    $clientMessage = "Помилка вставки: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $clientMessage = "Помилка запиту: " . $mysqli->error;
            }
        }
    }

    // ----- ДОДАВАННЯ КОМП'ЮТЕРА -----
    if ($_POST['action'] === 'add_computer') {
        $inventory_number = trim($_POST['inventory_number'] ?? "");
        $room             = trim($_POST['room'] ?? "");
        $specs            = trim($_POST['specs'] ?? "");
        $status           = trim($_POST['status'] ?? "available");
        $last_service     = trim($_POST['last_service'] ?? "");

        if ($inventory_number === "" || $room === "") {
            $computerMessage = "Помилка: інвентарний номер та зал є обов'язковими.";
        } else {
            if ($last_service === "") {
                $stmt = $mysqli->prepare("
                    INSERT INTO Computers (inventory_number, room, specs, status)
                    VALUES (?, ?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param("ssss", $inventory_number, $room, $specs, $status);
                }
            } else {
                $stmt = $mysqli->prepare("
                    INSERT INTO Computers (inventory_number, room, specs, status, last_service)
                    VALUES (?, ?, ?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param("sssss", $inventory_number, $room, $specs, $status, $last_service);
                }
            }

            if (isset($stmt) && $stmt) {
                if ($stmt->execute()) {
                    $computerMessage = "Комп'ютер успішно додано.";
                } else {
                    $computerMessage = "Помилка вставки: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $computerMessage = "Помилка запиту: " . $mysqli->error;
            }
        }
    }

    // ----- ДОДАВАННЯ СЕАНСУ -----
    if ($_POST['action'] === 'add_session') {
        $id_client   = intval($_POST['id_client'] ?? 0);
        $id_computer = intval($_POST['id_computer'] ?? 0);
        $id_tariff   = intval($_POST['id_tariff'] ?? 0);

        $start_raw = trim($_POST['start_time'] ?? "");
        $end_raw   = trim($_POST['end_time'] ?? "");

        $start_time = $start_raw ? str_replace("T", " ", $start_raw) . ":00" : "";
        $end_time   = $end_raw   ? str_replace("T", " ", $end_raw)   . ":00" : null;

        $duration   = $_POST['duration']   !== "" ? floatval($_POST['duration'])   : null;
        $total_cost = $_POST['total_cost'] !== "" ? floatval($_POST['total_cost']) : null;

        if ($id_client === 0 || $id_computer === 0 || $id_tariff === 0 || $start_time === "") {
            $sessionMessage = "Помилка: клієнт, комп'ютер, тариф і час початку є обов'язковими.";
        } else {
            if ($end_time === null) {
                $stmt = $mysqli->prepare("
                    INSERT INTO Sessions (id_client, id_computer, id_tariff, start_time, duration, total_cost)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param("iiisdd", $id_client, $id_computer, $id_tariff, $start_time, $duration, $total_cost);
                }
            } else {
                $stmt = $mysqli->prepare("
                    INSERT INTO Sessions (id_client, id_computer, id_tariff, start_time, end_time, duration, total_cost)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param("iiissdd", $id_client, $id_computer, $id_tariff, $start_time, $end_time, $duration, $total_cost);
                }
            }

            if (isset($stmt) && $stmt) {
                if ($stmt->execute()) {
                    $sessionMessage = "Сеанс успішно створено.";
                } else {
                    $sessionMessage = "Помилка вставки: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $sessionMessage = "Помилка запиту: " . $mysqli->error;
            }
        }
    }

    // ----- ДОДАВАННЯ ПЛАТЕЖУ -----
    if ($_POST['action'] === 'add_payment') {
        $id_session = intval($_POST['id_session'] ?? 0);
        $amount     = floatval($_POST['amount'] ?? 0);
        $method     = trim($_POST['payment_method'] ?? "");
        $id_admin   = intval($_POST['id_admin'] ?? 0);

        if ($id_session === 0 || $amount <= 0 || $method === "" || $id_admin === 0) {
            $paymentMessage = "Помилка: усі поля платежу є обов'язковими.";
        } else {
            $stmt = $mysqli->prepare("
                INSERT INTO Payments (id_session, amount, payment_method, payment_date, id_admin)
                VALUES (?, ?, ?, NOW(), ?)
            ");
            if ($stmt) {
                $stmt->bind_param("idsi", $id_session, $amount, $method, $id_admin);
                if ($stmt->execute()) {
                    $paymentMessage = "Платіж успішно додано.";
                } else {
                    $paymentMessage = "Помилка вставки: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $paymentMessage = "Помилка запиту: " . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>База даних комп'ютерного клубу</title>
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        header {
            background: #111827;
            color: #f9fafb;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }
        header h1 {
            margin: 0;
            font-size: 20px;
        }
        header span {
            font-size: 13px;
            opacity: 0.8;
        }
        .container {
            display: flex;
            min-height: calc(100vh - 64px);
        }
        nav {
            width: 230px;
            background: #1f2937;
            color: #e5e7eb;
            padding: 16px;
            box-sizing: border-box;
        }
        nav h2 {
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
        }
        .menu-btn {
            width: 100%;
            text-align: left;
            border: none;
            padding: 10px 12px;
            margin-bottom: 6px;
            border-radius: 8px;
            background: transparent;
            color: #e5e7eb;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.15s, transform 0.05s;
        }
        .menu-btn:hover {
            background: #374151;
            transform: translateY(-1px);
        }
        .menu-btn.active {
            background: #4b5563;
        }
        main {
            flex: 1;
            padding: 20px 24px;
            box-sizing: border-box;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
        }
        .card h2 {
            margin-top: 0;
            margin-bottom: 4px;
            font-size: 18px;
        }
        .card p.subtitle {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 13px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 14px;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 13px;
            color: #4b5563;
        }
        tr:nth-child(even) td {
            background: #f9fafb;
        }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            background: #e5e7eb;
        }
        .tag.green {
            background: #dcfce7;
            color: #166534;
        }
        .tag.red {
            background: #fee2e2;
            color: #991b1b;
        }
        .info-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }
        .view {
            display: none;
        }
        .view.active {
            display: block;
        }
        .badge {
            background: #eef2ff;
            color: #3730a3;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            display:inline-block;
            margin-right:6px;
        }
        footer {
            margin-top: 14px;
            font-size: 11px;
            color: #9ca3af;
        }
        .form-row {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-bottom:12px;
            align-items:flex-end;
        }
        .form-group {
            display:flex;
            flex-direction:column;
            font-size:13px;
        }
        .form-group input,
        .form-group select {
            padding:6px 8px;
            border-radius:8px;
            border:1px solid #d1d5db;
            min-width:140px;
        }
        .btn-primary {
            padding:8px 14px;
            border:none;
            border-radius:999px;
            background:#111827;
            color:#f9fafb;
            font-size:13px;
            cursor:pointer;
        }
        .alert {
            margin-bottom:10px;
            padding:8px 10px;
            border-radius:8px;
            font-size:13px;
        }
        .alert-yellow { background:#fef3c7; color:#92400e; }
        .alert-blue   { background:#e0f2fe; color:#0c4a6e; }
        .alert-green  { background:#dcfce7; color:#166534; }
    </style>
</head>
<body>
<header>
    <div>
        <h1>База даних комп'ютерного клубу</h1>
        <span>Навчальна демонстрація роботи реляційної БД</span>
    </div>
    <div style="font-size:13px; color:#9ca3af;">
        Стан: <span style="color:#22c55e;">● підключено</span>
    </div>
</header>

<div class="container">
    <nav>
        <h2>Розділи</h2>
        <button class="menu-btn active" data-view="view-clients">Клієнти</button>
        <button class="menu-btn" data-view="view-computers">Комп'ютери</button>
        <button class="menu-btn" data-view="view-sessions">Сеанси</button>
        <button class="menu-btn" data-view="view-payments">Платежі</button>
    </nav>

    <main>
        <div class="card">

            <!-- КЛІЄНТИ -->
            <section id="view-clients" class="view active">
                <h2>Клієнти</h2>
                <p class="subtitle">Список клієнтів клубу та форма додавання нового клієнта.</p>

                <?php if ($clientMessage !== ""): ?>
                    <div class="alert alert-yellow"><?= htmlspecialchars($clientMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="form-row">
                    <input type="hidden" name="action" value="add_client">
                    <div class="form-group">
                        <label>ПІБ</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="text" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    <button type="submit" class="btn-primary">Додати клієнта</button>
                </form>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ПІБ</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Статус</th>
                        <th>Бонуси</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT id_client, full_name, phone, email, status, bonus_points
                            FROM Clients ORDER BY full_name";
                    if ($res = $mysqli->query($sql)) {
                        if ($res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $statusClass = ($row['status'] === 'active') ? 'green' : 'red';
                                echo "<tr>";
                                echo "<td>{$row['id_client']}</td>";
                                echo "<td>".htmlspecialchars($row['full_name'])."</td>";
                                echo "<td>".htmlspecialchars($row['phone'])."</td>";
                                echo "<td>".htmlspecialchars($row['email'])."</td>";
                                echo "<td><span class='tag {$statusClass}'>".htmlspecialchars($row['status'])."</span></td>";
                                echo "<td>{$row['bonus_points']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Немає даних у таблиці Clients.</td></tr>";
                        }
                        $res->free();
                    }
                    ?>
                    </tbody>
                </table>

                <div class="info-text">
                    Дані беруться з таблиці <strong>Clients</strong> за допомогою SELECT-запиту.
                </div>
            </section>

            <!-- КОМП'ЮТЕРИ -->
            <section id="view-computers" class="view">
                <h2>Комп'ютери</h2>
                <p class="subtitle">Довідник комп’ютерів клубу.</p>

                <?php if ($computerMessage !== ""): ?>
                    <div class="alert alert-blue"><?= htmlspecialchars($computerMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="form-row">
                    <input type="hidden" name="action" value="add_computer">
                    <div class="form-group">
                        <label>Інвентарний №</label>
                        <input type="text" name="inventory_number" required>
                    </div>
                    <div class="form-group">
                        <label>Зал</label>
                        <input type="text" name="room" required>
                    </div>
                    <div class="form-group">
                        <label>Характеристики</label>
                        <input type="text" name="specs">
                    </div>
                    <div class="form-group">
                        <label>Стан</label>
                        <select name="status">
                            <option value="available">available</option>
                            <option value="occupied">occupied</option>
                            <option value="service">service</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата обслуговування</label>
                        <input type="date" name="last_service">
                    </div>
                    <button type="submit" class="btn-primary">Додати комп'ютер</button>
                </form>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Інв. №</th>
                        <th>Зал</th>
                        <th>Характеристики</th>
                        <th>Стан</th>
                        <th>Останнє обслуговування</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT id_computer, inventory_number, room, specs, status, last_service
                            FROM Computers ORDER BY inventory_number";
                    if ($res = $mysqli->query($sql)) {
                        if ($res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $statusClass = ($row['status'] === 'available') ? 'green' : 'red';
                                echo "<tr>";
                                echo "<td>{$row['id_computer']}</td>";
                                echo "<td>".htmlspecialchars($row['inventory_number'])."</td>";
                                echo "<td>".htmlspecialchars($row['room'])."</td>";
                                echo "<td>".htmlspecialchars($row['specs'])."</td>";
                                echo "<td><span class='tag {$statusClass}'>".htmlspecialchars($row['status'])."</span></td>";
                                echo "<td>".htmlspecialchars($row['last_service'])."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Немає даних у таблиці Computers.</td></tr>";
                        }
                        $res->free();
                    }
                    ?>
                    </tbody>
                </table>

                <div class="info-text">
                    Таблиця <strong>Computers</strong> використовується як довідник обладнання.
                </div>
            </section>

            <!-- СЕАНСИ -->
            <section id="view-sessions" class="view">
                <h2>Сеанси</h2>
                <p class="subtitle">Журнал сеансів із приєднаними клієнтами, ПК та тарифами.</p>

                <?php if ($sessionMessage !== ""): ?>
                    <div class="alert alert-green"><?= htmlspecialchars($sessionMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="form-row">
                    <input type="hidden" name="action" value="add_session">

                    <div class="form-group">
                        <label>Клієнт</label>
                        <select name="id_client" required>
                            <option value="">Обрати...</option>
                            <?php
                            $resC = $mysqli->query("SELECT id_client, full_name FROM Clients ORDER BY full_name");
                            if ($resC) {
                                while ($c = $resC->fetch_assoc()) {
                                    echo "<option value='{$c['id_client']}'>".htmlspecialchars($c['full_name'])."</option>";
                                }
                                $resC->free();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Комп'ютер</label>
                        <select name="id_computer" required>
                            <option value="">Обрати...</option>
                            <?php
                            $resPc = $mysqli->query("SELECT id_computer, inventory_number FROM Computers ORDER BY inventory_number");
                            if ($resPc) {
                                while ($pc = $resPc->fetch_assoc()) {
                                    echo "<option value='{$pc['id_computer']}'>".htmlspecialchars($pc['inventory_number'])."</option>";
                                }
                                $resPc->free();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Тариф</label>
                        <select name="id_tariff" required>
                            <option value="">Обрати...</option>
                            <?php
                            $resT = $mysqli->query("SELECT id_tariff, name FROM Tariffs WHERE active = 1 ORDER BY name");
                            if ($resT) {
                                while ($t = $resT->fetch_assoc()) {
                                    echo "<option value='{$t['id_tariff']}'>".htmlspecialchars($t['name'])."</option>";
                                }
                                $resT->free();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Початок</label>
                        <input type="datetime-local" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label>Завершення</label>
                        <input type="datetime-local" name="end_time">
                    </div>

                    <div class="form-group">
                        <label>Тривалість (год)</label>
                        <input type="number" step="0.1" name="duration">
                    </div>

                    <div class="form-group">
                        <label>Вартість (₴)</label>
                        <input type="number" step="0.01" name="total_cost">
                    </div>

                    <button type="submit" class="btn-primary">Додати сеанс</button>
                </form>

                <div class="badge">JOIN Clients</div>
                <div class="badge">JOIN Computers</div>
                <div class="badge">JOIN Tariffs</div>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Клієнт</th>
                        <th>Комп'ютер</th>
                        <th>Тариф</th>
                        <th>Початок</th>
                        <th>Завершення</th>
                        <th>Тривалість</th>
                        <th>Вартість</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "
                        SELECT 
                            s.id_session,
                            c.full_name AS client_name,
                            comp.inventory_number AS pc_number,
                            t.name AS tariff_name,
                            s.start_time,
                            s.end_time,
                            s.duration,
                            s.total_cost
                        FROM Sessions s
                        JOIN Clients c ON s.id_client = c.id_client
                        JOIN Computers comp ON s.id_computer = comp.id_computer
                        JOIN Tariffs t ON s.id_tariff = t.id_tariff
                        ORDER BY s.start_time DESC
                        LIMIT 50
                    ";
                    if ($res = $mysqli->query($sql)) {
                        if ($res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id_session']}</td>";
                                echo "<td>".htmlspecialchars($row['client_name'])."</td>";
                                echo "<td>".htmlspecialchars($row['pc_number'])."</td>";
                                echo "<td>".htmlspecialchars($row['tariff_name'])."</td>";
                                echo "<td>".htmlspecialchars($row['start_time'])."</td>";
                                echo "<td>".htmlspecialchars($row['end_time'])."</td>";
                                echo "<td>{$row['duration']}</td>";
                                echo "<td>{$row['total_cost']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Немає даних у таблиці Sessions.</td></tr>";
                        }
                        $res->free();
                    }
                    ?>
                    </tbody>
                </table>
            </section>

            <!-- ПЛАТЕЖІ -->
            <section id="view-payments" class="view">
                <h2>Платежі</h2>
                <p class="subtitle">Облік оплат за сеанси з прив'язкою до адміністратора.</p>

                <?php if ($paymentMessage !== ""): ?>
                    <div class="alert alert-green"><?= htmlspecialchars($paymentMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="form-row">
                    <input type="hidden" name="action" value="add_payment">

                    <div class="form-group">
                        <label>Сеанс</label>
                        <select name="id_session" required>
                            <option value="">Обрати...</option>
                            <?php
                            $resS = $mysqli->query("SELECT id_session FROM Sessions ORDER BY id_session DESC");
                            if ($resS) {
                                while ($s = $resS->fetch_assoc()) {
                                    echo "<option value='{$s['id_session']}'>Сеанс #{$s['id_session']}</option>";
                                }
                                $resS->free();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Сума (₴)</label>
                        <input type="number" step="0.01" name="amount" required>
                    </div>

                    <div class="form-group">
                        <label>Спосіб оплати</label>
                        <select name="payment_method" required>
                            <option value="cash">Готівка</option>
                            <option value="card">Картка</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Адміністратор</label>
                        <select name="id_admin" required>
                            <option value="">Обрати...</option>
                            <?php
                            $resA = $mysqli->query("SELECT id_admin, full_name FROM Admins ORDER BY full_name");
                            if ($resA) {
                                while ($a = $resA->fetch_assoc()) {
                                    echo "<option value='{$a['id_admin']}'>".htmlspecialchars($a['full_name'])."</option>";
                                }
                                $resA->free();
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary">Додати платіж</button>
                </form>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Сеанс</th>
                        <th>Клієнт</th>
                        <th>Сума</th>
                        <th>Метод</th>
                        <th>Дата</th>
                        <th>Адміністратор</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "
                        SELECT 
                            p.id_payment,
                            p.id_session,
                            c.full_name AS client_name,
                            p.amount,
                            p.payment_method,
                            p.payment_date,
                            a.full_name AS admin_name
                        FROM Payments p
                        JOIN Sessions s ON p.id_session = s.id_session
                        JOIN Clients  c ON s.id_client  = c.id_client
                        JOIN Admins   a ON p.id_admin   = a.id_admin
                        ORDER BY p.payment_date DESC
                    ";
                    if ($res = $mysqli->query($sql)) {
                        if ($res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id_payment']}</td>";
                                echo "<td>#{$row['id_session']}</td>";
                                echo "<td>".htmlspecialchars($row['client_name'])."</td>";
                                echo "<td>{$row['amount']}</td>";
                                echo "<td>".htmlspecialchars($row['payment_method'])."</td>";
                                echo "<td>{$row['payment_date']}</td>";
                                echo "<td>".htmlspecialchars($row['admin_name'])."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Платежів поки немає.</td></tr>";
                        }
                        $res->free();
                    }
                    ?>
                    </tbody>
                </table>
            </section>

            <footer>
                Демонстраційна панель. Дані беруться напряму з MySQL-бази <strong>ComputerClubDB</strong>.
            </footer>
        </div>
    </main>
</div>

<script>
    const buttons = document.querySelectorAll('.menu-btn');
    const views   = document.querySelectorAll('.view');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const targetId = btn.dataset.view;
            views.forEach(v => {
                if (v.id === targetId) v.classList.add('active');
                else v.classList.remove('active');
            });
        });
    });
</script>
</body>
</html>
<?php
$mysqli->close();
?>