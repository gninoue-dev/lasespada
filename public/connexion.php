<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"]);
    $password = trim($_POST["mdp"]);

    if (!empty($email) && !empty($password)) {

        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, email, mot_de_passe, statut
            FROM utilisateurs 
            WHERE email = :email
        ");
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["mot_de_passe"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"]     = $user["id"];
            $_SESSION["user_nom"]    = $user["nom"];
            $_SESSION["user_prenom"] = $user["prenom"];
            $_SESSION["user_email"]  = $user["email"];
            $_SESSION["role"]        = $user["role"] ?? "user";

            if ($_SESSION["role"] === "admin") {
                header("Location: ../admin/dashbord.php");
            } else {
                header("Location: dashbord.php");
            }
            exit;

        } else {
            $error = "Email ou mot de passe incorrect.";
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
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com" defer></script>
</head>
<style>
    body { background: #f4f4f5; }
    a { color: purple; text-decoration: underline; }
    label { color: #52525b; font-size: 14px; }
</style>
<body>
<section class="py-32">
    <div class="container">
        <div class="flex flex-col gap-4">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-auto w-full max-w-md">
                <div class="flex flex-col space-y-1.5 p-6 items-center">
                    <h3 class="font-semibold tracking-tight text-xl">Connexion avec son email</h3>
                    <p class="text-sm text-zinc-600">Entrez vos informations pour vous connecter</p>
                </div>
                <div class="p-6 pt-0">
                    <?php if (isset($error)): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded-md text-sm mb-4"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST" action="" class="grid gap-4">
                        <div class="grid gap-2">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required placeholder="nomprenom@example.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                        </div>
                        <div class="grid gap-2">
                            <div class="flex justify-between">
                                <label for="mdp">Mot de Passe</label>
                                <a href="#" style="color:purple;">Mot de passe oublié?</a>
                            </div>
                            <input type="password" id="mdp" name="mdp" required placeholder="Entrez votre mot de passe"
                                class="flex h-10 w-full rounded-md border px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                            class="bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 rounded-md w-full text-sm">
                            Se connecter
                        </button>
                    </form>
                </div>
            </div>
            <div class="mx-auto flex gap-1 text-sm">
                <p>vous n'avez pas de compte?</p>
                <a href="inscription.php">S'inscrire</a>
            </div>
        </div>
    </div>
</section>
</body>
</html>