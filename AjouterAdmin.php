<?php 

  include 'connexionBd.php';

  if (isset($_POST['envoyer'])) 
  {
    $nom=htmlspecialchars($_POST['txtnom']);
    $postnom=htmlspecialchars($_POST['txtpstnom']);
    $password=htmlspecialchars($_POST['txtmotdepasse']);

    if (!empty($nom) && !empty($postnom) && !empty($password)) //champs obligatoire
    {
      $bdd-> query("INSERT INTO admin(nom, postnom, pwd) VALUES ('$nom', '$postnom', '$password')");//inserer l'element à la BD
       $msg= "L'enregistrement réussi !";
    }
    else
    {
      $msg= "Completer tous les champs";
    }
  }






 ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter admin</title>
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
            <h2 class="fw-bold mb-0">Ajouter admin</h2>
            <a href="Admin.php" class="btn-close" ></a>
          </div>    
          <div class="modal-body p-5 pt-0">

            <form method="POST">
              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="txtnom">
                <label for="floatingInput">Entrez votre Nom </label>
              </div>

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="txtpstnom">
                <label for="floatingInput">Entrez votre pstnom </label>
              </div>

              <div class="form-floating mb-2">
                <input type="password" class="form-control rounded-4" id="floatingPassword" name="txtmotdepasse">
                <label for="floatingPassword">Entrer un mot de passe</label>
              </div>

              <p class="text-success text-center"><?php if(isset($msg)) echo $msg; ?></p>


              <button class="w-100 mb-2 btn btn-lg rounded-4 btn-success" name="envoyer" type="submit">Enregistrer</button>
              <small class="text-muted">En cliquant ici vous acceptez notre contrat d'utilisation</small>
              
            </form>
          </div>
        </div>
      </div>
   </div>



   <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>