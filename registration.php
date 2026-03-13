<?php
/*
 * FILE:    registration.php
 * SAVE TO: C:\xampp\htdocs\hospital\registration.php
 * TASK 2:  Causes Of Malaria Registration Form
 */

// ── 1. DATABASE CONNECTION ────────────────────────────────────────────────
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "malaria_research";

$conn  = new mysqli($servername, $username, $password, $dbname);
$db_ok = ($conn->connect_error === null);

// ── 2. VARIABLES ──────────────────────────────────────────────────────────
$errors      = array();
$success_msg = "";

// keep form values after error (sticky form)
$v_firstname  = "";
$v_secondname = "";
$v_phone      = "";
$v_gender     = "";
$v_age        = "";

// ── 3. HANDLE FORM SUBMISSION ─────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // collect
    $v_firstname  = trim($_POST["firstname"]        ?? "");
    $v_secondname = trim($_POST["secondname"]       ?? "");
    $v_phone      = trim($_POST["phone"]            ?? "");
    $v_gender     = trim($_POST["gender"]           ?? "");
    $v_age        = trim($_POST["age"]              ?? "");
    $password     = $_POST["password"]              ?? "";
    $confirm_pw   = $_POST["confirm_password"]      ?? "";

    // ── VALIDATION ────────────────────────────────────────
    // First Name
    if ($v_firstname === "") {
        $errors[] = "First Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $v_firstname)) {
        $errors[] = "First Name must contain letters only.";
    }

    // Second Name
    if ($v_secondname === "") {
        $errors[] = "Second Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $v_secondname)) {
        $errors[] = "Second Name must contain letters only.";
    }

    // Phone
    if ($v_phone === "") {
        $errors[] = "Phone Number is required.";
    } elseif (!preg_match("/^[0-9+\- ]{7,15}$/", $v_phone)) {
        $errors[] = "Phone Number is not valid (e.g. 0712345678).";
    }

    // Gender
    if ($v_gender === "") {
        $errors[] = "Please select a gender.";
    } elseif (!in_array($v_gender, array("Male", "Female", "Other"))) {
        $errors[] = "Invalid gender value.";
    }

    // Age
    if ($v_age === "") {
        $errors[] = "Age is required.";
    } elseif (!ctype_digit($v_age) || (int)$v_age < 1 || (int)$v_age > 120) {
        $errors[] = "Age must be a number between 1 and 120.";
    }

    // Password
    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_pw) {
        $errors[] = "Passwords do not match. Please re-enter.";
    }

    // ── SAVE TO DATABASE ──────────────────────────────────
    if (count($errors) === 0) {
        if (!$db_ok) {
            $errors[] = "Database not connected. Please run create_db.sql first.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $age_int = (int)$v_age;

            $stmt = $conn->prepare(
                "INSERT INTO registrations (firstname, secondname, phone, gender, age, password)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssis", $v_firstname, $v_secondname, $v_phone, $v_gender, $age_int, $hashed);

            if ($stmt->execute()) {
                $success_msg = "Registration successful! Thank you, " . htmlspecialchars($v_firstname) . ".";
                // clear form
                $v_firstname = $v_secondname = $v_phone = $v_gender = $v_age = "";
            } else {
                $errors[] = "Could not save record. Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Causes Of Malaria Registration Form</title>
    <style>

        /* ── RESET ────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #d6eaf8;
            min-height: 100vh;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        /* ── FORM CARD ────────────────────────────────────── */
        .form-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.13);
            overflow: hidden;
            margin-top: 10px;
        }

        /* header bar */
        .fc-header {
            background-color: #1a5276;
            color: #ffffff;
            padding: 20px 26px;
            text-align: center;
        }
        .fc-header .icon {
            font-size: 34px;
            margin-bottom: 6px;
        }
        .fc-header h1 {
            font-size: 19px;
            line-height: 1.4;
        }
        .fc-header p {
            font-size: 12px;
            color: #aed6f1;
            margin-top: 5px;
        }

        /* body */
        .fc-body {
            padding: 22px 26px;
        }

        /* alerts */
        .alert {
            padding: 11px 14px;
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
        .alert-error ul {
            margin-left: 18px;
            margin-top: 6px;
        }
        .alert-error ul li {
            margin-bottom: 3px;
        }

        /* form groups */
        .fg {
            margin-bottom: 15px;
        }
        .fg label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #333333;
            margin-bottom: 5px;
        }
        .fg label .req {
            color: #e74c3c;
        }
        .fg input[type="text"],
        .fg input[type="tel"],
        .fg input[type="number"],
        .fg input[type="password"] {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid #bbbbbb;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .fg input:focus {
            outline: none;
            border-color: #2e86c1;
            box-shadow: 0 0 0 2px rgba(46,134,193,0.18);
        }
        .hint {
            font-size: 11px;
            color: #888888;
            margin-top: 3px;
        }

        /* radio group for gender */
        .radio-row {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            margin-top: 4px;
        }
        .radio-row label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: normal;
            font-size: 14px;
            color: #444444;
            cursor: pointer;
        }
        .radio-row input[type="radio"] {
            width: 15px;
            height: 15px;
            accent-color: #1a5276;
            cursor: pointer;
        }

        /* divider line */
        .divider {
            height: 1px;
            background-color: #dde9f5;
            margin: 18px 0;
        }

        /* buttons */
        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }
        .btn-submit {
            flex: 1;
            padding: 11px;
            background-color: #1a5276;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #154360;
        }
        .btn-reset {
            flex: 1;
            padding: 11px;
            background-color: #95a5a6;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-reset:hover {
            background-color: #717d7e;
        }

        /* back link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 13px;
            color: #1a5276;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        /* footer bar */
        .fc-footer {
            background-color: #154360;
            color: #aed6f1;
            text-align: center;
            padding: 11px;
            font-size: 12px;
        }

    </style>
</head>
<body>

<div class="form-card">

    <!-- Card Header -->
    <div class="fc-header">
        <div class="icon">&#129714;</div>
        <h1>Causes Of Malaria<br>Registration Form</h1>
        <p>Afya Bora Hospital &mdash; Community Research Programme</p>
    </div>

    <div class="fc-body">

        <?php if ($success_msg !== ""): ?>
            <div class="alert alert-success">
                &#10003; <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (count($errors) > 0): ?>
            <div class="alert alert-error">
                <strong>&#9888; Please correct the following:</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="registration.php">

            <!-- First Name -->
            <div class="fg">
                <label>First Name <span class="req">*</span></label>
                <input type="text" name="firstname" maxlength="100"
                       placeholder="Enter your first name"
                       value="<?php echo htmlspecialchars($v_firstname); ?>"
                       required>
            </div>

            <!-- Second Name -->
            <div class="fg">
                <label>Second Name <span class="req">*</span></label>
                <input type="text" name="secondname" maxlength="100"
                       placeholder="Enter your second name"
                       value="<?php echo htmlspecialchars($v_secondname); ?>"
                       required>
            </div>

            <!-- Phone Number -->
            <div class="fg">
                <label>Phone Number <span class="req">*</span></label>
                <input type="tel" name="phone" maxlength="15"
                       placeholder="e.g. 0712345678"
                       value="<?php echo htmlspecialchars($v_phone); ?>"
                       required>
            </div>

            <!-- Gender - radio buttons as required by question -->
            <div class="fg">
                <label>Gender <span class="req">*</span></label>
                <div class="radio-row">
                    <label>
                        <input type="radio" name="gender" value="Male"
                            <?php echo ($v_gender === "Male")   ? "checked" : ""; ?>>
                        Male
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Female"
                            <?php echo ($v_gender === "Female") ? "checked" : ""; ?>>
                        Female
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Other"
                            <?php echo ($v_gender === "Other")  ? "checked" : ""; ?>>
                        Other
                    </label>
                </div>
            </div>

            <!-- Age -->
            <div class="fg">
                <label>Age <span class="req">*</span></label>
                <input type="number" name="age" min="1" max="120"
                       placeholder="Enter your age"
                       value="<?php echo htmlspecialchars($v_age); ?>"
                       required>
            </div>

            <div class="divider"></div>

            <!-- Password -->
            <div class="fg">
                <label>Password <span class="req">*</span></label>
                <input type="password" name="password" maxlength="100"
                       placeholder="Create a password"
                       required>
                <p class="hint">Minimum 6 characters.</p>
            </div>

            <!-- Confirm Password -->
            <div class="fg">
                <label>Confirm Password <span class="req">*</span></label>
                <input type="password" name="confirm_password" maxlength="100"
                       placeholder="Re-enter your password"
                       required>
            </div>

            <!-- Submit and Reset buttons -->
            <div class="btn-row">
                <button type="submit"  class="btn-submit">Submit</button>
                <button type="reset"   class="btn-reset">Reset</button>
            </div>

        </form>

        <a class="back-link" href="hospital.php">&larr; Back to Hospital Home</a>

    </div><!-- /fc-body -->

    <div class="fc-footer">
        &copy; 2026 Afya Bora Hospital &mdash; Malaria Research Programme
    </div>

</div><!-- /form-card -->

<?php
if ($db_ok) {
    $conn->close();
}
?>
</body>
</html>
