<?php

include_once "components/header.php";
require_once "api/pizza.php";

if (!$_SESSION["user"] ?? null) {
    header("Location: login.php");
}

$pizza_id = $_POST["id"] ?? -1;
$pizza_id = intval($pizza_id);

$target_id = intval($_POST["user_id"]) ?? null;

if (pizza_exist($pizza_id)) {
    pizza_order($pizza_id, $target_id);

    ?>

    <h4 class="title is-4">Zamowiles pizze!</h4>
    <a href="history.php">Sprawdz historie swoich zamowien</a>

    <?php
} else {
    ?>
        <h4 class="title is-4">Podana pizza nie istnieje :((</h4>
    <?php
}

include_once "components/footer.php";

?>