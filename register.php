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
        <label class="label" for="username">Username</label>
        <div class="control">
            <input class="input" id="username" name="username" type="text" placeholder="e.g. kowalski123">
        </div>
    </div>

    <div class="field">
        <label class="label" for="password">Password</label>
        <div class="control">
            <input class="input" id="password" name="password" type="password" placeholder="********">
        </div>
    </div>

    <button class="button">Register</button>
</form>

<?php

require_once "api/user.php";
require_once "components/message.php";

function register()
{
    $user = User::register(
        $_POST["email"] ?? "",
        $_POST["username"] ?? "",
        $_POST["password"] ?? ""
    );

    if (!$user->is_valid()) {
        message("Niepoprawne dane!");

        return;
    }

    $user->encrypt();

    try {
        $user->save();

        message("Zarejestrowano!");
    } catch (mysqli_sql_exception $e) {
        message("Uzytkownik z podanym emailem juz istnieje!");
    } catch (Exception $e) {
        message("Nieprzewidziany blad!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    register();
}

include_once "components/footer.php";

?>