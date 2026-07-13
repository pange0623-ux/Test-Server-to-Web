<?php
header('X-Robots-Tag: noindex, nofollow');
require_once '../private/yarsap_14881.php';
// Other includes if necessary...

session_start();

// IP Whitelist
// $whitelist = ['127.0.0.1', '::1'];
$visitoraddress = getClientIP();
// if (!in_array($visitoraddress, $whitelist)) {
//     header("HTTP/1.0 404 Not Found");
//     echo file_get_contents('../404.php');
//     exit();
// }

// Logout Logic
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location:create8361.php');
    exit;
}

// Check Authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Attempt to authenticate if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_key'])) {
        try {
            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Validate admin key from resellers table
            $stmt = $conn->prepare("SELECT sellerid FROM resellers WHERE sellerkey = :admin_key LIMIT 1");
            $stmt->bindParam(':admin_key', $_POST['admin_key'], PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['authenticated'] = true;
                // Save admin_key in session so we can reuse it in refresh_users.php
                $_SESSION['admin_key'] = $_POST['admin_key'];
            } else {
                // Invalid key -> exit or show error
                exit();
            }
        } catch (PDOException $e) {
            exit("Database error.");
        }
    } else {
        // Show login form if no POST data or not authenticated
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login</title>
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #000;
                    color: #fff;
                    margin: 0;
                    font-family: Arial, sans-serif;
                }

                .login-form {
                    text-align: center;
                    padding: 20px;
                    border-radius: 8px;
                }

                .login-form form {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 20px;
                    width: 300px;
                }

                .login-form input[type="password"],
                .login-form button {
                    display: block;
                    width: auto;
                    margin: 10px 0;
                    padding: 10px;
                    border: none;
                    border-radius: 5px;
                }

                .login-form input[type="password"] {
                    background-color: #333;
                    color: #fff;
                    text-align: center;
                }

                .login-form button {
                    background-color: #4CAF50;
                    color: #fff;
                    cursor: pointer;
                    width: 130px;
                }

                .login-form button:hover {
                    background-color: #45a049;
                }
            </style>
        </head>

        <body>
            <div class="login-form">
                <form method="POST">
                    <label>Admin Key</label>
                    <input type="password" name="admin_key" required>
                    <button type="submit">Enter</button>
                </form>
            </div>
        </body>

        </html>
<?php
        exit();
        
    }
}

// If authenticated, proceed to display admin panel
try {
    $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user data
    $stmt = $pdo->prepare("SELECT userid, email, Expire FROM users WHERE admin_key = :admkey");
    // Use the admin_key stored in session
    $stmt->bindParam(':admkey', $_SESSION['admin_key'], PDO::PARAM_STR);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalUsers = count($users);
    $activeUsers = $totalUsers;
    $adminkey = $_SESSION['admin_key'];
    $revenue = "0";
} catch (PDOException $e) {
    exit("Error retrieving users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel</title>

    <!-- Bootstrap 5 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
        rel="stylesheet" />

    <style>
        /* Body / Global Styling */
        body {
            background-color: rgb(26, 26, 26);
            color: #eaeaea;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: rgb(26, 26, 26);
            padding: 10px;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff !important;
        }

        .navbar-brand:hover,
        .navbar-nav .nav-link:hover {
            color: #ccc !important;
        }

        /* Card / Table Styling */
        .card {
            background-color: rgb(15, 15, 15);
            border: none;
            margin: 15px;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
        }

        /* Table header and row styling */
        .table thead th {
            color: #eaeaea;
            border-bottom: 2px solid #eaeaea;
            padding: 10px;
        }

        .table tbody tr td {
            color: #eaeaea;
            vertical-align: middle;
            padding: 10px;
        }

        /* Responsive Table: Make it look like cards on small screens */
        @media (max-width: 768px) {
            .table {
                display: block;
                width: 100%;
                margin: 0;
            }

            .table thead {
                display: none;
                /* Hide table headers */
            }

            .table tbody,
            .table tr {
                display: block;
                width: 100%;
                margin-bottom: 20px;
            }

            .table td {
                display: block;
                padding: 12px;
                text-align: left;
                background-color: rgb(39, 39, 39);


                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .table td::before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                color: #eaeaea;
            }

            .table td:last-child {
                margin-bottom: 0;
            }
        }

        /* Form elements */
        .form-control,
        .form-select {
            background-color: rgb(255, 255, 255);
            border: none;
            color: rgb(0, 0, 0);
            width: 100%;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            outline: 1px solid #00a8ff;
        }

        /* Button styles */
        .btn-primary {
            background-color: #00a8ff;
            border: none;
            border-radius: 0;
            color: white;
        }

        .btn-primary:hover {
            background-color: rgb(39, 39, 39);
        }

        .btn-danger,
        .btn-warning,
        .btn-info {
            background-color: transparent;
            border: none;
            border-bottom: 2px solid gray;
            border-radius: 0;
            color: #fff;
        }

        .btn-danger:hover,
        .btn-warning:hover,
        .btn-info:hover {
            background-color: rgb(39, 39, 39);
        }

        /* Modal */
        .modal-content {
            background-color: rgb(26, 26, 26);
            border: none;
        }

        .modal-header,
        .modal-footer {
            border-color: #444;
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.75);
            color: #fff;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        [data-tooltip]:hover::after {
            opacity: 1;
        }

        /* Responsive layout for larger screens */
        @media (min-width: 768px) {
            .container {
                width: 100%;
                margin: auto;
            }

            .card {
                margin: 20px;
            }
        }

        /* Make inputs, buttons, and modals more responsive */

        .d-flex {
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <h2 class="navbar-brand fw-bold">Admin Panel</h2>
            <div class="d-flex">
                <form method="POST" action="">
                    <button type="submit" name="logout" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                            <path d="M232-172q-26 0-43-17t-17-43v-496q0-26 17-43t43-17h249v28H232q-12 0-22 10t-10 22v496q0 12 10 22t22 10h249v28H232Zm432-184-20-20 90-90H370v-28h364l-90-90 20-20 124 124-124 124Z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container my-4">
        <!-- DASHBOARD SECTION (Stats Cards) -->
        <!-- <div class="row g-3">
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>Total Users</h4>
                    <p class="display-5"><?= $totalUsers ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>Active Users</h4>
                    <p class="display-5"><?= $activeUsers ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center">
                    <h4>Revenue</h4>
                    <p class="display-5">$<?= $revenue ?></p>
                </div>
            </div>
        </div> -->

        <!-- USER MANAGEMENT SECTION -->
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="m-0">User Management</h2>
                <!-- "Add User" button triggers a modal -->
                <button
                    class="btn btn-primary"
                    data-tooltip="Add new client"
                    data-bs-toggle="modal"
                    data-bs-target="#addUserModal">
                    + Add User
                </button>
            </div>

            <!-- Search Bar -->
            <div class="mt-3 mb-3">
                <input
                    type="text"
                    id="searchEmail"
                    class="form-control"
                    placeholder="Search by email..."
                    onkeyup="filterEmails()" />
            </div>

            <!-- User Table -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Email</th>
                            <th>Expire Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <?php if (isset($users) && count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['userid'] ?></td>
                                    <td><?= htmlspecialchars(DE($user['email'])) ?></td>
                                    <td>
                                        <input
                                            type="date"
                                            id="expir-<?= htmlspecialchars($user['userid']) ?>"
                                            class="form-control expdateinput"
                                            onchange="changexpire(this,`<?= htmlspecialchars(DE($user['email'])) ?>`);"
                                            value="<?= htmlspecialchars($user['Expire']) ?>"
                                            required />
                                    </td>
                                    <td class="text-center">
                                        <!-- Example action buttons -->
                                        <button data-tooltip="Change password" onclick="Changepass(`<?= htmlspecialchars(DE($user['email'])) ?>`)" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                                                <path d="M106-226v-28h748v28H106Zm14-238-24-14 40-70H56v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Zm320 0-24-14 40-70h-80v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Zm320 0-24-14 40-70h-80v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Z" />
                                            </svg></button>
                                        <button data-tooltip="Delete client" onclick="RemoveClient(`<?= htmlspecialchars(DE($user['email'])) ?>`)" class="btn btn-danger btn-sm"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                                                <path d="m376-336 104-104 104 104 20-20-104-104 104-104-20-20-104 104-104-104-20 20 104 104-104 104 20 20Zm-64 164q-26 0-43-17t-17-43v-488h-40v-28h148v-28h240v28h148v28h-40v488q0 26-17 43t-43 17H312Z" />
                                            </svg></button>
                                        <button data-tooltip="Reset Hardware ID" onclick="Resetid(`<?= htmlspecialchars(DE($user['email'])) ?>`)" class="btn btn-info btn-sm"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                                                <path d="M480.17-132Q408-132 345-159q-63-27-110.37-73.79-47.37-46.79-75-110.04Q132-406.08 132-478h28q0 66 25.5 124t69 101.5Q298-209 356.18-184q58.18 25 123.82 25 134 0 227-93t93-227q0-134-93-227t-227-93q-100 0-178.5 53.5T186-606h116v28H132v-170h28v131q40-94 125.5-152.5T480-828q72.21 0 135.72 27.39 63.51 27.39 110.49 74.35 46.98 46.96 74.38 110.43Q828-552.35 828-480.17q0 72.17-27.39 135.73-27.39 63.56-74.35 110.57-46.96 47.02-110.43 74.44Q552.35-132 480.17-132ZM400-332q-14.45 0-24.23-9.77Q366-351.55 366-366v-120q0-14.45 11.5-24.23Q389-520 406-520v-46q0-30.53 21.74-52.26Q449.48-640 480-640t52.26 21.74Q554-596.53 554-566v46q17 0 28.5 9.77Q594-500.45 594-486v120q0 14.45-9.78 24.23Q574.45-332 560-332H400Zm34-188h92v-45.61q0-19.39-13.5-32.89T480-612q-19 0-32.5 13.5T434-565.61V-520Z" />
                                            </svg></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">
                                    No users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADD USER MODAL -->
    <div
        class="modal fade"
        id="addUserModal"
        tabindex="-1"
        aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        style="filter: invert(1)"></button>
                </div>
                <div class="modal-body">
                    <form id="createAccountForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input
                                    type="text"
                                    id="username"
                                    class="form-control"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input
                                    type="email"
                                    id="email"
                                    class="form-control"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="adminkey" class="form-label">Admin Key</label>
                                <input
                                    type="text"
                                    id="adminkey"
                                    value="<?= $adminkey ?>"
                                    class="form-control"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="subtype" class="form-label">
                                    Subscription Type
                                </label>
                                <select id="subtype" class="form-select" required>
                                    <option value="1 Month">Essential</option>
                                    <option value="3 Month">Premium</option>
                                    <option value="12 Month">Elite</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="total_paid" class="form-label">Total Paid</label>
                                <input
                                    type="number"
                                    id="total_paid"
                                    step="0.01"
                                    class="form-control"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label for="additional_info" class="form-label">
                                    Additional Info
                                </label>
                                <input
                                    type="text"
                                    id="additional_info"
                                    class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label for="expireDate" class="form-label">Expire Date</label>
                                <input
                                    type="date"
                                    id="expireDate"
                                    class="form-control"
                                    required />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="submitForm()">
                        Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- BOOTSTRAP BUNDLE JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CUSTOM SCRIPTS -->
    <script>
        function refreshUserTable() {
            setTimeout(() => {
                fetch('refresh_users.php') // Update with actual path
                    .then(response => response.text())
                    .then(data => {
                        document.querySelector('#userTable').innerHTML = data;
                    })
                    .catch(error => console.error('Error fetching user data:', error));
            }, 1000);

        }




        // Live Filter by Email
        function filterEmails() {
    const input = document.getElementById("searchEmail").value.toLowerCase();
    const rows = document.querySelectorAll("#userTable tr");

    rows.forEach((row) => {
        // Select the second cell in each row
        const emailCell = row.querySelectorAll("td")[1];
        if (!emailCell) return;
        
        const emailText = emailCell.textContent.toLowerCase();
        // Show or hide the row based on whether it includes the search input
        row.style.display = emailText.includes(input) ? "" : "none";
    });
}


        // Example: Handle "Add User" form submission
        function submitForm() {
            // Collect form data
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const adminkey = document.getElementById('adminkey').value;
            const subtype = document.getElementById('subtype').value;
            const totalPaid = document.getElementById('total_paid').value;
            const additionalInfo = document.getElementById('additional_info').value;
            const expireDate = document.getElementById('expireDate').value;

            if (!username || !email || !password || !adminkey ||
                !subtype || !totalPaid || !expireDate) {
                alert("Please fill out all required fields!");
                return;
            }

            // Prepare data (example JSON body)
            const requestBody = {
                username,
                email,
                password,
                adminkey,
                subtype,
                action: "add",
                total_paid: totalPaid,
                additional_info: additionalInfo,
                expire_date: expireDate
            };

            // Example POST via fetch (adjust for your backend logic)
            fetch('../private/createacc.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestBody),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.Success) {
                        alert(data.Success);
                        // Close modal
                        const modalEl = document.getElementById('addUserModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        // Clear form
                        document.getElementById('createAccountForm').reset();
                        // Optionally refresh table or do something else

                        refreshUserTable();
                    } else if (data.Fail) {
                        alert(data.Fail);
                    } else {
                        alert("Unknown server response.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Error while creating user.");
                });
        }

        function RemoveClient(usremail) {
            // Get form data

            if (confirm(`Are you sure you want to delete ${usremail} \n\n Note: Delete = Set Account expired`)) {
                const adminkey = "<?= $adminkey ?>";

                // Basic validation
                if (!adminkey) {
                    alert("Please enter admin key.");
                    return;
                }

                let requestBody = {
                    adminkey: adminkey,
                    email: usremail,
                    action: 'remove'
                };

                // Send request using fetch
                fetch('../private/createacc.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestBody),
                    })
                    .then(response => response.json())
                    .then(result => {
                        refreshUserTable();
                        if (result.Success) {
                            alert(result.Success);
                        } else if (result.Fail) {
                            alert(result.Fail);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });
            }
        }

        function Resetid(usremail) {
            // Get form data

            if (confirm(`Reset Hardware ID: ${usremail} \n\n Allow client to login from new PC`)) {
                const adminkey = "<?= $adminkey ?>";

                // Basic validation
                if (!adminkey) {
                    alert("Please enter admin key.");
                    return;
                }

                let requestBody = {
                    adminkey: adminkey,
                    email: usremail,
                    action: 'resetid'
                };

                // Send request using fetch
                fetch('../private/createacc.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestBody),
                    })
                    .then(response => response.json())
                    .then(result => {
                        refreshUserTable();
                        if (result.Success) {
                            alert(result.Success);
                        } else if (result.Fail) {
                            alert(result.Fail);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });

            }

        }

        function showOverlayInput(callback) {
            // Create overlay elements dynamically
            const overlay = document.createElement('div');
            const overlayContent = document.createElement('div');
            const input = document.createElement('input');
            const submitBtn = document.createElement('button');
            const closeBtn = document.createElement('button');

            // Set up overlay styles
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            overlay.style.display = 'flex';
            overlay.style.justifyContent = 'center';
            overlay.style.alignItems = 'center';

            // Set up content styles
            overlayContent.style.backgroundColor = '#313131 ';
            overlayContent.style.padding = '20px';
            overlayContent.style.borderRadius = '8px';
            overlayContent.style.textAlign = 'center';
            overlayContent.style.width = '300px';

            // Set up input field
            input.type = 'text';
            input.placeholder = 'New Password';
            input.style.width = '100%';
            input.style.padding = '8px';
            input.style.marginBottom = '10px';
            input.style.fontSize = '14px';

            // Set up submit button
            submitBtn.textContent = 'Save';
            submitBtn.style.padding = '10px 20px';
            submitBtn.style.marginRight = '10px';
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.backgroundColor = '#4CAF50';
            submitBtn.style.color = 'white';
            submitBtn.style.border = 'none';
            submitBtn.style.borderRadius = '4px';

            // Set up close button
            closeBtn.textContent = 'Close';
            closeBtn.style.padding = '10px 20px';
            closeBtn.style.cursor = 'pointer';
            closeBtn.style.backgroundColor = '#f44336';
            closeBtn.style.color = 'white';
            closeBtn.style.border = 'none';
            closeBtn.style.borderRadius = '4px';

            // Append elements to the overlay
            overlayContent.appendChild(input);
            overlayContent.appendChild(submitBtn);
            overlayContent.appendChild(closeBtn);
            overlay.appendChild(overlayContent);
            document.body.appendChild(overlay);

            // Close the overlay
            closeBtn.addEventListener('click', () => {
                document.body.removeChild(overlay); // Remove overlay from the DOM
            });

            // Submit the input
            submitBtn.addEventListener('click', () => {
                const inputValue = input.value.trim();
                if (inputValue) {
                    callback(inputValue); // Return the input value to the callback function
                } else {
                    alert('Please enter something.');
                }
                document.body.removeChild(overlay); // Remove overlay after submission
            });
        }

        function Changepass(usremail) {
            // Get form data
            showOverlayInput((userInput) => {


                const adminkey = "<?= $adminkey ?>";

                // Basic validation
                if (!adminkey) {
                    alert("Please enter admin key.");
                    return;
                }

                let requestBody = {
                    adminkey: adminkey,
                    email: usremail,
                    password: userInput,
                    action: 'update'
                };

                // Send request using fetch
                fetch('../private/createacc.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestBody),
                    })
                    .then(response => response.json())
                    .then(result => {
                        refreshUserTable();
                        if (result.Success) {
                            alert(result.Success);
                        } else if (result.Fail) {
                            alert(result.Fail);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });

            });


        }

        function changexpire(object, usremail) {

            console.log(object.value, usremail);

            let expireDate = object.value;
            const adminkey = "<?= $adminkey ?>";

            if (confirm(`${usremail} Expire Date: ${expireDate}`)) {
                // Basic validation
                if (!adminkey) {
                    alert("Please enter admin key.");
                    return;
                }

                let requestBody = {
                    adminkey: adminkey,
                    email: usremail,
                    expire_date: expireDate,
                    action: 'cexpire'
                };

                // Send request using fetch
                fetch('../private/createacc.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestBody),
                    })
                    .then(response => response.json())
                    .then(result => {
                        refreshUserTable();
                        if (result.Success) {
                            alert(result.Success);
                        } else if (result.Fail) {
                            alert(result.Fail);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });

            }


        }
    </script>

</body>

</html>

<?php
// Close DB connection  
$pdo = null;
?>