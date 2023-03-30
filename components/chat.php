<?php

require_once __DIR__ . "/../api/messages.php";

?>

<div class="chat-open">
    <span class="icon">
        <i class="fas fa-regular fa-comment"></i>
    </span>
</div>

<div class="chat chat-closed message">
    <div class="message-header">
        <p>Support</p>
        <button class="delete chat-exit" aria-label="delete"></button>
    </div>
    <div class="message-body">
        <form action="api/messages.php?redirect=index.php" method="post">
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
    </div>
</div>