<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Travailleur.php';

class TravailleurController extends Controller
{
    public function index(): void
    {
        $model = new Travailleur();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { $this->handlePost($model); return; }
        $travailleurs = $model->getAll();
        $message = $_SESSION['worker_message'] ?? null; $messageType = $_SESSION['worker_message_type'] ?? 'success';
        unset($_SESSION['worker_message'], $_SESSION['worker_message_type']);
        $this->view('travailleurs/index', compact('travailleurs', 'message', 'messageType'));
    }

    private function handlePost(Travailleur $model): void
    {
        $action = $_POST['action'] ?? ''; $id = (int) ($_POST['id'] ?? 0);
        try {
            if ($action === 'delete' && $id > 0) { $model->delete($id); $this->message('Travailleur supprimé avec succès.'); }
            else {
                $data = $this->validatedData(); $profil = $this->uploadProfil();
                if ($profil !== null) $data['profil'] = $profil;
                if ($action === 'create') { $data['profil'] ??= ''; $matricule = $model->create($data); $this->message("Travailleur ajouté. Matricule : {$matricule}"); }
                elseif ($action === 'update' && $id > 0) { $model->update($id, $data); $this->message('Travailleur modifié avec succès.'); }
                else throw new RuntimeException('Action invalide.');
            }
        } catch (Throwable $e) {
            $message = $e instanceof PDOException && $e->getCode() === '23000' ? 'Cet email est déjà utilisé.' : $e->getMessage();
            $this->message($message, 'error');
        }
        $this->redirect('/travailleurs');
    }

    private function validatedData(): array
    {
        $nom = trim($_POST['nom'] ?? ''); $sexe = $_POST['sexe'] ?? ''; $statut = $_POST['statut'] ?? 'ACTIF';
        if ($nom === '') throw new RuntimeException('Le nom est obligatoire.');
        if ($sexe !== '' && !in_array($sexe, ['M','F'], true)) throw new RuntimeException('Le sexe est invalide.');
        if (!in_array($statut, ['ACTIF','SUSPENDU','LICENCIE'], true)) throw new RuntimeException('Le statut est invalide.');
        $email = trim($_POST['email'] ?? ''); if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('L’adresse email est invalide.');
        $password=$_POST['motde_passe']??'';if(($_POST['action']??'')==='create'&&strlen($password)<4)throw new RuntimeException('Le mot de passe doit contenir au moins 4 caractères.');if($password!==''&&strlen($password)<4)throw new RuntimeException('Le mot de passe doit contenir au moins 4 caractères.');
        return ['nom'=>$nom,'postnom'=>trim($_POST['postnom']??''),'prenom'=>trim($_POST['prenom']??''),'sexe'=>$sexe,
            'telephone'=>trim($_POST['telephone']??''),'email'=>$email,'adresse'=>trim($_POST['adresse']??''),
            'date_embauche'=>trim($_POST['date_embauche']??''),'statut'=>$statut,'motde_passe'=>$password];
    }

    private function uploadProfil(): ?string
    {
        $file = $_FILES['profil'] ?? null; if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Le téléversement de la photo a échoué.');
        if ($file['size'] > 2 * 1024 * 1024) throw new RuntimeException('La photo ne doit pas dépasser 2 Mo.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']); $types = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!isset($types[$mime])) throw new RuntimeException('Formats acceptés : JPG, PNG et WEBP.');
        $directory = ROOT_PATH . '/public/uploads/workers';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) throw new RuntimeException('Impossible de créer le dossier des photos.');
        $filename = bin2hex(random_bytes(16)) . '.' . $types[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) throw new RuntimeException('Impossible d’enregistrer la photo.');
        return 'uploads/workers/' . $filename;
    }

    private function message(string $message, string $type = 'success'): void
    { $_SESSION['worker_message'] = $message; $_SESSION['worker_message_type'] = $type; }
}
