<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Utilisateur.php';
require_once dirname(__DIR__) . '/models/Travailleur.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if(isset($_SESSION['user'])&&$_SERVER['REQUEST_METHOD']==='GET'){$this->redirect(($_SESSION['user']['role']??'')==='EMPLOYE'?'/mon-espace':'/dashboard');return;}
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = strtoupper(trim($_POST['matricule'] ?? ''));
            $password = $_POST['password'] ?? '';
            $csrf=(string)($_POST['csrf_token']??'');

            if(!hash_equals($_SESSION['login_csrf']??'',$csrf)){$error='La session du formulaire a expiré. Veuillez réessayer.';$this->newLoginToken();$this->view('auth/login',compact('error','matricule'));return;}
            if($matricule===''||strlen($password)<4){$error='Saisissez un matricule et un mot de passe d’au moins 4 caractères.';$this->view('auth/login',compact('error','matricule'));return;}

            $model = new Utilisateur();$workerModel=new Travailleur();
            $user = $model->findByMatricule($matricule);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                session_regenerate_id(true);unset($_SESSION['login_csrf']);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nom_utilisateur' => $user['nom_utilisateur'],
                    'profil' => $user['profil'],
                    'matricule' => $user['matricule'],
                    'role' => $user['role'],
                    'travailleur_id' => $user['travailleur_id'],
                    'auth_source'=>'utilisateur',
                ];
                $model->markLogin((int)$user['id']);$model->logAuthentication((int)$user['id'],'LOGIN');
                $this->redirect($user['role']==='EMPLOYE'?'/mon-espace':'/dashboard');
            }
            $worker=$workerModel->findActiveByMatricule($matricule);
            if($worker&&password_verify($password,$worker['motde_passe'])){
                session_regenerate_id(true);unset($_SESSION['login_csrf']);
                $_SESSION['user']=['id'=>(int)$worker['id'],'nom_utilisateur'=>trim(implode(' ',array_filter([$worker['nom'],$worker['postnom'],$worker['prenom']]))),'profil'=>$worker['profil'],'matricule'=>$worker['matricule'],'role'=>'EMPLOYE','travailleur_id'=>(int)$worker['id'],'auth_source'=>'travailleur'];
                $workerModel->logAuthentication((int)$worker['id'],'LOGIN');$this->redirect('/mon-espace');
            }

            $error = 'Matricule ou mot de passe incorrect.';
            $this->view('auth/login', compact('error','matricule'));
            return;
        }
        $this->newLoginToken();
        $this->view('auth/login');
    }

    public function logout(): void
    {
        if(isset($_SESSION['user']['id'])){try{if(($_SESSION['user']['auth_source']??'utilisateur')==='travailleur')(new Travailleur())->logAuthentication((int)$_SESSION['user']['id'],'LOGOUT');else(new Utilisateur())->logAuthentication((int)$_SESSION['user']['id'],'LOGOUT');}catch(Throwable$e){}}
        $_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_destroy();
        $this->redirect('/login');
    }
    private function newLoginToken():void{$_SESSION['login_csrf']=bin2hex(random_bytes(32));}
}
