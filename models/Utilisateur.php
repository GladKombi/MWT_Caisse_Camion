<?php
require_once __DIR__ . '/../core/Model.php';

class Utilisateur extends Model
{
    public function getAll(): array
    {
        return $this->db->query('SELECT id, nom_utilisateur, matricule, role, profil, statut, derniere_connexion, created_at FROM utilisateurs WHERE statut = 0 ORDER BY id DESC')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE id = ? AND statut = 0 LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByMatricule(string $matricule): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM utilisateurs WHERE matricule = ? AND statut = 0 LIMIT 1'
        );
        $stmt->execute([$matricule]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $nomUtilisateur, string $password, string $role, string $profil = ''): string
    {
        $annee = date('Y');
        $prefixe = strtoupper($role);
        $verrou = "matricule_utilisateur_{$prefixe}_{$annee}";
        $lockStmt = $this->db->prepare('SELECT GET_LOCK(?, 5)');
        $lockStmt->execute([$verrou]);
        if ((int) $lockStmt->fetchColumn() !== 1) throw new RuntimeException('Impossible de générer le matricule. Veuillez réessayer.');
        try {
            $stmt = $this->db->prepare('SELECT matricule FROM utilisateurs WHERE matricule LIKE ?');
            $stmt->execute(["{$prefixe}-%/{$annee}"]);
            $dernierNumero = 0;
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $matricule) {
                if (preg_match('/^' . preg_quote($prefixe, '/') . '-(\d+)\/' . $annee . '$/', $matricule, $matches)) $dernierNumero = max($dernierNumero, (int) $matches[1]);
            }
            $matricule = sprintf('%s-%03d/%s', $prefixe, $dernierNumero + 1, $annee);
            $stmt = $this->db->prepare('INSERT INTO utilisateurs (nom_utilisateur, matricule, mot_de_passe, role, profil) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nomUtilisateur, $matricule, password_hash($password, PASSWORD_DEFAULT), $role, $profil]);
            return $matricule;
        } finally {
            $releaseStmt = $this->db->prepare('SELECT RELEASE_LOCK(?)');
            $releaseStmt->execute([$verrou]);
        }
    }

    public function update(int $id, string $nomUtilisateur, string $role, string $password = '', ?string $profil = null): void
    {
        $profileSql = $profil !== null ? ', profil = ?' : '';
        $profileParams = $profil !== null ? [$profil] : [];
        if ($password !== '') {
            $stmt = $this->db->prepare("UPDATE utilisateurs SET nom_utilisateur = ?, role = ?, mot_de_passe = ?{$profileSql} WHERE id = ? AND statut = 0");
            $stmt->execute(array_merge([$nomUtilisateur, $role, password_hash($password, PASSWORD_DEFAULT)], $profileParams, [$id]));
            return;
        }
        $stmt = $this->db->prepare("UPDATE utilisateurs SET nom_utilisateur = ?, role = ?{$profileSql} WHERE id = ? AND statut = 0");
        $stmt->execute(array_merge([$nomUtilisateur, $role], $profileParams, [$id]));
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE utilisateurs SET statut = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
