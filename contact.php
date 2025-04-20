<?php
    ob_start();
    $pageTitle = 'Contact - SkyTrack';
    include "include/header.inc.php"; 
?>

<main>
    <section class="contact">
        <h2>Contactez-nous</h2>
        <p>Si vous avez des questions, des suggestions ou des commentaires, n'hésitez pas à nous contacter en remplissant le formulaire ci-dessous.</p>

        <form method="POST" action="contact.php">
            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" name="name" id="name"  placeholder="Votre nom" />
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email"  placeholder="Votre email" />
            </div>
            <div class="form-group">
                <label for="sujet">Sujet :</label>
                <input type="sujet" id="sujet"  placeholder="Objet de votre message" />
            </div>
            <div class="form-group">
                <label for="message">Message :</label>
                <textarea name="message" id="message"  placeholder="Votre message" rows="5"></textarea>
            </div>
            <button type="submit" class="btn">Envoyer</button>
        </form>

        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);

                // Envoi de l'email
                $to = "contactskytrack@icloud.com"; 
                $subject = "Nouveau message de contact de $name";
                $body = "Nom : $name\nEmail : $email\nMessage :\n$message";
                $headers = "From: $email";

                if (mail($to, $subject, $body, $headers)) {
                    echo '<p class="success">Votre message a été envoyé avec succès !</p>';
                } else {
                    echo '<p class="error">Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.</p>';
                }
            }
        ?>
    </section>
</main>

<?php
    include "include/footer.inc.php";
    ob_end_flush(); 
?>
