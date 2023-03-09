<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbar">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbar" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="index.php">
                Pizzeria
            </a>

            <?php

            $user = $_SESSION["user"] ?? null;

            if ($user && $user->role == "user") {
                ?>

                <a href="history.php" class="navbar-item">
                    Zamowienia
                </a>

                <?php
            }

            if ($user && ($user->role == "admin" || $user->role == "deliverer")) {
                ?>

                <a href="confirm.php" class="navbar-item">
                    Potwierdz zamowenia
                </a>

                <?php
            }

            ?>

        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <?php
                    if ($_SESSION["user"] ?? null) {
                        ?>

                        <a class="button is-light" href="edit.php">
                            Edytuj
                        </a>
                        <a class="button is-light" href="logout.php">
                            <strong>Wyloguj sie</strong>
                        </a>

                        <?php
                    } else {
                        ?>

                        <a class="button is-light" href="login.php">
                            Zaloguj sie
                        </a>
                        <a class="button is-light" href="register.php">
                            <strong>Zarejestruj sie</strong>
                        </a>

                        <?php
                    }
                    ?>
                    
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="/static/navbar.js" defer></script>