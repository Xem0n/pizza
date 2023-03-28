<?php

require_once "api/db.php";
require_once "api/pizza.php";
require_once "components/message.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

$availableRoles = [
    "deliverer" => true,
    "user" => true
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    change_user_role();
}

function change_user_role() {
    global $mysqli;
    global $availableRoles;

    $id = intval($_POST["id"]) ?? -1;
    $role = $_POST["role"] ?? null;

    if ($id == -1) {
        return;
    }

    if ($role == null) {
        return;
    }

    if (($availableRoles[$role] ?? false) != true) {
        return;
    }

    $query = $mysqli->prepare("UPDATE user SET role = ? WHERE id = ?");
    $query->bind_param("si", $role, $id);
    $query->execute();
}

$users = $mysqli->query("SELECT id, email, username, role FROM user WHERE role != 'admin'")->fetch_all(MYSQLI_ASSOC);

foreach($users as $user) {
    $delivererDisabled = $user["role"] == "deliverer";
    $userDisabled = $user["role"] == "user";

    ?>

    <div class="card">
        <div class="card-header">
            <div class="card-header-title"><?= $user["username"] ?></div>
        </div>

        <div class="card-content">
            <?= $user["email"] ?>
            <br><br>

            <div class="buttons">
                <form class="form-contents" action="?page=users" method="post">
                    <input type="hidden" name="id" id="id" value="<?= $user["id"] ?>">
                    <input type="hidden" name="role" id="role" value="deliverer">
                    <button class="button is-primary" <?= $delivererDisabled ? "disabled" : "" ?>>Ustaw jako dostawca</button>
                </form>
                <form class="form-contents" action="?page=users" method="post">
                    <input type="hidden" name="id" id="id" value="<?= $user["id"] ?>">
                    <input type="hidden" name="role" id="role" value="user">
                    <button class="button is-primary" <?= $userDisabled ? "disabled" : "" ?>>Ustaw jako uzytkownik</button>
                </form>
            </div>
        </div>
    </div>

    <br>

    <?php
}

?>