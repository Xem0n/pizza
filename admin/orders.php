<?php

include_once "components/header.php";

require_once "api/db.php";
require_once "api/pizza.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

$user = $_SESSION["user"];

$PREPARED = 0;
$DELIVERED = 1;
$RECEIVED = 2;

$check = array();
$check[$PREPARED] = function($transaction) {
    global $user;
    return ($transaction["prepared_time"] == NULL && $transaction["delivery_time"] == NULL && $user->role == "admin");
};
$check[$DELIVERED] = function($transaction) {
    global $user;
    return ($transaction["prepared_time"] != NULL && $transaction["delivery_time"] == NULL && $user->role == "deliverer");
};
$check[$RECEIVED] = function($transaction) {
    global $user;
    return ($transaction["prepared_time"] != NULL && $transaction["delivery_time"] != NULL && $user->role == "deliverer");
};

$actions = array();
$actions[$PREPARED] = function($transaction) {
    pizza_prepared($transaction);
};
$actions[$DELIVERED] = function($transaction) {
    pizza_deliver($transaction);

};
$actions[$RECEIVED] = function($transaction) {
    pizza_receive($transaction);
};

function display_form($data, $status, $text) {
    ?>

    <form action="?" method="post">
        <input type="hidden" name="transaction_id" id="transaction_id" value="<?= $data["id"] ?>">
        <input type="hidden" name="status" id="status" value="<?= $status ?>">
        <button class="button is-primary"><?= $text ?></button>
    </form>

    <?php
};

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction = intval($_POST["transaction_id"] ?? 0) ;
    $status = intval($_POST["status"] ?? -1);

    if ($transaction > 0 && $status >= 0) {
        $transaction_data = $mysqli->prepare("SELECT prepared_time, delivery_time FROM transactions WHERE id=?");
        $transaction_data->bind_param("i", $transaction);
        $transaction_data->execute();
        $transaction_data = $transaction_data->get_result()->fetch_assoc();

        if ($check[$status]($transaction_data)) {
            $actions[$status]($transaction);
        }
    }
}

$transactions = $mysqli->prepare("SELECT transactions.id, transactions.bought_time, transactions.prepared_time , transactions.delivery_time , user.username, pizza.name AS pizza_name FROM transactions JOIN user ON user.id = transactions.receiver_id JOIN pizza ON pizza.id = transactions.pizza_id WHERE transactions.received_time IS NULL ORDER BY transactions.bought_time DESC");
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

            <?php

            if ($check[$PREPARED]($transaction)) {
                display_form($transaction, $PREPARED, "Oznacz jako w przygotowaniu");
            } else if ($check[$DELIVERED]($transaction)) {
                display_form($transaction, $DELIVERED, "Oznacz jako w dostawie");
            } else if ($check[$RECEIVED]($transaction)) {
                display_form($transaction, $RECEIVED, "Oznacz jako dostarczone");
            } else {
                ?>

                <p>Nie mozesz nic zrobic w tej chwili :(</p>

                <?php
            }

            ?>
        </div>
    </div>

    <br>

    <?php
}

include_once "components/footer.php";

?>