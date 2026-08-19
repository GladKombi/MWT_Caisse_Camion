<?php
require_once __DIR__ . '/../core/Model.php';

class AffectationVehicule extends Model
{
    public function getAll(): array
    {
        $sql = "SELECT av.*, v.immatriculation, v.marque, v.modele,
                       t.matricule travailleur_matricule, t.nom, t.postnom, t.prenom, t.profil,
                       f.nom fonction_nom
                FROM affectations_vehicule av
                JOIN vehicules v ON v.id = av.vehicule_id
                JOIN attribution_fonctions af ON af.id = av.attribution_id
                JOIN travailleurs t ON t.id = af.travailleur_id
                JOIN fonctions_travailleur f ON f.id = af.fonction_id
                WHERE av.statut_supprime = 0 ORDER BY av.date_debut DESC, av.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getVehicules(): array
    {
        return $this->db->query("SELECT id, immatriculation, marque, modele FROM vehicules WHERE statut_supprime = 0 AND statut != 'VENDU' ORDER BY immatriculation")->fetchAll();
    }

    public function getAttributions(): array
    {
        $sql = "SELECT af.id, t.matricule, t.nom, t.postnom, t.prenom, f.nom fonction_nom
                FROM attribution_fonctions af JOIN travailleurs t ON t.id = af.travailleur_id
                JOIN fonctions_travailleur f ON f.id = af.fonction_id
                WHERE af.statut = 'ACTIF' AND t.statut = 'ACTIF' AND f.statut = 1
                ORDER BY t.nom, t.postnom, t.prenom";
        return $this->db->query($sql)->fetchAll();
    }

    public function hasConflict(int $vehiculeId, int $attributionId, string $debut, ?string $fin, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM affectations_vehicule
                WHERE statut_supprime = 0 AND id != ? AND (vehicule_id = ? OR attribution_id = ?)
                AND date_debut <= COALESCE(?, '9999-12-31')
                AND COALESCE(date_fin, '9999-12-31') >= ?";
        $stmt = $this->db->prepare($sql); $stmt->execute([$excludeId, $vehiculeId, $attributionId, $fin, $debut]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO affectations_vehicule (vehicule_id, attribution_id, date_debut, date_fin, observation) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$data['vehicule_id'],$data['attribution_id'],$data['date_debut'],$data['date_fin']?:null,$data['observation']?:null]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE affectations_vehicule SET vehicule_id = ?, attribution_id = ?, date_debut = ?, date_fin = ?, observation = ? WHERE id = ? AND statut_supprime = 0');
        $stmt->execute([$data['vehicule_id'],$data['attribution_id'],$data['date_debut'],$data['date_fin']?:null,$data['observation']?:null,$id]);
    }

    public function delete(int $id): void
    { $this->db->prepare('UPDATE affectations_vehicule SET statut_supprime = 1 WHERE id = ?')->execute([$id]); }
}
