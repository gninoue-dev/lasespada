<?php
session_start();
require_once "../config/database.php"; // fichier qui contient la connexion PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        // Préparer la requête
        $stmt = $pdo->prepare("SELECT id, nom, prenom, email, mot_de_passe FROM utilisateurs WHERE email = :email");
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["mot_de_passe"])) {
            // Authentification réussie
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_nom"] = $user["nom"];
            $_SESSION["user_prenom"] = $user["prenom"];
            $_SESSION["user_email"] = $user["email"];

            // Redirection vers le tableau de bord
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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/"

</head>

<style>
  a {

    color: purple;
    text-decoration: underline;


  }

  label {
    color: #c7c7c7;


  }
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
            <!-- FORMULAIRE -->
            <form action="connexion.php" method="POST" class="grid gap-4">
              <!-- Bouton Google -->
              <button
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium border bg-white hover:bg-zinc-100 hover:text-zinc-800 h-10 px-4 py-2 w-full">
                <svg class="h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                  <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0..."></path>
                  <!-- icône Google simplifiée -->
                </svg>Connexion avec Google
              </button>

              <!-- Séparateur -->
              <div class="flex items-center gap-4">
                <span class="h-px w-full bg-gray-100"></span>
                <span class="text-xs text-zinc-600">OU</span>
                <span class="h-px w-full bg-gray-100"></span>
              </div>

              <!-- Champ Email -->
              <div class="grid gap-2">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="nomprenom@example.com"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>

              <!-- Champ Mot de Passe -->
              <div class="grid gap-2">
                <div class="flex justify-between">
                  <label for="mdp">Mot de Passe</label>
                  <a href="#" class="text-sm underline" style="color: purple;">Mot de passe oublié?</a>
                </div>
                <input type="password" id="mdp" name="mdp" required placeholder="Entrez votre mot de passe"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" style="color:#ccc;" />
              </div>

              <!-- Bouton Connexion -->
              <button type="submit"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 w-full">
                Se connecter
              </button>
            </form>
            <!-- FIN FORMULAIRE -->
          </div>
        </div>

        <!-- Lien vers inscription -->
        <div class="mx-auto flex gap-1 text-sm">
          <p>Vous n'avez pas de compte?</p>
          <a href="inscription.php" class="underline" style="color: purple;">S'inscrire</a>
        </div>
      </div>
    </div>
  </section>
</body>


</html>