<?php

include_once "components/header.php";

require_once "api/db.php";

if (($_SESSION["user"] ?? null) == NULL || $_SESSION["user"]->role == "user") {
    header("Location: login.php");
}

$user = $_SESSION["user"];

$pages = [
    "users" => [
        "file" => "users.php",
        "name" => "Zarzadzaj uzytkownikami"
    ],
    "pizza" => [
        "file" => "pizza.php",
        "name" => "Zarzadzaj pizzami"
    ],
    "orders" => [
        "file" => "orders.php",
        "name" => "Potwierdz zamowienia"
    ],
    "messages" => [
        "file" => "messages.php",
        "name" => "Wiadomosci"
    ]
];

$active_page = $_GET["page"] ?? "";

?>

<div class="columns is-8">
    <div class="column is-one-fifth box">
        <aside class="menu">
            <p class="menu-label">
                Panel administratora
            </p>

            <ul class="menu-list">
                <?php

                foreach ($pages as $key => $page) {
                    $is_active = $active_page == $key ? "is-active" : "";

                    ?>

                    <li><a class="<?= $is_active ?>" href="admin.php?page=<?= $key ?>"><?= $page["name"] ?></a></li>

                    <?php
                }

                ?>
            </ul>
        </aside>
    </div>

    <div class="column box">
        <?php

        if (isset($pages[$active_page])) {
            include_once "admin/" . $pages[$active_page]["file"];
        } else {
            ?>

            <h5 class="title is-5">Wybierz podstrone</h5>

            <?php
        }

        ?>
    </div>
</div>

<?php

include_once "components/footer.php";

?>