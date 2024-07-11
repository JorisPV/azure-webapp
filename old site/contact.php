<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Send email using PHPMailer or another library
    // ...

    echo "Message envoyé avec succès!";
} else {
    echo "Erreur lors de l'envoi du message.";
}
?>