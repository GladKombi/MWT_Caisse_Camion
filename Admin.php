<?php 
include 'connexionBd.php';//Se connecter à la BD
session_start();//demarer la sessiin 
if (empty($_SESSION['id'] )) //code pour sécuriser la page 
{
  header("Location: Index.php");//redirection de l'utilisateur
}

if (isset($_GET['codeCat'])) {
 $codeCat=$_GET['codeCat'];
$bdd->query("DELETE FROM categoriet WHERE codeCat='$codeCat' ");
header("location:Admin.php");

}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administation</title>
    <link rel="stylesheet" href="assets/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font/bootstrap-icons.css">

    <style >
      .fon{
        background: #f1f1f1;
        margin: 1%;
      }
      .titre
      {
        width: 100%;
        font-weight: 600;
      }
      .font-w
      {
        font-weight: 600;
      }
    </style>
    
</head>
<body>
    
    <?php 

          include "navbar.php";

     ?>


    <div class="container">


<div class="row">
  
  <div class="bd-example text-center fon col-lg-3">
        <table class="table table-sm  table-bordered">
          <thead>
          <tr class="text-center">
            <div class="titre bg-success text-white">
              Profil
            </div>
            
          </tr>
          </thead>
          <tbody>
          <tr>
           <?php echo $_SESSION['id']; ?>
            <td class="font-w">Nom</td>
            <td class="font-w">Postnom</td>
            
          </tr>
          <tr>
            <?php 

            $id=$_SESSION['id'];

        $recuperUser= $bdd->prepare("SELECT * FROM admin WHERE id='$id'");//recuper tous dans la table admin et comparer avec les zonnes de saisie
        $recuperUser->execute();//execute la reqquette
        $recup = $recuperUser->fetch();//stocke dans le tablo
        if($recup)
        {
          ?> 
            <td><?php echo $recup['nom']; ?></td>
            <td><?php echo $recup['postnom']; ?></td>
          <?php  
        }

             ?>
            
            
          </tr>
          
          </tbody>
        </table>
        </div>



        <div class="bd-example fon text-center col-lg-8">
        <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
               Les operations
            </div>
           
          </tr>
          </thead>
          <tbody>
          
          <tr>
           
            <td><a href="AjouterAdmin.php" class="btn btn-success text-center">Ajouter Admin</a></td>
            <td><a href="catTravail.php" class="btn btn-success text-center">Catégotie travailleur</a></td>
            <td><a href="catDepense.php" class="btn btn-success text-center">Catégirie depanse</a></td>
           
           
          </tr>
          <tr>
           
            
            <td><a href="vehicule.php" class="btn btn-success text-center">vehicule</a></td>
             <td><a href="Chaffeur.php" class="btn btn-success text-center">Chaffeur</a></td>
            <td><a href="Recettes.php" class="btn btn-success text-center">Recettes</a></td>
           
          </tr>

          <tr>
           
            
            <td><a href="depanse.php" class="btn btn-success text-center">Depenses</a></td>
            <td><a href="paiement.php" class="btn btn-success text-center">paiement</a></td>
            
          </tr>
         
          </tbody>
        </table>
        </div>
</div>

        <div class="row">
          <div class="bd-example fon text-center col-lg-5">
        <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              categorie travailleur
            </div>
            <th>N°</th>
            <th>categorie</th>
            <th>Actions</th>

            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT * FROM categoriet ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab['codeCat']; ?></td>
                          <td><?php echo $tab['categorie']; ?></td>


                          <td>

                            <a href="M_C_T.php?codeCat=<?php echo $tab['codeCat']; ?>" class="btn btn-success text-white">Modifier</a>
                            

                          </td>
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
         
        
          </tbody>
        </table>
        </div>



        <div class="bd-example text-center fon col-lg-6">
         <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              categorie vahucule
            </div>
            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT * FROM categoried ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab['categorie']; ?></td>
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
         
        
          </tbody>
        </table>
        </div>


        </div>


        <div class="row">
          <div class="bd-example fon text-center col-lg-4">
         <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              Vahicule
            </div>

            <th>N°</th>
            <th>N° plaque</th>
            <th>Description</th>
            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT * FROM vehicule ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab[1]; ?></td>
                          <td><?php echo $tab[2]; ?></td>
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
         
        
          </tbody>
        </table>
        </div>



<div class="bd-example fon text-center col-lg-7">
         <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              Recettes
            </div>

            <th>N°</th>
            <th>Date</th>
            <th>Nom</th>
            <th>Postom</th>
            <th>Libelle</th>
            <th>Montant</th>
            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT recette.dateR, travailleur.nom, travailleur.postnom , recette.libelle,recette.montant FROM recette,  travailleur WHERE travailleur.id=recette.idT ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab[0]; ?></td>
                           <td><?php echo $tab[1]; ?></td>
                          <td><?php echo $tab[2]; ?></td>
                          <td><?php echo $tab[3]; ?></td>
                          <td><?php echo $tab[4]; ?></td>
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
         
        
          </tbody>
        </table>
        </div>



        </div>

        <div class="row">
          
           <div class="bd-example fon text-center col-lg-12">
        <table class="table table-sm table-bordered">
          <thead>
            <div class="titre bg-success text-white">
             Les depenses
            </div>
          <tr>
            
             <th>N°</th>
            <th>Date</th>
            <th>Libellé</th>
            <th>Détail</th>
            <th>Montant</th>
            <th>Travailleur</th>
            <th>Fonction</th>
           
          </tr>
          </thead>
          <tbody>
          <tr>
            <?php 
                  $affichercat= $bdd->query("SELECT depenses.dateD, depenses.libelle, depenses.details, depenses.montant, travailleur.nom, travailleur.postnom, categoried.categorie FROM depenses, categoried, travailleur WHERE travailleur.id=depenses.codeT AND categoried.codeCat=depenses.codeCatDepanse");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab[0]; ?></td>
                          <td><?php echo $tab[1]; ?></td>
                           <td><?php echo $tab[2]; ?></td>
                          <td><?php echo $tab[3]; ?></td>
                          <td><?php echo $tab[4]." ".$tab[5]; ?></td>
                          <td><?php echo $tab[6]; ?></td>
                          
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
          </tr>
          
          </tbody>
        </table>
        </div>
        </div>




        <div class="row">
          
           <div class="bd-example fon text-center col-lg-12">
        <table class="table table-sm table-bordered">
          <thead>
            <div class="titre bg-success text-white">
             Les travailleurs
            </div>
          <tr>
            
             <th>N°</th>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Postnom</th>
            <th>Adresse</th>
            <th>N° tel</th>
            <th>Véhicule</th>
            <th>N° plaque</th>
            <th>Fonction</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <?php 
                  $affichercat= $bdd->query("SELECT travailleur.matricule AS matricule, travailleur.nom AS nomTravailleur, travailleur.postnom as postnomTravailleur, travailleur.residence as adresseTravailleur, travailleur.numTel as telephoneTravailleur, vehicule.description as vehicule, vehicule.numP as numeroPlaque, categoriet.categorie as fontion FROM travailleur, vehicule, categoriet WHERE travailleur.codeCat=categoriet.codeCat AND vehicule.id=travailleur.codeV ORDER BY travailleur.id ASc ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo $tab['matricule']; ?></td>
                          <td><?php echo $tab['nomTravailleur']; ?></td>
                           <td><?php echo $tab['postnomTravailleur']; ?></td>
                          <td><?php echo $tab['adresseTravailleur']; ?></td>
                           <td><?php echo $tab['telephoneTravailleur']; ?></td>
                          <td><?php echo $tab['vehicule']; ?></td>
                           <td><?php echo $tab['numeroPlaque']; ?></td>
                          <td><?php echo $tab['fontion']; ?></td>
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
          </tr>
          
          </tbody>
        </table>
        </div>
        </div>

    </div>
    <div class="container">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <div class="col-md-4 d-flex align-items-center">
          <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
            <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"/></svg>
          </a>
          <span class="text-muted">&copy;MWIRA_Trans</span>
        </div>
    
        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
          <li class="ms-3"><a class="text-muted" href="https://mobile.facebook.com/photo.php?fbid=1573448029707322&id=100011264207963&set=a.139872809731525&eav=Afaog_E3uQFlUBJk5C33FBogT3SYxZU0RtBb_52_1puppsz4oRCQgjZ5lM20dNmam7k&paipv=0&source=11&refid=17"><img src="photo/icons8_facebook_circled_30px.png" ></a></li>
          <li class="ms-3"><a class="text-muted" href="mailto:gladkombigs@gmail.com"><img src="photo/icons8_mail_filled_50px.png" ></a></li>
          <li class="ms-3"><a class="text-muted" href="tel:0997019883"><img src="photo/icons8_phone_filled_50px (2).png" ></a></li>
          <li class="ms-3"><a class="text-muted" href="https://wa.me/+243997019883"><img src="photo/icons8_whatsapp_60px.png" ></a></li>
         
        </ul>
      </footer>
    </div>
    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
