<?php
require_once __DIR__ . '/../core/Model.php';

class Utilisateur extends Model
{
    public function getAll(): array
    {
        return $this->db->query("SELECT u.id,u.travailleur_id,u.nom_utilisateur,u.matricule,u.role,u.profil,u.statut,u.derniere_connexion,u.created_at,TRIM(CONCAT_WS(' ',t.nom,t.postnom,t.prenom)) travailleur_nom FROM utilisateurs u LEFT JOIN travailleurs t ON t.id=u.travailleur_id WHERE u.statut=0 ORDER BY u.id DESC")->fetchAll();
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

    public function create(string $nomUtilisateur, string $password, string $role, string $profil = '', ?int $travailleurId=null): string
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
            $stmt = $this->db->prepare('INSERT INTO utilisateurs (travailleur_id,nom_utilisateur,matricule,mot_de_passe,role,profil) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$travailleurId,$nomUtilisateur,$matricule,password_hash($password,PASSWORD_DEFAULT),$role,$profil]);
            return $matricule;
        } finally {
            $releaseStmt = $this->db->prepare('SELECT RELEASE_LOCK(?)');
            $releaseStmt->execute([$verrou]);
        }
    }

    public function update(int $id, string $nomUtilisateur, string $role, string $password = '', ?string $profil = null, ?int $travailleurId=null): void
    {
        $profileSql = $profil !== null ? ', profil = ?' : '';
        $profileParams = $profil !== null ? [$profil] : [];
        if ($password !== '') {
            $stmt = $this->db->prepare("UPDATE utilisateurs SET nom_utilisateur=?,role=?,travailleur_id=?,mot_de_passe=?{$profileSql} WHERE id=? AND statut=0");
            $stmt->execute(array_merge([$nomUtilisateur,$role,$travailleurId,password_hash($password,PASSWORD_DEFAULT)],$profileParams,[$id]));
            return;
        }
        $stmt = $this->db->prepare("UPDATE utilisateurs SET nom_utilisateur=?,role=?,travailleur_id=?{$profileSql} WHERE id=? AND statut=0");
        $stmt->execute(array_merge([$nomUtilisateur,$role,$travailleurId],$profileParams,[$id]));
    }

    public function getAvailableWorkers():array{return$this->db->query("SELECT t.id,t.matricule,TRIM(CONCAT_WS(' ',t.nom,t.postnom,t.prenom)) nom FROM travailleurs t WHERE t.statut='ACTIF' ORDER BY t.nom,t.postnom,t.prenom")->fetchAll();}
    public function workerAlreadyLinked(int$workerId,int$excludeId=0):bool{$s=$this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE travailleur_id=? AND statut=0 AND id<>?");$s->execute([$workerId,$excludeId]);return(int)$s->fetchColumn()>0;}
    public function markLogin(int$id):void{$s=$this->db->prepare('UPDATE utilisateurs SET derniere_connexion=NOW() WHERE id=?');$s->execute([$id]);}
    public function logAuthentication(?int$userId,string$action):void
    {
        $s=$this->db->prepare("INSERT INTO journal_audit(utilisateur_id,action,table_concernee,enregistrement_id,adresse_ip,user_agent)VALUES(?,?,'utilisateurs',?,?,?)");$s->execute([$userId,$action,$userId,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,1000)]);
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE utilisateurs SET statut = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
