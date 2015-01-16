<?php
//todo affiner la suppression d'un cookie
/**
 * Class Cookie Permet une gestion simple des cookie
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .1
 * @package CBi
 * @subpackage utils
 */
class Cookie
{
	/**
	 * Méthode de définition d'un nouveau cookie
	 * @param string $pId
	 * @param string $pValue
	 * @param string $pTime
	 * @return void
	 */
    static public function set($pId, $pValue, $pTime = "default")
    {
        $ids = explode(".", $pId);
        $t = "";
        $string = "";
        for($i = 0, $max = count($ids); $i<$max;$i++)
        {
	        $string .= "[".$ids[$i]."]";
            $t.= ($i>0?"[":"").$ids[$i].($i>0?"]":"");
        }
        if($pTime == "default")
            $pTime = time() + 3600;
        eval('$_COOKIE'.$string.'="'.$pValue.'";');
        setcookie($t, $pValue, $pTime);
    }

	/**
	 * Méthode de récupération de la valeur d'un cookie
	 * Renvoie false si inexistant
	 * @param  string $pId
	 * @return string|bool
	 */
    static public function get($pId)
    {
        $ids = explode(".", $pId);
        $d = &$_COOKIE;
        for($i = 0, $max = count($ids); $i<$max;$i++)
        {
            if(!isset($d[$ids[$i]]))
                return false;
            $d = &$d[$ids[$i]];
        }
        return $d;
    }

	/**
	 * Méthode de suppression d'un cookie
	 * @param string $pId
	 * @return void
	 */
    static public function delete($pId)
    {
        $ids = explode(".", $pId);
        $value = self::get($pId);
        $string = "";
        for($i = 0, $max = count($ids); $i<$max;$i++)
            $string .= "[".$ids[$i]."]";
        if($value===false)
            return;
        self::set($pId, $value, time()-3600);
        eval('unset($_COOKIE'.$string.');');
    }
}
