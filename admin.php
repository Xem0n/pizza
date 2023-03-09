<?php

include_once "components/header.php";

require_once "api/db.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

$user = $_SESSION["user"];

?>

lolxd

<?php

include_once "components/footer.php";

?>