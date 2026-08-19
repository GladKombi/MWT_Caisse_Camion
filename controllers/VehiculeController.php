<?php
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Vehicule.php';

class VehiculeController extends Controller
{
    public function index(): void
    {
        $model = new Vehicule();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($model);
            return;
        }

        $vehicules = $model->getAll();
        $message = $_SESSION['vehicle_message'] ?? null;
        $messageType = $_SESSION['vehicle_message_type'] ?? 'success';
        unset($_SESSION['vehicle_message'], $_SESSION['vehicle_message_type']);
        $this->view('vehicules/index', compact('vehicules', 'message', 'messageType'));
    }

    private function handlePost(Vehicule $model): void
    {
        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);
        try {
            if ($action === 'delete' && $id > 0) {
                $model->delete($id);
                $this->message('Véhicule supprimé avec succès.');
            } else {
                $data = $this->validatedData();
                if ($action === 'create') {
                    $model->create($data);
                    $this->message('Véhicule ajouté avec succès.');
                } elseif ($action === 'update' && $id > 0) {
                    $model->update($id, $data);
                    $this->message('Véhicule modifié avec succès.');
                } else throw new RuntimeException('Action invalide.');
            }
        } catch (Throwable $e) {
            $message = $e instanceof PDOException && $e->getCode() === '23000' ? 'Cette immatriculation ou ce numéro de châssis existe déjà.' : $e->getMessage();
            $this->message($message, 'error');
        }
        $this->redirect('/vehicules');
    }

    private function validatedData(): array
    {
        $immatriculation = strtoupper(trim($_POST['immatriculation'] ?? ''));
        $statut = strtoupper($_POST['statut'] ?? 'ACTIF');
        $annee = trim($_POST['annee'] ?? '');
        if ($immatriculation === '') throw new RuntimeException('L’immatriculation est obligatoire.');
        if (!in_array($statut, ['ACTIF', 'EN_MAINTENANCE', 'HORS_SERVICE', 'VENDU'], true)) throw new RuntimeException('Le statut est invalide.');
        if ($annee !== '' && ((int) $annee < 1900 || (int) $annee > (int) date('Y') + 1)) throw new RuntimeException('L’année du véhicule est invalide.');
        return [
            'immatriculation' => $immatriculation,
            'numero_chassis' => strtoupper(trim($_POST['numero_chassis'] ?? '')),
            'marque' => trim($_POST['marque'] ?? ''), 'modele' => trim($_POST['modele'] ?? ''),
            'annee' => $annee, 'couleur' => trim($_POST['couleur'] ?? ''),
            'type_vehicule' => trim($_POST['type_vehicule'] ?? ''),
            'capacite_passagers' => max(0, (int) ($_POST['capacite_passagers'] ?? 0)),
            'kilometrage_initial' => max(0, (float) ($_POST['kilometrage_initial'] ?? 0)),
            'date_acquisition' => trim($_POST['date_acquisition'] ?? ''), 'statut' => $statut,
        ];
    }

    private function message(string $message, string $type = 'success'): void
    {
        $_SESSION['vehicle_message'] = $message;
        $_SESSION['vehicle_message_type'] = $type;
    }
}
