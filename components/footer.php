        <?php

        if (isset($_SESSION["user"]) && $_SESSION["user"]->role == "user") {
            include "chat.php";

            ?>

            <script src="static/chat.js" defer></script>

            <?php
        }

        ?>

        </div>
    </section>
</body>

</html>