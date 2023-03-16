<?php

require_once "api/db.php";
require_once "api/pizza.php";
require_once "components/message.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try_add_pizza();
}

$message;

function try_add_pizza() {
    global $message;

    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $price = floatval($_POST["price"]) ?? 0;

    if (trim($name) == "") {
        $message = "Niepoprawna nazwa";

        return;
    }

    if (trim($description) == "") {
        $message = "Niepoprawny opis";

        return;
    }

    if ($price == 0) {
        $message = "Niepoprawna cena";

        return;
    }

    pizza_add($name, $description, $price);

    $message = "Pizza zostala dodana";
}

?>

<form action="?page=add_pizza" method="post">
    <div class="field">
        <label class="label" for="name">Nazwa</label>
        <div class="control">
            <input class="input" id="name" name="name" type="text" required>
        </div>
    </div>

    <div class="field">
        <label class="label" for="description">Opis</label>
        <div class="control">
            <textarea class="textarea" name="description" id="description" cols="30" rows="5" required></textarea>
        </div>
    </div>

    <div class="field">
        <label class="label" for="price">Cena</label>
        <div class="control">
            <input class="input" id="price" name="price" type="number" step="0.01" required>
        </div>
    </div>

    <br>
    <input class="button is-primary" type="submit" value="Dodaj">
</form>

<br>

<?php

if ($message) {
    message($message);
}

?>