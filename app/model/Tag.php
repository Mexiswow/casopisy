<?php

namespace Casopisy;

use Nette;
use \dibi;

class Tag extends Entity {

    /**
     * @return Obsah[]
     */
    function getObsah() {
        $result = array();
        $stranky = dibi::query("
            SELECT o.* -- TODO, c.*
            FROM tag t
            LEFT JOIN obsah o USING(cislo_id, strana)
            LEFT JOIN cislo c ON t.cislo_id = c.id
            WHERE tag = %s", $this->tag, " 
              AND casopis_id=%i", $this->casopis_id, " 
              %if",!CasopisModel::$showUnpublished," AND verejne = 1 %end
            ORDER BY rocnik DESC, cislo");
        
        foreach($stranky as $r){
            $result[] = new Obsah($r);
            //todo cache c.*
        }
        return $result;
    }

    function getCasopis() {
        return CasopisModel::getCasopisById($this->casopis_id);
    }
    
    function __toString() {
        return $this->tag;
    }
    
}
