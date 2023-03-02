<?php

include_once "components/header.php";
require_once "components/message.php";

if (!$_SESSION["user"] ?? null) {
    header("Location: login.php");
}

$fields = [
    "email" => "email",
    "username" => "text",
    "password" => "password",
    "confirm password" => "password"
];

$user = $_SESSION["user"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["verify_password"] ?? "";

    if (trim($password) != "") {
        $password = User::encryptPass($password);
    }

    if ($user->hash == $password) {
        update();
    } else {
        $message .= "Niepoprawne haslo!<br>";
    }
}

function update() {
    global $user;
    global $message;

    $any_change = false;

    foreach ($_POST as $key => $value) {
        if ($key == "confirm_password") {
            continue;
        }

        if (trim($value) != "" && $value != $user->{$key}) {
            if ($key == "password" && $value != $_POST["confirm_password"]) {
                $message .= "Passwords don't match!<br>";

                continue;
            }

            $user->{$key} = $value;
            
            if ($key == "password") {
                $user->encrypt();
            }

            $any_change = true;
        }
    }

    if (!$any_change) {
        $message .= "There's no changes to make!<br>";

        return;
    }

    try {
        $user->update();

        $message .= "Changes applied!<br>";
    } catch (Exception $e) {
        var_dump($e);
        $message .= "Couldn't make the changes!<br>";
    }
}

?>

<div class="box">
    <h1 class="title">Edit</h1>

    <form action="?" method="post" data-target="modal">
        <?php

        foreach ($fields as $name => $type) {
            $name_uppercase = ucfirst($name);

            if ($name == "confirm password") {
                $value = "";
            } else {
                $value = $user->{$name};
            }

            echo "
                <div class=\"field\">
                    <label class=\"label\" for=\"$name\">$name_uppercase</label>
                    <div class=\"control\">
                        <input class=\"input\" type=\"$type\" name=\"$name\" id=\"$name\" placeholder=\"$value\">
                    </div>
                </div>
            ";
        }

        ?>

        <div class="field is-grouped">
            <div class="control">
                <!-- <button class="button is-primary">Submit</button> -->
                <button class="button is-primary js-modal-trigger">Submit</button>
            </div>
        </div>
    </form>
</div>

<?php

if ($message != "") {
    message($message);
}

?>

<div id="modal" class="modal">
    <div class="modal-background"></div>

        <div class="modal-content">
            <div class="box">
                <p>Podaj swoje haslo, aby potwierdzic zmiany</p>

                <div class="field">
                    <input class="input" type="password" name="verify_password">
                </div>

                <button class="button is-primary" id="sendForm">Potwierdz</button>
            </div>
        </div>

    <button class="modal-close is-large" aria-label="close"></button>
</div>

<script src="static/modal.js" defer></script>

<?php

include_once "components/footer.php";

?>