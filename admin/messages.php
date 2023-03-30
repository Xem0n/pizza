<form action="api/messages.php?redirect=admin.php?page=messages" method="post">
    <input class="input" type="text" placeholder="Wpisz swoja wiadomosc..." id="message" name="message">
    <button class="button">
        <span class="icon">
            <i class="fas fa-regular fa-paper-plane"></i>
        </span>
    </button>
</form>

<div class="messages">
    <?php

    $id = $_SESSION["user"]->id;
    $messages = message_get_all($id, 0);

    foreach ($messages as $message) {
        $align = $message["sender"] == $id ? "right" : "";

        ?>

        <div class="message <?= $align ?>">
            <p><?= $message["content"] ?></p>
        </div>

        <?php
    }

    ?>
</div>