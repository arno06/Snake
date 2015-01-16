<?php
/**
 * Class Folder
 * Surcouche aux fonctions Php permettant de g�rer les dossiers
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .2
 * @package CBi
 * @subpackage system
 */
abstract class Folder
{
	/**
	 * M�thode permettant de lister les dossiers et les fichiers contenus dans un dossier pass� en param�tre
	 * Renvoi un tableau multidimensionnel sous la forme 
	 * @param String $pFolder					Chemin du dossier � lire
	 * @param Boolean $pRecursive				Indique si la lecture du dossier se fait de fa�on r�curcive
	 * @return array
	 */
	static public function read($pFolder,$pRecursive = true)
	{
		$return = array();
		$dossier = opendir($pFolder);
		while ($file = readdir($dossier))
		{
			if ($file != "." && $file != "..")
			{
			  	$data = array();
			    $f = $pFolder."/".$file;
			    if(!is_file($f)&&$pRecursive)
					$data = self::read($f);
				$return[$file]= array("data"=>$data,"path"=>$f);
			}
		}
		closedir($dossier);	
		return $return;
	}
	
	/**
	 * M�thode de cr�ation d'un nouveau dossier
	 * Renvoi le r�sultat du traitement
	 * @param String $pPath					Chemin du dossier � cr�er
	 * @param Number $pMode					CHMod du dossier souhait�
	 * @return Boolean
	 */
	static public function create($pPath, $pMode = 0777)
	{
		if(file_exists($pPath))
			return chmod($pPath, $pMode);
		else
			return @mkdir($pPath, $pMode);
	}
	
	/**
	 * M�thode de destruction d'un dossier et de tout son contenu <!!!>
	 * Renvoi le r�sultat du traitement
	 * @param String $pPath					Chemin du dossier � supprimer
	 * @return Boolean
	 */
	static public function deleteRecursive($pPath)
	{
		if (!is_dir($pPath))
			return false;
			
		chmod( $pPath, 0777);
	    $files = glob( $pPath . '*', GLOB_MARK );
	    foreach( $files as $file ){
	        if( substr( $file, -1 ) == '/')
			{
	            self::deleteRecursive( $file );
			}
	        else
			{
				chmod( $file, 0777);
	            File::delete($file);
			}
	    }	   
	    return rmdir( $pPath );	   
 	}
}
?>