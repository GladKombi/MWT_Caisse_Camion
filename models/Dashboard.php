<?php
require_once __DIR__ . '/../core/Model.php';

class Dashboard extends Model
{
    public function getStats(): array
    {
        return [
            'vehicules' => (int) $this->db->query("SELECT COUNT(*) FROM vehicules WHERE statut_supprime = 0")->fetchColumn(),
            'vehicules_actifs' => (int) $this->db->query("SELECT COUNT(*) FROM vehicules WHERE statut_supprime = 0 AND statut = 'ACTIF'")->fetchColumn(),
            'travailleurs' => (int) $this->db->query("SELECT COUNT(*) FROM travailleurs WHERE statut != 'SUPPRIME'")->fetchColumn(),
            'affectations' => (int) $this->db->query("SELECT COUNT(*) FROM affectations_vehicule WHERE statut_supprime = 0 AND (date_fin IS NULL OR date_fin >= CURDATE())")->fetchColumn(),
            'recettes' => $this->totauxParDevise('recettes'),
            'depenses' => $this->totauxParDevise('depenses'),
        ];
    }

    public function getEvolution(int $jours = 7): array
    {
        $sql = "SELECT dates.jour, SUM(CASE WHEN dates.type_operation = 'RECETTE' THEN dates.montant ELSE 0 END) recettes,
                       SUM(CASE WHEN dates.type_operation = 'DEPENSE' THEN dates.montant ELSE 0 END) depenses
                FROM (SELECT date_recette jour, montant, 'RECETTE' type_operation FROM recettes WHERE devise = 'USD'
                      UNION ALL SELECT date_depense jour, montant, 'DEPENSE' type_operation FROM depenses WHERE devise = 'USD') dates
                WHERE dates.jour >= DATE_SUB(CURDATE(), INTERVAL :jours DAY) GROUP BY dates.jour ORDER BY dates.jour";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':jours', $jours - 1, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMouvementsRecents(int $limite = 8): array
    {
        $stmt = $this->db->prepare('SELECT type_mouvement, montant, description, created_at FROM mouvements_caisse ORDER BY created_at DESC LIMIT :limite');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function totauxParDevise(string $table): array
    {
        $totaux = ['USD' => 0.0, 'CDF' => 0.0];
        foreach ($this->db->query("SELECT devise, COALESCE(SUM(montant), 0) total FROM {$table} GROUP BY devise")->fetchAll() as $row) {
            $totaux[$row['devise']] = (float) $row['total'];
        }
        return $totaux;
    }
}
