<?php

require_once "api/db.php";
require_once "api/pizza.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["id"]) ?? 0;

    pizza_change_status($id);
}

$pizzas = pizza_all();

foreach ($pizzas as $pizza) {
    $deleted = boolval(intval($pizza["deleted"]));
    $label = $deleted ? "Przywroc" : "Usun";

    ?>

    <div class="card">
        <div class="card-header">
            <div class="card-header-title"><?= $pizza["name"] ?></div>
        </div>

        <div class="card-content">
            <form action="?page=pizza" method="post">
                <input type="hidden" name="id" id="id" value="<?= $pizza["id"] ?>">
                <input class="button is-primary" type="submit" value="<?= $label ?>">
            </form>
        </div>
    </div>

    <br>

    <?php
}

?>