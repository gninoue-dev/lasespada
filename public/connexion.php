<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {

        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, email, mot_de_passe
            FROM utilisateurs
            WHERE email = :email
        ");

        $stmt->execute([
            "email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["mot_de_passe"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_nom"] = $user["nom"];
            $_SESSION["user_prenom"] = $user["prenom"];
            $_SESSION["user_email"] = $user["email"];

            header("Location: ../public/historique.php");
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

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<style>

    body{
        background: #f4f4f5;
    }

    a{
        color: purple;
        text-decoration: underline;
    }

    label{
        color: #52525b;
        font-size: 14px;
    }

</style>

<body>

<section class="py-32">

    <div class="container mx-auto">

        <div class="rounded-lg border bg-white shadow-sm mx-auto w-full max-w-md">

            <div class="flex flex-col space-y-4 p-6">

                <div class="text-center">

                    <h3 class="font-semibold tracking-tight text-2xl">
                        Connexion
                    </h3>

                    <p class="text-sm text-zinc-600 mt-2">
                        Entrez vos informations pour vous connecter
                    </p>

                </div>

                <?php if(isset($error)) : ?>

                    <div class="bg-red-100 text-red-700 p-3 rounded-md text-sm">
                        <?= $error; ?>
                    </div>

                <?php endif; ?>

                <form method="POST" action="" class="space-y-4">

                    <div class="grid gap-2">

                        <label for="email">Email</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nomprenom@example.com"
                            required
                            class="flex h-10 w-full rounded-md border px-3 py-2 text-sm"
                        >

                    </div>

                    <div class="grid gap-2">

                        <div class="flex justify-between">

                            <label for="password">
                                Mot de passe
                            </label>

                            <a href="#">
                                Mot de passe oublié ?
                            </a>

                        </div>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            class="flex h-10 w-full rounded-md border px-3 py-2 text-sm"
                        >

                    </div>

                    <button
                        type="submit"
                        class="bg-zinc-900 text-white h-10 px-4 py-2 w-full rounded-md hover:bg-zinc-800 transition"
                    >
                        Se connecter
                    </button>

                </form>

            </div>

        </div>

        <div class="mx-auto flex justify-center gap-1 text-sm mt-4">

            <p>Vous n'avez pas de compte ?</p>

            <a href="inscription.php">
                S'inscrire
            </a>

        </div>

    </div>

</section>

</body>
</html>