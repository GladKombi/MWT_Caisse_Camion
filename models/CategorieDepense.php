<?php
require_once __DIR__ . '/../core/Model.php';

class CategorieDepense extends Model
{
    public function getAll():array
    {
        $sql="SELECT c.*,COUNT(d.id) nombre_depenses FROM categories_depenses c LEFT JOIN depenses d ON d.categorie_depense_id=c.id GROUP BY c.id ORDER BY c.actif DESC,c.nom";
        return $this->db->query($sql)->fetchAll();
    }
    public function create(string$nom,string$type):void{$this->db->prepare('INSERT INTO categories_depenses(nom,type_depense,actif)VALUES(?,?,1)')->execute([$nom,$type]);}
    public function update(int$id,string$nom,string$type,int$actif):void{$this->db->prepare('UPDATE categories_depenses SET nom=?,type_depense=?,actif=? WHERE id=?')->execute([$nom,$type,$actif,$id]);}
    public function toggle(int$id):void{$this->db->prepare('UPDATE categories_depenses SET actif=IF(actif=1,0,1) WHERE id=?')->execute([$id]);}
}
