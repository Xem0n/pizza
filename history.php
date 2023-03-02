<?php

include_once "components/header.php";
require_once "api/pizza.php";

if (!$_SESSION["user"] ?? null) {
    header("Location: login.php");
}

pizza_show_history();

?>

<div class="box">
    <?= "Suma wynosi $" . pizza_get_history_price() ?>
</div>

<?php

include_once "components/footer.php";

?>