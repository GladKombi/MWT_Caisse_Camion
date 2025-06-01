<?php
include 'connexionBd.php'; //Se connecter à la BD
session_start(); //demarer la sessiin 
if (empty($_SESSION['id'])) //code pour sécuriser la page 
{
    header("Location: Index.php"); //redirection de l'utilisateur
}
if (isset($_GET["Chauffeur"]) && isset($_GET["datD"]) && isset($_GET["datF"])) {
    # Récupération des paramètres de l'URL
    $idChauffeur = $_GET["Chauffeur"];
    $dat = $_GET["dat"];
    $datD = $_GET["datD"];
    $datF = $_GET["datF"];
    $selectChauffeur = $bdd->prepare("SELECT * FROM travailleur WHERE id=?");
    $selectChauffeur->execute(array($idChauffeur));
    $detail = $selectChauffeur->fetch();
    $nom = $detail["nom"];
    $Postnom = $detail["postnom"];
    $etat = 0; // Etat pour les depenses et recettes
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>situation Paiement </title>
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

        @media print {
            .no-print {
                display: none;
            }
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        th,
        td,
        tr {
            border: 5px solid black;
            border-collapse: collapse;
        }
    </style>

</head>

<body>

    <?php

    include "navbar.php";

    ?>

    <div class="container">
        <div class="row ">
            <div class="col-3"></div>
            <div class="col-3 no-print mt-3">
                <a href="Admin.php" class="btn btn-danger col-12 me-2">Quitter</a>
            </div>
            <div class="col-3 no-print mt-3">
                <button onclick="window.print()" class="btn btn-success bi bi-printer col-12 me-2"> Imprimer</button>
            </div>
            <div class="col-3"></div>
            <!-- <div class="col-6 no-print mb-3">
                <button onclick="saveAsImage()" class="btn btn-dark col-12 me-2">Imprimer</button>
            </div> -->
        </div>
        <div class="row" id="invoice">
            <div class="bd-example fon col-lg-12">
                <div class="row m-3">
                    <h2 class="text-center mb-2">Situation de paiement</h2>
                    <div class="col-6">
                        <h3>MwiraTrans</h3>
                        <h5>Q.Vungi</h5>
                        <h5>rue kin , N° 1</h5>
                        <h5>Tel : +243 998 385 019</h5>
                    </div>
                    <div class="col-6 text-center">
                        <h5>Bbo le <?php $date = date('d/m/Y');
                                    echo ($date); ?></h5>
                    </div>
                    <h3 class="text-center">Chauffeur : <?php echo strtoupper($nom . " " . $Postnom); ?></h3>
                </div>
                <table class="table table-sm table-bordered">
                    <thead>
                        <div class="titre bg-success text-white mt-3 text-center">
                            Les depenses
                        </div>
                        <tr>
                            <th>N°</th>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Détail</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $affichercat = $bdd->prepare("SELECT depenses.dateD, depenses.libelle, depenses.details, depenses.montant FROM depenses, payement WHERE  depenses.codeT=? AND depenses.etat=1 AND depenses.dateD > $datD ORDER BY depenses.dateD DESC ;");
                            $affichercat->execute(array($idChauffeur));
                            $numero = 0;
                            while ($tab = $affichercat->fetch()) {
                                $numero += 1;
                            ?>
                        <tr>
                            <th scope="row"><?php echo  $numero; ?></th>
                            <td><?php echo $tab[0]; ?></td>
                            <td><?php echo $tab[1]; ?></td>
                            <td><?php echo $tab[2]; ?></td>
                            <td><?php echo $tab[3]; ?> $</td>
                        </tr>
                    <?php
                            }
                    ?>
                    </tr>
                    </tbody>
                </table>
                <table class="table table-sm  table-bordered">
                    <thead>
                        <tr>
                            <div class="titre bg-success text-white text-center">
                                Recettes
                            </div>
                            <th>N°</th>
                            <th>Date</th>
                            <th>Libelle</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $affichercat = $bdd->prepare("SELECT recette.dateR,recette.libelle,recette.montant FROM recette WHERE recette.idT=? AND recette.etat=1 AND recette.dateR > $datD ORDER BY recette.dateR DESC ;");
                        $affichercat->execute(array($idChauffeur));
                        $numero = 0;
                        while ($tab = $affichercat->fetch()) {
                            $numero += 1;
                        ?>
                            <tr>
                                <th scope="row"><?php echo  $numero; ?></th>
                                <td><?php echo $tab[0]; ?></td>
                                <td><?php echo $tab[1]; ?></td>
                                <td><?php echo $tab[2]; ?> $</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="row mt-3 mb-3">
                <?php
                $affichercat = $bdd->prepare("SELECT payement.date, payement.detail, payement.dateD, payement.dateF, (payement.aideChauf+payement.montant) as montant, payement.montant as chauffeur, payement.aideChauf, travailleur.nom, travailleur.postnom, travailleur.id as chaufId FROM payement, travailleur WHERE travailleur.id=payement.idchauffeur AND payement.date=? AND travailleur.id=?;");
                $affichercat->execute(array($dat, $idChauffeur));
                while ($tab = $affichercat->fetch()) {
                ?>
                    <div class="card shadow text-center">
                        <h3 class="text-center m-2">Details de paiement</h3>
                        <p class="text-center">
                            Libellé : <?php echo  $tab[1]; ?>
                        </p>
                        <div class="row mb-2">
                            <div class="col-6">
                                Date début : <h5><?php echo  $tab[2]; ?></h5>
                            </div>
                            <div class="col-6">
                                Date Fin : <h5><?php echo  $tab[3]; ?></h5>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                Salaire aide-chaufeur : <h5><?php $salaireAide=$tab[5]; echo $salaireAide ?> $</h5>
                            </div>
                            <div class="col-6">
                                Salaire Chauffeur<h5><?php $sailaireChauf= $tab[6]; echo $sailaireChauf?> $</h5>
                            </div>
                        </div>
                        <div class="row">
                            <h5>Total : <?php $totalSalaire=$sailaireChauf+$salaireAide; echo $totalSalaire ?> $</h5>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>

        </div>
    </div>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    function saveAsImage() {
        const invoiceElement = document.getElementById('invoice');
        html2canvas(invoiceElement).then(canvas => {
            const link = document.createElement('a');
            link.download = 'facture.png';
            link.href = canvas.toDataURL('photo/png');
            link.click();
        });
    }
</script>

</html>