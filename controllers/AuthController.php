<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Utilisateur.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = trim($_POST['matricule'] ?? '');
            $password = $_POST['password'] ?? '';

            $model = new Utilisateur();
            $user = $model->findByMatricule($matricule);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nom_utilisateur' => $user['nom_utilisateur'],
                    'profil' => $user['profil'],
                    'matricule' => $user['matricule'],
                    'role' => $user['role'],
                ];

                $this->redirect('/dashboard');
            }

            $error = 'Matricule ou mot de passe incorrect.';
            $this->view('auth/login', compact('error'));
            return;
        }

        $this->view('auth/login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
