<?php

require_once "db.php";

function pizza_show() {
    global $mysqli;

    $pizzas = $mysqli->query("SELECT id, name, description, price FROM pizza")->fetch_all();

    foreach ($pizzas as $pizza) {
        ?>

        <div class="card">
            <div class="card-header">
                <div class="card-header-title"><?= $pizza[1] ?></div>
            </div>
            <div class="card-content">
                <p><?= $pizza[2] ?></p>
                <br>
                <p>$<?= $pizza[3] ?></p>

                <br>

                <form action="order.php" method="post">
                    <input type="hidden" id="id" name="id" value="<?= $pizza[0] ?>">

                    <button class="button">Zamow</button>
                </form>
            </div>
        </div>

        <br>

        <?php
    }
}

function pizza_exist($id) {
    global $mysqli;

    $pizza = $mysqli->prepare("SELECT id FROM pizza WHERE id=?");
    $pizza->bind_param("i", $id);
    $pizza->execute();
    
    return $pizza->get_result()->num_rows > 0;
}

function pizza_get_price($id) {
    global $mysqli;

    $pizza = $mysqli->prepare("SELECT price FROM pizza WHERE id=?");
    $pizza->bind_param("i", $id);
    $pizza->execute();

    return $pizza->get_result()->fetch_row()[0];
}

function pizza_order($pizza_id, $target_id) {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;
    $target_id ??= $user->id;

    if ($user == null) {
        return;
    }

    if ($target_id == 0) {
        $target_id = $user->id;
    }

    if ($user->id != $target_id && $user->role != "admin") {
        return;
    }

    $price = pizza_get_price($pizza_id);

    $transaction = $mysqli->prepare("INSERT INTO transactions (pizza_id, receiver_id, buyer_id, price) VALUES (?, ?, ?, ?)");
    $transaction->bind_param("iiid", $pizza_id, $target_id, $user->id, $price);
    $transaction->execute();
}

function pizza_receive($transaction_id) {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null || $user->role != "admin") {
        return;
    }

    $query = $mysqli->prepare("UPDATE transactions SET received_time = CURRENT_TIMESTAMP WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
}

function pizza_get_history_price() {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null) {
        return 0;
    }

    $price = $mysqli->prepare("SELECT SUM(price) FROM transactions WHERE receiver_id=?");
    $price->bind_param("i", $user->id);
    $price->execute();

    return number_format(floatval($price->get_result()->fetch_row()[0]), 2);
}

function pizza_show_history() {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null) {
        return;
    }

    $history = $mysqli->prepare("SELECT pizza.name, transactions.price, transactions.bought_time, transactions.received_time FROM transactions JOIN pizza ON pizza.id = transactions.pizza_id WHERE receiver_id = ? ORDER BY transactions.bought_time DESC");
    $history->bind_param("i", $user->id);
    $history->execute();
    $history = $history->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($history as $order) {
        $received = $order["received_time"] != null;

        ?>

        <div class="card">
            <div class="card-header">
                <div class="card-header-title">
                    <?php 

                    echo $order["name"];
                    echo " - ";
                    echo $received ? "Otrzymano - " . $order["received_time"] : "W przygotowaniu";

                    ?>
                </div>
            </div>
            <div class="card-content">
                <p>$<?= $order["price"] ?></p>
                <br>
                <p><?= $order["bought_time"] ?></p>
            </div>
        </div>

        <br>

        <?php
    }
}