<?php
session_start();
require_once "../config/database.php";

error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom          = trim($_POST["nom"]);
    $prenom       = trim($_POST["prenom"]);
    $email        = trim($_POST["email"]);
    $mdp          = trim($_POST["mdp"]);
    $confirmation = trim($_POST["confirmation"]);

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($mdp) && !empty($confirmation)) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif ($mdp !== $confirmation) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
                $stmt->execute(["email" => $email]);

                if ($stmt->fetch()) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    $hash = password_hash($mdp, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("
                        INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe)
                        VALUES (:nom, :prenom, :email, :mdp)
                    ");
                    $stmt->execute([
                        "nom"    => $nom,
                        "prenom" => $prenom,
                        "email"  => $email,
                        "mdp"    => $hash
                    ]);
                    header("Location: connexion.php");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Erreur SQL : " . $e->getMessage();
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-100">
<section class="py-20">
    <div class="container mx-auto">
        <div class="flex flex-col gap-4">
            <div class="rounded-lg border bg-white shadow-sm mx-auto w-full max-w-md">
                <div class="flex flex-col space-y-2 p-6 items-center">
                    <h3 class="font-semibold tracking-tight text-2xl">S'inscrire</h3>
                    <p class="text-sm text-zinc-600">Entrez vos informations pour créer un compte</p>
                </div>
                <div class="p-6 pt-0">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded-md text-sm mb-4"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required
                                    value="<?= htmlspecialchars($nom ?? '') ?>"
                                    class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                            </div>
                            <div class="grid gap-2">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required
                                    value="<?= htmlspecialchars($prenom ?? '') ?>"
                                    class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                            </div>
                            <div class="grid gap-2">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="Entrez votre email" required
                                    value="<?= htmlspecialchars($email ?? '') ?>"
                                    class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                            </div>
                            <div class="grid gap-2">
                                <label for="mdp">Mot de passe</label>
                                <input type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required
                                    class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                            </div>
                            <div class="grid gap-2">
                                <label for="confirmation">Confirmation</label>
                                <input type="password" id="confirmation" name="confirmation" placeholder="Confirmez votre mot de passe" required
                                    class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                            </div>
                            <button type="submit"
                                class="bg-zinc-900 text-white hover:bg-zinc-800 h-10 px-4 py-2 rounded-md transition">
                                Créer un compte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mx-auto flex gap-1 text-sm">
                <p>Déjà un compte ?</p>
                <a href="connexion.php" class="underline text-purple-700">Se connecter</a>
            </div>
        </div>
    </div>
</section>
</body>
</html>