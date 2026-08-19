<?php
require_once __DIR__ . '/../core/Model.php';

class AttributionFonction extends Model
{
    public function getAll(): array
    {
        $sql="SELECT af.*,t.matricule,t.nom,t.postnom,t.prenom,t.profil,f.nom fonction_nom
              FROM attribution_fonctions af JOIN travailleurs t ON t.id=af.travailleur_id
              JOIN fonctions_travailleur f ON f.id=af.fonction_id ORDER BY af.id DESC";
        return $this->db->query($sql)->fetchAll();
    }
    public function getTravailleurs(): array
    {
        $sql = "SELECT t.id, t.matricule, t.nom, t.postnom, t.prenom, t.profil
                FROM travailleurs t
                WHERE t.statut = 'ACTIF'
                  AND NOT EXISTS (SELECT 1 FROM attribution_fonctions af
                                  WHERE af.travailleur_id = t.id AND af.statut = 'ACTIF')
                ORDER BY t.nom, t.postnom, t.prenom";
        return $this->db->query($sql)->fetchAll();
    }
    public function getFonctions():array{return $this->db->query("SELECT id,nom FROM fonctions_travailleur WHERE statut=1 ORDER BY nom")->fetchAll();}
    public function exists(int $travailleur,int $fonction,int $exclude=0):bool{$s=$this->db->prepare("SELECT COUNT(*) FROM attribution_fonctions WHERE travailleur_id=? AND fonction_id=? AND statut='ACTIF' AND id!=?");$s->execute([$travailleur,$fonction,$exclude]);return(int)$s->fetchColumn()>0;}
    public function create(array $d):void{$this->db->prepare('INSERT INTO attribution_fonctions(travailleur_id,fonction_id,taux_remuneration,statut)VALUES(?,?,?,?)')->execute([$d['travailleur_id'],$d['fonction_id'],$d['taux_remuneration'],$d['statut']]);}
    public function update(int $id,array $d):void{$this->db->prepare('UPDATE attribution_fonctions SET travailleur_id=?,fonction_id=?,taux_remuneration=?,statut=? WHERE id=?')->execute([$d['travailleur_id'],$d['fonction_id'],$d['taux_remuneration'],$d['statut'],$id]);}
    public function suspend(int $id):void{$this->db->prepare("UPDATE attribution_fonctions SET statut='SUSPENDU' WHERE id=?")->execute([$id]);}
}
