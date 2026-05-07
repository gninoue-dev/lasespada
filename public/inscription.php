 <?php
session_start();
require_once "../config/database.php"; // connexion PDO

// Activer l'affichage des erreurs pour le debug
error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $mdp = trim($_POST["mdp"]);
    $confirmation = trim($_POST["confirmation"]);

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($mdp) && !empty($confirmation)) {
        if ($mdp !== $confirmation) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            try {
                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
                $stmt->execute(["email" => $email]);

                if ($stmt->fetch()) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // Hash du mot de passe
                    $hash = password_hash($mdp, PASSWORD_BCRYPT);

                    // Insertion en BDD
                    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) 
                                           VALUES (:nom, :prenom, :email, :mdp)");
                    $stmt->execute([
                        "nom" => $nom,
                        "prenom" => $prenom,
                        "email" => $email,
                        "mdp" => $hash
                    ]);

                    // Redirection vers la connexion
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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

  <section class="py-32">
    <div class="container">
      <div class="flex flex-col gap-4">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-auto w-full max-w-md">
          <div class="flex flex-col space-y-1.5 p-6 items-center">

            <h3 class="font-semibold tracking-tight text-xl">S'inscrire</h3>
            <p class="text-sm text-zinc-600">Entrez vos informations pour créer un compte</p>
          </div>
          <div class="p-6 pt-0">
            <div class="grid gap-4">
              <button
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-white hover:bg-zinc-100 hover:text-zinc-800 h-10 px-4 py-2 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-globe mr-2 size-4">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                  <path d="M2 12h20"></path>
                </svg>S'inscrire avec Google
              </button>
              <div class="flex items-center gap-4">
                <span class="h-px w-full bg-gray-100"></span><span class="text-xs text-zinc-600">OU</span><span
                  class="h-px w-full bg-gray-100"></span>
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="nom">Nom</label><input type="text"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="nom" placeholder="Entrez votre nom" required="" name="nom" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="prenom">Prénom</label><input type="text"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="prenom" placeholder="Entrez votre prénom" required="" name="prenom" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="email">Email</label><input type="email"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="email" placeholder="Entrez votre email" required="" name="email" />
              </div>


              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="mdp">Mot de Passe</label><input type="password"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="mdp" placeholder="Entrez votre mot de passe" required="" name="mdp" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="confirmation">Confirmation</label><input type="password"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="confirmation" placeholder="Confirmez votre mot de passe" required="" name="confirmation" />
              </div>


              <button
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 w-full"
                type="submit">
                creer un compte
              </button>
            </div>

          </div>
        </div>
        <div class="mx-auto flex gap-1 text-sm">
          <p>Déjà un compte?</p>
          <a href="connexion.php" class="underline">Se connecter</a>
        </div>
      </div>
    </div>
  </section>

</body>

</html>

