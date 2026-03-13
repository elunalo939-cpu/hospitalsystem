<?php
/*
 * FILE:    hospital.php
 * SAVE TO: C:\xampp\htdocs\hospital\hospital.php
 * TASK 1:  Afya Bora Hospital main homepage
 */

// ── 1. DATABASE CONNECTION ────────────────────────────────────────────────
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "PatientData";

$conn  = new mysqli($servername, $username, $password, $dbname);
$db_ok = ($conn->connect_error === null);

// ── 2. HANDLE ADD PATIENT FORM ────────────────────────────────────────────
$msg      = "";
$msgtype  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_patient"])) {
    if (!$db_ok) {
        $msg     = "Database not connected. Please run create_db.sql first.";
        $msgtype = "error";
    } else {
        $fn  = trim($_POST["firstname"]);
        $ln  = trim($_POST["lastname"]);
        $id  = trim($_POST["idnumber"]);
        $gen = $_POST["gender"];
        $dia = trim($_POST["diagnosis"]);
        $drg = trim($_POST["drug"]);

        if ($fn === "" || $ln === "" || $id === "" || $gen === "" || $dia === "" || $drg === "") {
            $msg     = "All fields are required. Please fill in every field.";
            $msgtype = "error";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO patients (firstname, lastname, idnumber, gender, diagnosis, drug)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssss", $fn, $ln, $id, $gen, $dia, $drg);

            if ($stmt->execute()) {
                $msg     = "Patient record saved successfully!";
                $msgtype = "success";
            } else {
                $msg     = "Error saving record: " . $stmt->error;
                $msgtype = "error";
            }
            $stmt->close();
        }
    }
}

// ── 3. FETCH ALL PATIENTS ─────────────────────────────────────────────────
$patients = array();
if ($db_ok) {
    $result = $conn->query("SELECT * FROM patients ORDER BY id ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afya Bora Hospital</title>
    <style>

        /* ── RESET ──────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* ── HEADER  background-color #1 : dark blue ───── */
        header {
            background-color: #1a5276;
            color: #ffffff;
            padding: 18px 30px;
        }
        .header-inner {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .logo {
            width: 75px;
            height: 75px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            flex-shrink: 0;
        }
        .site-title h1 {
            font-size: 30px;
            letter-spacing: 1px;
        }
        .site-title p {
            font-size: 13px;
            color: #aed6f1;
            font-style: italic;
            margin-top: 3px;
        }

        /* ── NAVIGATION ─────────────────────────────────── */
        nav {
            background-color: #154360;
        }
        nav > ul {
            list-style: none;
            display: flex;
            padding: 0 30px;
        }
        nav > ul > li {
            position: relative;
        }
        nav > ul > li > a {
            display: block;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 20px;
            font-size: 15px;
            transition: background-color 0.25s;
        }
        nav > ul > li > a:hover,
        nav > ul > li > a.active {
            background-color: #2e86c1;
        }

        /* dropdown */
        nav > ul > li > ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #1a5276;
            min-width: 210px;
            z-index: 999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            list-style: none;
            counter-reset: drop-counter;
        }
        nav > ul > li:hover > ul {
            display: block;
        }
        nav > ul > li > ul > li {
            counter-increment: drop-counter;
        }
        nav > ul > li > ul > li > a {
            display: block;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 16px;
            font-size: 14px;
            border-bottom: 1px solid #2e6da4;
            transition: background-color 0.2s;
        }
        nav > ul > li > ul > li > a::before {
            content: counter(drop-counter) ". ";
            font-weight: bold;
        }
        nav > ul > li > ul > li > a:hover {
            background-color: #2e86c1;
        }

        /* ── MAIN BODY  background-color #2 : light blue ─ */
        main {
            background-color: #d6eaf8;
            padding: 30px;
            min-height: 62vh;
        }

        /* intro card */
        .intro-card {
            background-color: #ffffff;
            border-left: 5px solid #2e86c1;
            border-radius: 6px;
            padding: 24px 28px;
            margin-bottom: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .intro-card h2 {
            color: #1a5276;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .intro-card p {
            color: #444444;
            line-height: 1.85;
            font-size: 15px;
        }

        /* patient section */
        .patient-card {
            background-color: #ffffff;
            border-radius: 6px;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .patient-card h2 {
            color: #1a5276;
            font-size: 22px;
            border-bottom: 2px solid #2e86c1;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        /* alert messages */
        .alert {
            padding: 11px 15px;
            border-radius: 5px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d4efdf;
            color: #1d6a3a;
            border-left: 4px solid #27ae60;
        }
        .alert-error {
            background-color: #f9ebea;
            color: #922b21;
            border-left: 4px solid #e74c3c;
        }

        /* add-patient form */
        .add-form-wrap {
            background-color: #eaf4fb;
            padding: 18px 20px;
            border-radius: 6px;
            margin-bottom: 24px;
        }
        .add-form-wrap h3 {
            color: #154360;
            font-size: 16px;
            margin-bottom: 14px;
        }
        .fg-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 14px;
        }
        .fg label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #333333;
            margin-bottom: 4px;
        }
        .fg input,
        .fg select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #aaaaaa;
            border-radius: 4px;
            font-size: 14px;
        }
        .fg input:focus,
        .fg select:focus {
            outline: none;
            border-color: #2e86c1;
        }
        .btn-add {
            background-color: #1a5276;
            color: #ffffff;
            padding: 9px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-add:hover {
            background-color: #154360;
        }

        /* patient table */
        .tbl-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        table thead tr {
            background-color: #1a5276;
            color: #ffffff;
        }
        table th,
        table td {
            padding: 11px 13px;
            text-align: left;
        }
        table thead th {
            white-space: nowrap;
        }
        table tbody tr:nth-child(even) {
            background-color: #eaf4fb;
        }
        table tbody tr:hover {
            background-color: #d0e9f7;
        }
        table tbody td {
            border-bottom: 1px solid #cce0f0;
            color: #333333;
        }
        .no-records {
            text-align: center;
            color: #888888;
            padding: 22px;
        }

        /* ── FOOTER  background-color #3 : darkest blue ── */
        footer {
            background-color: #0e2d47;
            color: #aed6f1;
        }
        .marquee-bar {
            background-color: #2e86c1;
            padding: 8px 0;
        }
        .marquee-bar marquee {
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 24px 30px 16px;
        }
        .footer-col h3 {
            color: #ffffff;
            font-size: 15px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #2e6da4;
        }
        .footer-col p {
            font-size: 13px;
            line-height: 2;
            color: #aed6f1;
        }
        .footer-bottom {
            text-align: center;
            padding: 10px 30px;
            font-size: 12px;
            color: #7fb3d3;
            border-top: 1px solid #1a4a6b;
        }

    </style>
</head>
<body>

<!-- =====================================================
     HEADER
===================================================== -->
<header>
    <div class="header-inner">
        <div class="logo">&#127973;</div>
        <div class="site-title">
            <h1>Afya Bora Hospital</h1>
            <p>Quality Healthcare for Every Community &nbsp;|&nbsp; Est. 2022</p>
        </div>
    </div>
</header>

<!-- =====================================================
     NAVIGATION  (horizontal tabs with dropdowns)
===================================================== -->
<nav>
    <ul>
        <li>
            <a href="hospital.php" class="active">Home</a>
        </li>
        <li>
            <a href="#">Departments &#9660;</a>
            <ul>
                <li><a href="departments/cardiology.php">Cardiology</a></li>
                <li><a href="departments/orthopedics.php">Orthopedics</a></li>
                <li><a href="departments/pediatrics.php">Pediatrics</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Services Offered &#9660;</a>
            <ul>
                <li><a href="services/emergency.php">Emergency Care</a></li>
                <li><a href="services/laboratory.php">Laboratory Services</a></li>
                <li><a href="services/pharmacy.php">Pharmacy</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Careers &#9660;</a>
            <ul>
                <li><a href="careers/nursing.php">Nursing Positions</a></li>
                <li><a href="careers/doctors.php">Doctor Vacancies</a></li>
                <li><a href="careers/admin.php">Administrative Roles</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- =====================================================
     MAIN BODY
===================================================== -->
<main>

    <!-- Introduction paragraph (exact text from question) -->
    <div class="intro-card">
        <h2>Welcome to Afya Bora Hospital</h2>
        <p>
            Afya Bora Hospital was founded in 2022 to address the growing need for accessible,
            high-quality healthcare services in the rapidly expanding region. Our history is rooted
            in a commitment to community well-being and a vision for a healthier future.
        </p>
    </div>

    <!-- Patient Registration Table linked to PatientData database -->
    <div class="patient-card">
        <h2>Patient Registration</h2>

        <?php if ($msg !== ""): ?>
            <div class="alert <?php echo ($msgtype === "success") ? "alert-success" : "alert-error"; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- Add Patient Form -->
        <div class="add-form-wrap">
            <h3>Add New Patient</h3>
            <form method="POST" action="hospital.php">
                <div class="fg-grid">
                    <div class="fg">
                        <label>First Name *</label>
                        <input type="text" name="firstname" placeholder="e.g. John" required>
                    </div>
                    <div class="fg">
                        <label>Last Name *</label>
                        <input type="text" name="lastname" placeholder="e.g. Kamau" required>
                    </div>
                    <div class="fg">
                        <label>ID Number *</label>
                        <input type="text" name="idnumber" placeholder="e.g. 34567890" required>
                    </div>
                    <div class="fg">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label>Diagnosis *</label>
                        <input type="text" name="diagnosis" placeholder="e.g. Malaria" required>
                    </div>
                    <div class="fg">
                        <label>Drug Prescribed *</label>
                        <input type="text" name="drug" placeholder="e.g. Coartem" required>
                    </div>
                </div>
                <button type="submit" name="add_patient" class="btn-add">Add Patient</button>
            </form>
        </div>

        <!-- Display patients fetched from database -->
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>ID Number</th>
                        <th>Gender</th>
                        <th>Diagnosis</th>
                        <th>Drug</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($patients) > 0): ?>
                        <?php $num = 1; ?>
                        <?php foreach ($patients as $row): ?>
                            <tr>
                                <td><?php echo $num; ?></td>
                                <td><?php echo htmlspecialchars($row["firstname"]); ?></td>
                                <td><?php echo htmlspecialchars($row["lastname"]); ?></td>
                                <td><?php echo htmlspecialchars($row["idnumber"]); ?></td>
                                <td><?php echo htmlspecialchars($row["gender"]); ?></td>
                                <td><?php echo htmlspecialchars($row["diagnosis"]); ?></td>
                                <td><?php echo htmlspecialchars($row["drug"]); ?></td>
                                <td><?php echo htmlspecialchars($row["date_added"]); ?></td>
                            </tr>
                            <?php $num++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-records">
                                No patient records found. Use the form above to add patients.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- =====================================================
     FOOTER
===================================================== -->
<footer>
    <div class="marquee-bar">
        <marquee behavior="scroll" direction="left" scrollamount="5">
            &nbsp;&nbsp;&nbsp; Quick Recovery &nbsp;&nbsp;|&nbsp;&nbsp;
            Quick Recovery &nbsp;&nbsp;|&nbsp;&nbsp;
            Quick Recovery &nbsp;&nbsp;|&nbsp;&nbsp;
            Wishing all our patients a Quick Recovery! &nbsp;&nbsp;&nbsp;
        </marquee>
    </div>

    <div class="footer-grid">
        <div class="footer-col">
            <h3>Contact Us</h3>
            <p>Phone: +254 700 123 456</p>
            <p>Phone: +254 722 987 654</p>
            <p>Email: info@afyaborahospital.co.ke</p>
        </div>
        <div class="footer-col">
            <h3>Postal Address</h3>
            <p>Afya Bora Hospital</p>
            <p>P.O. Box 1234 - 00100</p>
            <p>Nairobi, Kenya</p>
        </div>
        <div class="footer-col">
            <h3>Working Hours</h3>
            <p>Monday - Friday: 8:00 AM - 8:00 PM</p>
            <p>Saturday: 8:00 AM - 4:00 PM</p>
            <p>Emergency: 24 Hours / 7 Days</p>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; 2026 Afya Bora Hospital. All rights reserved. | Quality Healthcare for Every Community
    </div>
</footer>

<?php
if ($db_ok) {
    $conn->close();
}
?>
</body>
</html>
