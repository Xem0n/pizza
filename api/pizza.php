<?php

require_once "db.php";

function pizza_all() {
    global $mysqli;

    $pizzas = $mysqli->query("SELECT * FROM pizza")->fetch_all(MYSQLI_ASSOC);

    return $pizzas;
}

function pizza_show() {
    global $mysqli;

    $pizzas = $mysqli->query("SELECT id, name, description, price, deleted FROM pizza")->fetch_all(MYSQLI_ASSOC);

    foreach ($pizzas as $pizza) {
        if ($pizza["deleted"]) {
            continue;
        }

        ?>

        <div class="card">
            <div class="card-header">
                <div class="card-header-title"><?= $pizza["name"] ?></div>
            </div>
            <div class="card-content">
                <p><?= $pizza["description"] ?></p>
                <br>
                <p>$<?= $pizza["price"] ?></p>

                <br>

                <?php

                $user = $_SESSION["user"] ?? null;

                if (!$user || $user->role == "user") {
                    ?>

                    <form action="order.php" method="post">
                        <input type="hidden" id="id" name="id" value="<?= $pizza["id"] ?>">

                        <button class="button">Zamow</button>
                    </form>

                    <?php
                }

                ?>
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

function pizza_change_status($pizza_id) {
    global $mysqli;

    $query = $mysqli->prepare("UPDATE pizza SET deleted = NOT deleted WHERE id = ?");
    $query->bind_param("i", $pizza_id);
    $query->execute();
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

    if ($user->role != "user") {
        return;
    }

    $pizza = $mysqli->prepare("SELECT price, deleted FROM pizza WHERE id=?");
    $pizza->bind_param("i", $pizza_id);
    $pizza->execute();
    $pizza = $pizza->get_result()->fetch_assoc();

    if ($pizza["deleted"]) {
        return;
    }

    $transaction = $mysqli->prepare("INSERT INTO transactions (pizza_id, receiver_id, buyer_id, price) VALUES (?, ?, ?, ?)");
    $transaction->bind_param("iiid", $pizza_id, $target_id, $user->id, $pizza["price"]);
    $transaction->execute();
}

function pizza_receive($transaction_id) {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null || $user->role != "deliverer") {
        return;
    }

    $query = $mysqli->prepare("SELECT delivery_time FROM transactions WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
    $is_delivered = $query->get_result()->fetch_row()[0] != NULL;

    if (!$is_delivered) {
        return;
    }

    $query = $mysqli->prepare("UPDATE transactions SET received_time = CURRENT_TIMESTAMP WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
}

function pizza_deliver($transaction_id) {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null || $user->role != "deliverer") {
        return;
    }

    $query = $mysqli->prepare("SELECT prepared_time FROM transactions WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
    $is_prepared = $query->get_result()->fetch_row()[0] != NULL;

    if (!$is_prepared) {
        return;
    }

    $query = $mysqli->prepare("UPDATE transactions SET delivery_time = CURRENT_TIMESTAMP WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
}

function pizza_prepared($transaction_id) {
    global $mysqli;

    $user = $_SESSION["user"] ?? null;

    if ($user == null || $user->role != "admin") {
        return;
    }

    $query = $mysqli->prepare("UPDATE transactions SET prepared_time = CURRENT_TIMESTAMP WHERE id = ?");
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

    $history = $mysqli->prepare("SELECT pizza.name, transactions.price, transactions.bought_time, transactions.received_time, transactions.delivery_time, transactions.prepared_time FROM transactions JOIN pizza ON pizza.id = transactions.pizza_id WHERE receiver_id = ? ORDER BY transactions.bought_time DESC");
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
                    echo $received ? "Zakonczone" : "W trakcie";

                    ?>
                </div>
            </div>
            <div class="card-content">
                <p>$<?= $order["price"] ?></p>
                <br>
                <p>Zakupiono - <?= get_time_string($order["bought_time"]) ?></p>
                <p>Przygotowano - <?= get_time_string($order["prepared_time"]) ?></p>
                <p>Wyslano - <?= get_time_string($order["delivery_time"]) ?></p>
                <p>Odebrano - <?= get_time_string($order["received_time"]) ?></p>
            </div>
        </div>

        <br>

        <?php
    }
}

function get_time_string($time) {
    return $time != null ? $time : "W trakcie";
}