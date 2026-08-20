<?php
require_once __DIR__ . '/../core/Model.php';

class Travailleur extends Model
{
    public function getAll(): array
    {
        $sql = "SELECT id,matricule,nom,postnom,prenom,sexe,telephone,email,adresse,date_embauche,profil,statut,created_at,updated_at FROM travailleurs WHERE statut!='SUPPRIME' ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findActiveByMatricule(string$matricule):?array{$s=$this->db->prepare("SELECT * FROM travailleurs WHERE matricule=? AND statut='ACTIF' LIMIT 1");$s->execute([$matricule]);return$s->fetch()?:null;}
    public function logAuthentication(int$id,string$action):void{$s=$this->db->prepare("INSERT INTO journal_audit(utilisateur_id,action,table_concernee,enregistrement_id,adresse_ip,user_agent)VALUES(NULL,?,'travailleurs',?,?,?)");$s->execute([$action,$id,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,1000)]);}

    public function create(array $data): string
    {
        $annee = date('Y'); $lock = "matricule_travailleur_{$annee}";
        $lockStmt = $this->db->prepare('SELECT GET_LOCK(?, 5)'); $lockStmt->execute([$lock]);
        if ((int) $lockStmt->fetchColumn() !== 1) throw new RuntimeException('Impossible de générer le matricule.');
        try {
            $matricule = $this->nextMatricule($annee);
            $sql = 'INSERT INTO travailleurs (matricule, nom, postnom, prenom, sexe, telephone, email, motde_passe, adresse, date_embauche, profil, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $this->db->prepare($sql)->execute([$matricule, $data['nom'], $data['postnom'] ?: null, $data['prenom'] ?: null,
                $data['sexe'] ?: null, $data['telephone'] ?: null, $data['email'] ?: null,password_hash($data['motde_passe'],PASSWORD_DEFAULT),$data['adresse'] ?: null,
                $data['date_embauche'] ?: null, $data['profil'] ?? '', $data['statut']]);
            return $matricule;
        } finally {
            $releaseStmt = $this->db->prepare('SELECT RELEASE_LOCK(?)'); $releaseStmt->execute([$lock]);
        }
    }

    public function update(int $id, array $data): void
    {
        $profileSql = isset($data['profil']) ? ', profil = ?' : '';$passwordSql=$data['motde_passe']!==''?', motde_passe = ?':'';
        $values = [$data['nom'], $data['postnom'] ?: null, $data['prenom'] ?: null, $data['sexe'] ?: null,
            $data['telephone'] ?: null, $data['email'] ?: null, $data['adresse'] ?: null, $data['date_embauche'] ?: null, $data['statut']];
        if (isset($data['profil'])) $values[] = $data['profil'];
        if($data['motde_passe']!=='')$values[]=password_hash($data['motde_passe'],PASSWORD_DEFAULT);
        $values[] = $id;
        $sql = "UPDATE travailleurs SET nom = ?, postnom = ?, prenom = ?, sexe = ?, telephone = ?, email = ?, adresse = ?, date_embauche = ?, statut = ?{$profileSql}{$passwordSql} WHERE id = ? AND statut != 'SUPPRIME'";
        $this->db->prepare($sql)->execute($values);
    }

    public function delete(int $id): void
    {
        $this->db->prepare("UPDATE travailleurs SET statut = 'SUPPRIME' WHERE id = ?")->execute([$id]);
    }

    private function nextMatricule(string $annee): string
    {
        $stmt = $this->db->prepare('SELECT matricule FROM travailleurs WHERE matricule LIKE ?');
        $stmt->execute(["MWTR-{$annee}-%"]); $max = 0;
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $value) if (preg_match('/^MWTR-' . $annee . '-(\d+)$/', $value, $m)) $max = max($max, (int) $m[1]);
        return sprintf('MWTR-%s-%04d', $annee, $max + 1);
    }
}
