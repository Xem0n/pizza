<?php

include_once "components/header.php";

require_once "api/db.php";
require_once "api/pizza.php";

if (!$_SESSION["user"] ?? null || $_SESSION["user"]->role != "admin") {
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pizza_id = intval($_POST["transaction_id"]) ?? 0;

    if ($pizza_id > 0) {
        pizza_receive($pizza_id);
    }
}

$transactions = $mysqli->prepare("SELECT transactions.id, transactions.bought_time, user.username, pizza.name AS pizza_name FROM transactions JOIN user ON user.id = transactions.receiver_id JOIN pizza ON pizza.id = transactions.pizza_id WHERE transactions.received_time IS NULL ORDER BY transactions.bought_time DESC");
$transactions->execute();
$transactions = $transactions->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($transactions as $transaction) {
    ?>

    <div class="card">
        <div class="card-header">
            <div class="card-header-title"><?= $transaction["pizza_name"] ?></div>
        </div>
        <div class="card-content">
            <p>Klient: <?= $transaction["username"] ?></p>
            <p>Czas zamowienia: <?= $transaction["bought_time"] ?></p>
            <br>

            <form action="?" method="post">
                <input type="hidden" name="transaction_id" id="transaction_id" value="<?= $transaction["id"] ?>">
                <button class="button is-primary">Oznacz jako dostarczone</button>
            </form>
        </div>
    </div>

    <br>

    <?php
}

include_once "components/footer.php";

?>