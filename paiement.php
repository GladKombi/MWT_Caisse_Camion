<?php
# Se connecter à la BD
include 'connexionBd.php';
# Demarer la session 
session_start();
# Sécuriser la page au cas où l'utilisateur n'est pas connecté
if (empty($_SESSION['id'])) {
  header("Location: Index.php"); //redirection de l'utilisateur
}

if (isset($_POST['envoyer'])) {
  # Selectionner le chauffeur et les dates dans le formulaire
  $travailleur = $_POST['Chauffeur'];
  $dateDebut = $_POST['dateDebut'];
  $dateFin = $_POST['dateFin'];
  $etat = 0; // Etat pour les depenses et recettes
  $statut = 0; // Statut pour les depenses et recettes
  # Selectionner les recettes non cloturer du chauffeur
  $getRecette = $bdd->prepare("SELECT SUM(recette.montant), travailleur.nom FROM recette, travailleur WHERE travailleur.id=recette.idT AND travailleur.id=?  AND recette.dateR AND recette.etat=? AND recette.statut=? BETWEEN '$dateDebut' AND '$dateFin' ");
  $getRecette->execute(array($travailleur, $etat, $statut));
  $recette = 0;
  while ($tab = $getRecette->fetch()) {
    $recette = $tab[0];
  }
  # Selectionner les depenses non cloturer du chauffeur
  $getDepense = $bdd->prepare("SELECT sum(depenses.montant), travailleur.nom FROM depenses, travailleur, categoried WHERE depenses.codeT=travailleur.id and depenses.codeCatDepanse=categoried.codeCat AND categoried.categorie='legere' AND travailleur.id=? and depenses.dateD AND depenses.etat=? AND depenses.statut=? BETWEEN '$dateDebut' And '$dateFin' ");
  $getDepense->execute(array($travailleur, $etat, $statut));
  $depasse = 0;
  while ($tab = $getDepense->fetch()) {
    if ($tab[0] == "") {
      $depasse = 0;
    } else {
      $depasse = $tab[0];
    }
  }
  # Calcul du solde et du montant à payer sur les recettes et depenses non cloturées
  $solde = $recette - $depasse;
  $chauf = ($solde * 10) / 100;
  $aide_chauf = ($solde * 5) / 100;
  $MotnatApayer = $chauf + $aide_chauf;
  # recuperer le nom du chauffeur et le details de paiement
  $detail = htmlspecialchars($_POST['detail']);
  $chauffeur = htmlspecialchars($_POST['Chauffeur']);
  if ($solde == 0) {
    $notification = "Aucun montant à payer pour ce chauffeur";
  } else {
    # Vérification des dates
    if (empty($travailleur) || empty($dateDebut) || empty($dateFin) || empty($detail)) {
      $notification = "Veuillez remplir tous les champs";
    } elseif ($dateDebut == $dateFin) {
      $notification = "La date de debut et de fin ne peuvent pas etre identique";
    } elseif ($dateDebut < date("Y-m-d") || $dateFin < date("Y-m-d")) {
      $notification = "La date de debut et de fin doivent etre superieur ou egal à la date du jour";
    } elseif ($dateDebut == "" || $dateFin == "") {
      $notification = "Veuillez entrer les dates";
    } elseif ($travailleur == "") {
      $notification = "Veuillez selectionner un chauffeur";
    } elseif ($MotnatApayer <= 0) {
      $notification = "Aucun montant à payer pour ce chauffeur";
    }
    if ($dateDebut > $dateFin) {
      $notification = "La dete du debut doit etre enterieur à la date du fin  ";
    } else {
      # Enregistrement de l'operation de paiment dans la base de données
      $recuperUser = $bdd->prepare("SELECT payement.dateD, payement.dateF, payement.idchauffeur FROM payement WHERE payement.dateD='$dateDebut'  and payement.dateF='$dateFin' and payement.idchauffeur='$travailleur' ");
      $recuperUser->execute();
      $recup = $recuperUser->fetch();
      if ($recup) {
        $notification = "Cette opération existe deja";
      } else {
        $query = $bdd->prepare("INSERT INTO `payement`(`date`, `detail`, `dateD`, `dateF`, `aideChauf`, `idchauffeur`, `montant`) VALUES (now(),?,?,?,?,?,?)");
        $query->execute(array($detail, $dateDebut, $dateFin, $aide_chauf, $travailleur, $chauf));
        if (!$query) {
          $notification = "Erreur lors de l'enregistrement du paiement";
        } else {
          # Mise à jour de l'état recettes du chauffeur
          $cloture = 1; // Etat de cloture
          $setClotureRecette = $bdd->prepare("UPDATE `recette` SET `etat`=? WHERE recette.statut =? and recette.etat=?");
          $testR = $setClotureRecette->execute(array($cloture, $statut, $etat));
          # Mise à jour de l'état depenses du chauffeur
          $setClotureDepense = $bdd->prepare("UPDATE `depenses` SET `etat`=? WHERE depenses.statut =? and depenses.etat=?");
          $testD = $setClotureDepense->execute(array($cloture, $statut, $etat));
          if ($testD == true) {
            $notification = "Paiement du chauffeur " . $travailleur . " effectué avec succès";
          }
        }
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paiement du travailleur</title>
  <link rel="stylesheet" href="assets/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/font/bootstrap-icons.css">

  <style>
    .fon {
      background: #f1f1f1;
      margin: 1%;
    }

    .titre {
      width: 100%;
      font-weight: 600;
    }

    .font-w {
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

      <div class="col-lg-5">
        <form method="post" class="col-lg-12 mt-5" enctype="multipart/form-data">

          <div class="row border pb-2 text-center">
            <div class="col-lg-6">

              <label for="exampleInputEmail1">Détail</label>
              <input type="text" class="form-control" id="exampleInputEmail1" name="detail" required>

              <label for="ok" class="mb-2">Chauffeur</label>
              <select name="Chauffeur" id="ok" class="form-control padding ">
                <?php
                $recuperIdFac = $bdd->prepare('SELECT travailleur.* FROM travailleur, categoriet WHERE travailleur.codeCat=categoriet.codeCat AND categoriet.categorie="chauffeur"  ');
                $recuperIdFac->execute(array());
                while ($idfac = $recuperIdFac->fetch()) {
                ?>
                  <option value="<?php echo $idfac['id']; ?>"><?php echo $idfac['nom'] . " " . $idfac['postnom']; ?></option>
                <?php
                }

                ?>
              </select>


            </div>

            <div class="col-lg-6">

              <div class="col-lg-12S">
                <label for="exampleInputEmail1">Date du debut</label>
                <input type="date" class="form-control" id="exampleInputEmail1 w-50" name="dateDebut" required>

              </div>

              <div class="col-lg-12">
                <label for="exampleInputEmail1">Date de fin</label>
                <input type="date" class="form-control" id="exampleInputEmail1 w-50" name="dateFin" required>

              </div>
            </div>
            <p><?php if (isset($notification)) echo $notification; ?></p>
            <a href="Admin.php" class="text-success"><button type="submit" name="envoyer" class="btn bg-success mb-2  text-center text-white w-50 ">Enregistrer</button></a>

            <a href="Admin.php" class="text-success"><button type="button" class="btn bg-success mb-2 mt-1  text-center text-white w-50 ">Retour</button></a>
          </div>
      </div>
      </form>
    </div>
    <div class="row">
      <div class="bd-example fon text-center col-lg-12">
        <table class="table table-sm  table-bordered">
          <thead>
            <tr>
              <div class="titre bg-success text-white">
                Liste des paiements
              </div>
              <th>N°</th>
              <th>Date</th>
              <th>Detail</th>
              <th>date debut</th>
              <th>date fin</th>
              <th>Motnat</th>
              <th>S chauffeur</th>
              <th>S aide</th>
              <th>Chauffeur</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>

            <?php
            $affichercat = $bdd->query("SELECT payement.date, payement.detail, payement.dateD, payement.dateF, (payement.aideChauf+payement.montant) as montant, payement.montant as chauffeur, payement.aideChauf, travailleur.nom, travailleur.postnom, travailleur.id as chaufId FROM payement, travailleur WHERE travailleur.id=payement.idchauffeur order by payement.date desc ");
            $numero = 0;
            while ($tab = $affichercat->fetch()) {
              $numero += 1;
            ?>

              <tr>
                <th scope="row"><?php echo  $numero; ?></th>
                <td><?php echo  $tab[0]; ?></td>
                <td><?php echo  $tab[1]; ?></td>
                <td><?php echo  $tab[2]; ?></td>
                <td><?php echo  $tab[3]; ?></td>
                <td><?php echo  $tab[4]; ?></td>
                <td><?php echo  $tab[5]; ?></td>
                <td><?php echo  $tab[6]; ?></td>
                <td><?php echo  $tab[7] . " " . $tab[8]; ?></td>
                <td>
                  <a href="Chauf-situation.php?Chauffeur=<?php echo  $tab["chaufId"]; ?>&dat=<?php echo  $tab[0]; ?>&datD=<?php echo  $tab[2]; ?>&datF=<?php echo  $tab[3]; ?>" class="btn btn-success btn-sm bi bi-eye"></a>
                </td>

              </tr>

            <?php
            }

            ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>