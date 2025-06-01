<?php 
      include 'connexionBd.php';

      if (isset($_POST['envoyer'])) {
        $plaque=htmlspecialchars($_POST['plaque']);
        $Description=htmlspecialchars($_POST['Description']);
          if (!empty($plaque) && !empty($Description)) 
          {
             $bdd-> query("INSERT INTO vehicule VALUES (null, '$plaque', '$Description') ");
             $notification = "Réussie";
          }
          else
          {
            $notification = "completer la categorie";
          }
      }


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter catégorie</title>
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
            <h2 class="fw-bold mb-0">Ajouter catégorie</h2>
            <a href="Admin.php" class="btn-close" ></a>
          </div>    
          <div class="modal-body p-5 pt-0">

            <form method="POST">

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="plaque">
                <label for="floatingInput">N° plaque </label>
              </div>

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="Description">
                <label for="floatingInput">Description </label>
              </div>

              <p class="text-success text-center"><?php if(isset($notification))echo $notification; ?></p>


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