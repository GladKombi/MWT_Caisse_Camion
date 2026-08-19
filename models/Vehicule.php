<?php
require_once __DIR__ . '/../core/Model.php';

class Vehicule extends Model
{
    public function getAll(): array
    {
        $sql = 'SELECT * FROM vehicules WHERE statut_supprime = 0 ORDER BY id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO vehicules (immatriculation, numero_chassis, marque, modele, annee, couleur, type_vehicule, capacite_passagers, kilometrage_initial, date_acquisition, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->prepare($sql)->execute($this->values($data));
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE vehicules SET immatriculation = ?, numero_chassis = ?, marque = ?, modele = ?, annee = ?, couleur = ?, type_vehicule = ?, capacite_passagers = ?, kilometrage_initial = ?, date_acquisition = ?, statut = ? WHERE id = ? AND statut_supprime = 0';
        $values = $this->values($data);
        $values[] = $id;
        $this->db->prepare($sql)->execute($values);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE vehicules SET statut_supprime = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function values(array $data): array
    {
        return [$data['immatriculation'], $data['numero_chassis'] ?: null, $data['marque'] ?: null,
            $data['modele'] ?: null, $data['annee'] ?: null, $data['couleur'] ?: null,
            $data['type_vehicule'] ?: null, $data['capacite_passagers'] ?: null,
            $data['kilometrage_initial'], $data['date_acquisition'] ?: null, $data['statut']];
    }
}
