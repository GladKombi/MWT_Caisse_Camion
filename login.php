<?php 
include 'connexionBd.php';
session_start();
  $fonction=$_GET['fonction'];  //la methode quirecuper les donnees dans url

 if (isset($_POST['valider'])) //creation d'un evenement sur un button valider
 {
   $nom=htmlspecialchars($_POST['nom']);//recuperer les carrecteres dans une zonne de saisie
   $motdepasse=htmlspecialchars($_POST['password']);

  if ($fonction=="admin")   //Comparer la valeur qui etait recuperée ds l'url avec le string admin
  {

    if (!empty($nom) && !empty($motdepasse))//champs obligatoire
     {
        $recuperUser= $bdd->prepare("SELECT * FROM admin WHERE nom='$nom'  and pwd='$motdepasse'");//recuper tous dans la table admin et comparer avec les zonnes de saisie
        $recuperUser->execute();//execute la reqquette
        $recup = $recuperUser->fetch();//stocke dans le tablo
        if($recup)
        {
            $_SESSION['id']=$recup['id'];//mettre la donnée dans une session
            
            header("Location: Admin.php");//redirection de l'utilasateur
        }
        else
        {
            $erreur= 'Mot de passe incorrect ';
        }
    }
    else
    {
        $erreur= 'Completer tous les champs ';
    }  
  }




  if ($fonction=="Employeur") 
  {
    echo "connecter Employeur";
  }


  if ($fonction=="Employe") 
  {
    if (!empty($nom) && !empty($motdepasse))//champs obligatoire
     {
        $recuperUser= $bdd->prepare("SELECT * FROM travailleur WHERE nom='$nom'  and matricule='$motdepasse'");//recuper tous dans la table admin et comparer avec les zonnes de saisie
        $recuperUser->execute();//execute la reqquette
        $recup = $recuperUser->fetch();//stocke dans le tablo
        if($recup)
        {
            $_SESSION['identifant']=$recup['id'];//mettre la donnée dans une session
            
            header("Location: travailleur.php");//redirection de l'utilasateur
        }
        else
        {
            $erreur= 'Mot de passe incorrect ';
        }
    }
    else
    {
        $erreur= 'Completer tous les champs ';
    }  
  }
 }

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>se connecter</title>
    <link rel="stylesheet" href="assets/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <div class="container">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-5 shadow">
          <div class="modal-header p-5 pb-4 border-bottom-0">
            <!-- <h5 class="modal-title">Modal title</h5> -->
            <h2 class="fw-bold mb-0">Connectez-vous</h2>
            <a href="Index.php" class="btn-close" ></a>
          </div>    
          <div class="modal-body p-5 pt-0">
            <form method="POST">
              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="nom">
                <label for="floatingInput">Entrez votre Nom </label>
              </div>
             

              <div class="form-floating mb-2">
                <input type="password" class="form-control rounded-4" id="floatingPassword" name="password">
                <label for="floatingPassword">Entrer un mot de passe</label>
              </div>

              <p class="text-danger text-center"><?php if(isset( $erreur)) echo $erreur; ?></p>
              <button class="w-100 mb-2 btn btn-lg rounded-4 btn-primary" type="submit" name="valider">Connexion</button>
              <small class="text-muted">En cliquant ici vous acceptez notre contrat d'utilisation</small>
              
            </form>
          </div>
        </div>
      </div>
   </div>



   <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>