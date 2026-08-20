<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Utilisateur.php';
require_once dirname(__DIR__) . '/middleware/AdminMiddleware.php';

class UtilisateurController extends Controller
{
    public function index(): void
    {
        AdminMiddleware::handle();
        $model = new Utilisateur();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($model);
            return;
        }

        $utilisateurs = $model->getAll();$travailleurs=$model->getAvailableWorkers();
        $message = $_SESSION['user_message'] ?? null;
        $messageType = $_SESSION['user_message_type'] ?? 'success';
        unset($_SESSION['user_message'], $_SESSION['user_message_type']);
        $this->view('utilisateurs/index', compact('utilisateurs','travailleurs','message','messageType'));
    }

    private function handlePost(Utilisateur $model): void
    {
        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);
        $nomUtilisateur = trim($_POST['nom_utilisateur'] ?? '');
        $role = strtoupper(trim($_POST['role'] ?? ''));
        $password = $_POST['mot_de_passe'] ?? '';
        $travailleurId=$role==='EMPLOYE'?(int)($_POST['travailleur_id']??0):0;if($role==='EMPLOYE'&&$travailleurId<=0)$travailleurId=0;

        try {
            if ($action === 'delete') {
                if ($id === (int) ($_SESSION['user']['id'] ?? 0)) throw new RuntimeException('Vous ne pouvez pas désactiver votre propre compte.');
                $model->deactivate($id);
                $this->setMessage('Utilisateur désactivé avec succès.');
            } elseif (in_array($role, ['ADMIN', 'CEO', 'COMPTABLE', 'EMPLOYE'], true) && $nomUtilisateur !== '') {
                if($role==='EMPLOYE'&&$travailleurId>0&&$model->workerAlreadyLinked($travailleurId,$id))throw new RuntimeException('Ce travailleur possède déjà un compte employé actif.');
                if ($action === 'create') {
                    if (strlen($password) < 4) throw new RuntimeException('Le mot de passe doit contenir au moins 4 caractères.');
                    $profil = $this->uploadProfil();
                    if($role==='EMPLOYE'&&$travailleurId<=0)throw new RuntimeException('Sélectionnez le travailleur lié à ce compte.');$matricule = $model->create($nomUtilisateur,$password,$role,$profil??'',$travailleurId?:null);
                    $this->setMessage("Utilisateur créé avec succès. Matricule : {$matricule}");
                } elseif ($action === 'update' && $id > 0) {
                    if ($password !== '' && strlen($password) < 4) throw new RuntimeException('Le mot de passe doit contenir au moins 4 caractères.');
                    $profil = $this->uploadProfil();
                    if($role==='EMPLOYE'&&$travailleurId<=0)throw new RuntimeException('Sélectionnez le travailleur lié à ce compte.');$model->update($id,$nomUtilisateur,$role,$password,$profil,$travailleurId?:null);
                    if ($id === (int) ($_SESSION['user']['id'] ?? 0)) {
                        $_SESSION['user']['nom_utilisateur'] = $nomUtilisateur;
                        if ($profil !== null) $_SESSION['user']['profil'] = $profil;
                    }
                    $this->setMessage('Utilisateur modifié avec succès.');
                }
            } else {
                throw new RuntimeException('Les informations saisies sont invalides.');
            }
        } catch (Throwable $e) {
            $this->setMessage($e instanceof PDOException && $e->getCode() === '23000' ? 'Ce matricule existe déjà.' : $e->getMessage(), 'error');
        }
        $this->redirect('/utilisateurs');
    }

    private function setMessage(string $message, string $type = 'success'): void
    {
        $_SESSION['user_message'] = $message;
        $_SESSION['user_message_type'] = $type;
    }

    private function uploadProfil(): ?string
    {
        $file = $_FILES['profil'] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Le téléversement de la photo a échoué.');
        if ($file['size'] > 2 * 1024 * 1024) throw new RuntimeException('La photo ne doit pas dépasser 2 Mo.');

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) throw new RuntimeException('Formats acceptés : JPG, PNG et WEBP.');

        $directory = ROOT_PATH . '/public/uploads/profiles';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) throw new RuntimeException('Impossible de créer le dossier des profils.');
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) throw new RuntimeException('Impossible d’enregistrer la photo de profil.');
        return 'uploads/profiles/' . $filename;
    }
}
