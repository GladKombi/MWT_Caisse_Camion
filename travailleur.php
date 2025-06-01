<?php 
include 'connexionBd.php';
session_start();


if (empty($_SESSION['identifant'])) {
	header("location: Index.php");
}

$id=$_SESSION['identifant'];





 ?>



 <!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement du travailleur</title>
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

<!-- tous les calculs -->
<?php 


 	$affichercat= $bdd->query("SELECT   SUM(recette.montant) FROM recette, travailleur WHERE recette.idT=travailleur.id AND travailleur.id='$id' ");
     $recette=0;
     while($tab=$affichercat->fetch())
     {
     	if ($tab[0]=="")
     	 {
     		$recette=0;
     	 }
     	 else
     	 {
     	 	$recette=$tab[0];
     	 }
     }

 

     $affichercat= $bdd->query("SELECT SUM(depenses.montant) FROM depenses, travailleur, categoried WHERE depenses.codeT=travailleur.id AND  categoried.codeCat=depenses.codeCatDepanse AND  categoried.categorie='legere' AND travailleur.id='$id' ");
     $Depense=0;
     while($tab=$affichercat->fetch())
     {
     	if ($tab[0]=="")
     	 {
     		$Depense=0;
     	 }
     	 else
     	 {
     	 	$Depense=$tab[0];
     	 }
     }

      $affichercat= $bdd->query("SELECT SUM(payement.montant+payement.aideChauf) FROM payement, travailleur WHERE travailleur.id=payement.idchauffeur AND travailleur.id='$id' ");
     $montantPayer=0;
     while($tab=$affichercat->fetch())
     {
     	if ($tab[0]=="")
     	 {
     		$montantPayer=0;
     	 }
     	 else
     	 {
     	 	$montantPayer=$tab[0];
     	 }
     }


$MAP=$recette-$Depense;


$MontanttTotal = $MAP*15/100;
$chauffeur=$MAP*10/100;
$aide=$MAP*5/100;

$reste=$MontanttTotal-$montantPayer;




                    

 ?>













    <div class="container">

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

            <td class="font-w">Nom</td>
            <td class="font-w">Postnom</td>
            
          </tr>
          <tr>
            <?php 

        $recuperUser= $bdd->prepare("SELECT * FROM travailleur WHERE id='$id'");//recuper tous dans la table admin et comparer avec les zonnes de saisie
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



        <div class="row">
          <div class="bd-example fon text-center col-lg-5">
        <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              Recette
            </div>
            <th>N°</th>
            <th>Date</th>
            <th>Libele</th>
            <th>Montantt</th>
            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT  recette.dateR, recette.libelle, recette.montant FROM recette, travailleur WHERE recette.idT=travailleur.id AND travailleur.id='$id' ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo  $tab[0]; ?></td>
                          <td><?php echo  $tab[1]; ?></td>
                          <td><?php echo  $tab[2]; ?></td>
                         
                          
                        </tr>

                      <?php 
                    }
                   

             ?>
         
        
          </tbody>
        </table>
        </div>



 <div class="bd-example fon text-center col-lg-6">
        <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              Depenses
            </div>
            <th>N°</th>
            <th>Date</th>
            <th>Libele</th>
            <th>Montantt</th>
            
          </tr>
          </thead>
          <tbody>

            <?php 
                  $affichercat= $bdd->query("SELECT  depenses.dateD, depenses.libelle, depenses.montant FROM depenses, travailleur WHERE depenses.codeT=travailleur.id AND travailleur.id='$id' ");
                  $numero=0;
                  while($tab=$affichercat->fetch())
                   {
                    $numero+=1;
                      ?>

                        <tr>
                          <th scope="row"><?php echo  $numero; ?></th>
                          <td><?php echo  $tab[0]; ?></td>
                          <td><?php echo  $tab[1]; ?></td>
                          <td><?php echo  $tab[2]; ?></td>
                         
                          
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
        <table class="table table-sm  table-bordered">
          <thead>
          <tr>
            <div class="titre bg-success text-white">
              Mouvement
            </div>
            <th>Recette</th>
            <th>Depense</th>
            <th>Solde</th>
            <th>Montant à payer</th>
            <th>chauffeur</th>
            <th>Son Aide</th>
            <th>Montant payer</th>
            <th>Dette</th>
            
            
          </tr>
          </thead>
          <tbody>

          

                        <tr>
                        
                          <td><?php echo $recette; ?></td>
                          <td><?php echo $Depense; ?></td>
                          <td><?php echo $MAP; ?></td>
                          <td><?php echo $MontanttTotal; ?></td>
                          <td><?php echo $chauffeur; ?></td>
                          <td><?php echo $aide; ?></td>
                          <td><?php echo $montantPayer; ?></td>
                          <td><?php echo $reste; ?></td>
                         
                         
                          
                        </tr>

           
         
        
          </tbody>
        </table>
        </div>
          </div>
    </div>
    
    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
