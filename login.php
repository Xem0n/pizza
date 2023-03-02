<?php

include_once "components/header.php";

if ($_SESSION["user"] ?? null) {
    header("Location: index.php");
}

?>

<form action="?" method="post" class="box">
    <div class="field">
        <label class="label" for="email">Email</label>
        <div class="control">
            <input class="input" id="email" name="email" type="email" placeholder="e.g. alex@example.com">
        </div>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
            <input class="input" id="password" name="password" type="password" placeholder="********">
        </div>
    </div>

    <button class="button">Login</button>
</form>

<?php

require_once "api/user.php";
require_once "components/message.php";

function login()
{
    $user = User::login(
        $_POST["email"] ?? "",
        $_POST["password"] ?? ""
    );

    if (!$user) {
        message("Invalid email!");

        return;
    }

    if (!$user->check_password()) {
        message("Invalid password");

        return;
    }

    $user->make_secure();

    // message("Zalogowano!");

    $_SESSION["user"] = $user;

    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    login();
}

include_once "components/footer.php";

?>