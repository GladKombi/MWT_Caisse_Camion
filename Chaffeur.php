 <?php 
      include 'connexionBd.php';

      if (isset($_POST['envoyer'])) {
        $nom=htmlspecialchars($_POST['nom']);
        $postnom=htmlspecialchars($_POST['postnom']);
        $phone=htmlspecialchars($_POST['phone']);
        $adresse=htmlspecialchars($_POST['adresse']);
        $Matricule=htmlspecialchars($_POST['Matricule']);
        $codeCat=htmlspecialchars($_POST['codeCat']);
        $codev=htmlspecialchars($_POST['codev']);
          if (!empty($nom) && !empty($postnom)&& !empty($phone)&& !empty($adresse)&& !empty($Matricule)&& !empty($codeCat) &&!empty($codev)) 
          {
             $bdd-> query("INSERT INTO travailleur VALUES (null, '$nom', '$postnom', '$phone', '$adresse', '$Matricule', '$codeCat', '$codev') ");
             $notification = "Réussie";
          }
          else
          {
            $notification = "completer tous les champs";
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
            <h2 class="fw-bold mb-0">Ajouter un chaffeur</h2>
            <a href="Admin.php" class="btn-close" ></a>
          </div>    
          <div class="modal-body p-5 pt-0">

            <form method="POST">

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="nom">
                <label for="floatingInput">Nom </label>
              </div>

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="postnom">
                <label for="floatingInput">postnom </label>
              </div>

              <div class="form-floating mb-2">
                <input type="number" class="form-control rounded-4" id="floatingInput" name="phone">
                <label for="floatingInput">Tel </label>
              </div>

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="adresse">
                <label for="floatingInput">residence </label>
              </div>

              <div class="form-floating mb-2">
                <input type="text" class="form-control rounded-4" id="floatingInput" name="Matricule">
                <label for="floatingInput">Matricule </label>
              </div>

            <div class="form-floating mb-2">
            	
              


              <select class=" form-control mb-2" name="codeCat" >

              	<?php 

                 $repChat=   $bdd-> query("SELECT * from categoriet");
                 while ($tab=$repChat->fetch()) {
                  
                ?>
                
                <option value="<?php echo $tab['codeCat']; ?>">
                	<?php echo $tab['categorie']; ?>
                		
                	</option>
                <?php 

               }

               ?>
                 
              </select>
             
               
                <label>categorie travailleur</label>
           </div>

           <div class="form-floating mb-2">
            	
              


              <select class=" form-control mb-2" name="codev">
              	<?php 

                 $repChat=   $bdd-> query("SELECT * from vehicule");
                 while ($tab=$repChat->fetch()) {
                  
                ?>
                
                <option  value="<?php echo $tab['id']; ?>">

                	<?php echo $tab['description']."  ". $tab['numP']; ?>
                		
                	</option>
                <?php 

               }

               ?>
                 
              </select>
             
               
                <label> vehicule</label>
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