<?php

require_once __DIR__ . "/../api/user.php";
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $redirect = $_GET["redirect"] ?? "index.php";
    $receiver_id = intval($_POST["receiver"]) ?? NULL;
    $message = $_POST["message"] ?? "";

    if ($message == "" || strlen($message) > 1024 || !isset($_SESSION["user"])) {
        header("Location: ../" . $redirect);
        return;
    }

    message_send($receiver_id, $message);

    header("Location: ../" . $redirect);
}

function message_send($receiver_id, $message) {
    global $mysqli;

    $sender_id = $_SESSION["user"]->id;

    $query = $mysqli->prepare("INSERT INTO messages (sender, receiver, content, sent_time) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    $query->bind_param("iis", $sender_id, $receiver_id, $message);
    $query->execute();
}

function message_get_all($sender_id, $receiver_id) {
    global $mysqli;

    $query = $mysqli->prepare("SELECT sender, receiver, content, sent_time FROM messages WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY sent_time ASC");
    $query->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    $query->execute();

    $messages = $query->get_result()->fetch_all(MYSQLI_ASSOC);

    return $messages;
}