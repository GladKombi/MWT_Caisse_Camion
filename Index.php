<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil</title>
  <link rel="stylesheet" href="assets/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>


  <?php

  include "navbar.php";

  ?>

  <div class="container">

    <div class="bd-example mb-2">

      <h1 class="text-white text-center mb-5">MWIRA_Trans</h1>

      <p class="text-white text-center">
        Nous somme l'agence de transport basé en ville de Butembo. <br>
        nous faisons le transport des materiels de construction<br>
        à l'occurance du ciment, des briques, du sable et tant d'autres <br>
        MWIRA_Trans est disposer à fournir des services de qualité exceptionnels
      </p>
      <div class="container">

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          <div class="col">
            <div class="card shadow-sm">

              <div class="card-body text-center">
                <p class="card-text">
                  <a href="login.php?fonction=admin"><img src="photo/icons8_plus_32px.png" alt=""></a><br>
                  <b>Connexion</b> <br> Administrateur
                </p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="login.php?fonction=admin" class="btn btn-lg btn-success w-100 mx-0 mb-2">se connecter</a>

                  </div>

                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="card shadow-sm">

              <div class="card-body text-center">
                <p class="card-text">
                  <a href="login.php?fonction=Employeur"><img src="photo/icons8_plus_32px.png" alt=""></a><br>
                  <b>Connexion</b> <br> Employeur
                </p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="login.php?fonction=Employeur" class="btn btn-lg btn-success mb-2 text-center">se connecter</a>

                  </div>

                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="card shadow-sm">

              <div class="card-body text-center">
                <p class="card-text">
                  <a href="login.php?fonction=Employe"><img src="photo/icons8_plus_32px.png" alt=""></a><br>
                  <b>Connexion</b> <br> Employé
                </p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <a href="login.php?fonction=Employe" class="btn btn-lg btn-success w-100 mx-0 mb-2">se connecter</a>

                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
  <div class="container">
    <div class="row  row-cols-1 row-cols-md-2 g-4">
      <div class="col">
        <div class="card">
          <img src="photo/Screenshot_20220907-204854.png" width="100%" height="300" alt="">

          <div class="card-body">
            <h5 class="card-title">Nos Transports</h5>
            <p class="card-text">
              MWIRA_Trans est une agence de transport des produits de tout genre, elle s'est specialiser
              au fil des ans dans le transport des materiaux de construction (sables, briques, ciment, pieres, ...), cela ne reste pas
              l'activité principale de MWIRA_Trans
            </p>
            <a href="" class="btn btn-success bi bi-eye btn-sm"> Details</a>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <img src="photo/bene2.png" width="100%" height="350" alt="">

          <div class="card-body">
            <h5 class="card-title">Nos Transports</h5>
            <p class="card-text">
              MWIRA_Trans est une agence de transport des produits de tout genre, elle s'est specialiser
              au fil des ans dans le transport des materiaux de construction (sables, briques, ciment, pieres, ...), cela ne reste pas
              l'activité principale de MWIRA_Trans
            </p>
            <a href="" class="btn btn-success bi bi-eye btn-sm"> Details</a>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <img src="photo/Screenshot_20220724-000841.png" width="100%" height="350" alt="">

          <div class="card-body">
            <h5 class="card-title">Servicing</h5>
            <p class="card-text">
              les Servicing chez MWIRA_Trans reguliere peu importe si le camion a beaucoup travailler oupas !
              vous pouvez sur de leur services on line comme off line. le Servicing est obligatiore chez nous
              voici certains elements qui devrons vous en rassuré:
            <ul>
              <li>le lavage hebdomadaires des vehicules</li>
              <li>Un entretien mensuel</li>
              <li>remplacement des pieces usées</li>
            </ul>
            </p>
            <a href="" class="btn btn-success bi bi-eye btn-sm"> Details</a>
          </div>
        </div>
      </div>
    </div>
    
  </div>
  <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
    <div class="col-md-4 d-flex align-items-center">
      <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
        <svg class="bi" width="30" height="24">
          <use xlink:href="" />
        </svg>
      </a>
      <span class="text-muted">&copy;MWIRA_Trans - <?php $anne=date('Y'); echo$anne?></span>
    </div>

    <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
      <li class="ms-3"><a class="text-muted" href="https://mobile.facebook.com/photo.php?fbid=1573448029707322&id=100011264207963&set=a.139872809731525&eav=Afaog_E3uQFlUBJk5C33FBogT3SYxZU0RtBb_52_1puppsz4oRCQgjZ5lM20dNmam7k&paipv=0&source=11&refid=17"><img src="photo/icons8_facebook_circled_30px.png"></a></li>
      <li class="ms-3"><a class="text-muted" href="mailto:gladkombigs@gmail.com"><img src="photo/icons8_mail_filled_50px.png"></a></li>
      <li class="ms-3"><a class="text-muted" href="tel:0997019883"><img src="photo/icons8_phone_filled_50px (2).png"></a></li>
      <li class="ms-3"><a class="text-muted" href="https://wa.me/+243997019883"><img src="photo/icons8_whatsapp_60px.png"></a></li>

    </ul>
  </footer>
  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>