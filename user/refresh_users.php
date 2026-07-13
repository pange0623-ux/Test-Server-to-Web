<?php
require_once '../private/yarsap_14881.php'; // Make sure this has DB credentials & the DE function, etc.

session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    exit("Unauthorized");
}

try {
    $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Use the admin key from session
    if (!isset($_SESSION['admin_key'])) {
        exit("No admin key in session");
    }

    $adminKey = $_SESSION['admin_key'];

    $stmt = $pdo->prepare("SELECT userid, email, Expire FROM users WHERE admin_key = :admkey");
    $stmt->bindParam(':admkey', $adminKey, PDO::PARAM_STR);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return rows only
    foreach ($users as $user) {
        echo '<tr>
        <td>' . $user['userid'] . '</td>
        <td>' . htmlspecialchars(DE($user['email'])) . '</td>
        <td>
            <input
                type="date"
                id="expir-' . htmlspecialchars($user['userid']) . '"
                class="form-control expdateinput"
                onchange="changexpire(this,`' . htmlspecialchars(DE($user['email'])) . '`);"
                value="' . htmlspecialchars($user['Expire']) . '"
                required />
        </td>
        <td class="text-center">
        <button data-tooltip="Change password"
                    onclick="Changepass(`' . htmlspecialchars(DE($user['email'])) . '`)"
                    class="btn btn-danger btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF">
                                                <path d="M106-226v-28h748v28H106Zm14-238-24-14 40-70H56v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Zm320 0-24-14 40-70h-80v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Zm320 0-24-14 40-70h-80v-28h80l-40-68 24-14 40 68 40-68 24 14-40 68h80v28h-80l40 70-24 14-40-70-40 70Z" />
                                            </svg>
            </button>
            <button data-tooltip="Delete client"
                    onclick="RemoveClient(`' . htmlspecialchars(DE($user['email'])) . '`)"
                    class="btn btn-danger btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                     width="24px" fill="#FFFFFF">
                     <path d="m376-336 104-104 104 104 20-20-104-104 104-104-20-20-104 104-104-104-20 20 
                              104 104-104 104 20 20Zm-64 164q-26 0-43-17t-17-43v-488h-40v-28h148v-28h240v28h148v28h-40v488
                              q0 26-17 43t-43 17H312Z"/>
                </svg>
            </button>
            <button data-tooltip="Reset Hardware ID"
                    onclick="Resetid(`' . htmlspecialchars(DE($user['email'])) . '`)"
                    class="btn btn-info btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                     width="24px" fill="#FFFFFF">
                    <path d="M480.17-132Q408-132 345-159q-63-27-110.37-73.79-47.37-46.79-75-110.04Q132-406.08 
                             132-478h28q0 66 25.5 124t69 101.5Q298-209 356.18-184q58.18 25 123.82 25 134 0 227-93t93-227q0-134-93-227
                             t-227-93q-100 0-178.5 53.5T186-606h116v28H132v-170h28v131q40-94 125.5-152.5T480-828q72.21 0 135.72 27.39 63.51 
                             27.39 110.49 74.35 46.98 46.96 74.38 110.43Q828-552.35 828-480.17q0 72.17-27.39 135.73-27.39 63.56-74.35 
                             110.57-46.96 47.02-110.43 74.44Q552.35-132 480.17-132ZM400-332q-14.45 0-24.23-9.77Q366-351.55 
                             366-366v-120q0-14.45 11.5-24.23Q389-520 406-520v-46q0-30.53 21.74-52.26Q449.48-640 480-640t52.26 
                             21.74Q554-596.53 554-566v46q17 0 28.5 9.77Q594-500.45 594-486v120q0 14.45-9.78 24.23Q574.45-332 
                             560-332H400Zm34-188h92v-45.61q0-19.39-13.5-32.89T480-612q-19 0-32.5 13.5T434-565.61V-520Z"/>
                </svg>
            </button>
        </td>
    </tr>';
    }

} catch (PDOException $e) {
    echo "Error retrieving users: " . $e->getMessage();
}
?>
