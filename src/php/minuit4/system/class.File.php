<?php
/**
 * Class File
 * Surcouche aux fonctions Php permettant de gérer les fichiers
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .3
 * @package CBi
 * @subpackage system
 */
abstract class File
{
	
	/**
	 * Méthode de création d'un nouveau fichier sur le serveur
	 * Renvoi le résultat du traitement - False si le fichier existe déjà
	 * @param String $pFile					chemin du fichier
	 * @return boolean
	 */
	static public function create($pFile)
	{
		if(file_exists($pFile))
			return;
		else
			return fclose(fopen($pFile,'x'));
	}
	
	/**
	 * Méthode de récupération d'une ressource après ouverture d'un fichier non binaire
	 * @param String $pFile					Chemin du fichier
	 * @param String $pMode					Mode d'ouverture, par défault "r" pour "read" lecture
	 * @return resource
	 */
	static protected function open($pFile, $pMode = "r")
	{
		if(file_exists($pFile))
			return fopen($pFile, $pMode);
		else
			return;
	}
	
	/**
	 * Méthode de récupération du contenu d'un fichier non binaire
	 * Déclenche une Exception en cas d'échec
	 * @param String $pPath					Chemin du fichier
	 * @return String
	 */
	static public function read($pPath)
	{
		if($ressource = self::open($pPath))
			return fread($ressource, filesize($pPath));
		else
			throw new Exception("Le fichier n'existe pas.");
	}
	
	/**
	 * Méthode d'écriture à la suite d'un fichier existant
	 * @param String $pFile					Chemin du fichier
	 * @param String $pValue				Valeur à écrire
	 * @return Ressource
	 */
	static public function append($pFile, $pValue)
	{
		$r = self::open($pFile, "a");
		$return = @fwrite($r, $pValue);
		@fclose($r);
		return $return;
	}
	
	/**
	 * Méthode de suppression d'un fichier
	 * Renvoi le résultat de l'action, false si le fichier n'existe pas
	 * @param String $pFile					Chemin du fichier
	 * @return boolean
	 */
	static public function delete($pFile)
	{
		if(file_exists($pFile))
			return @unlink($pFile);
		else
			return;
	}
	
	/**
	 * Méthode de renommage d'un fichier/dossier
	 * Renvoi le résultat de l'action, false si le fichier n'existe pas
	 * @param String $pFile					Chemin actuel
	 * @param String $pNewName				Nouveau Chemin
	 * @return boolean
	 */
	static public function rename($pFile, $pNewName)
	{
		if(file_exists($pFile))
			return @rename($pFile, $pNewName);
		else
			return;
	}
	
	/**
	 * Méthode d'échappement des caractères pouvant poser problème dans certains systèmes de fichiers
	 * @param String $pFileName				Nom du fichier
	 * @return String
	 */
	static public function sanitizeFileName($pFileName)
	{
		$pFileName = strtolower($pFileName);
		$chars = array(" "=>"-",
						"@"=>"at",
						"\\"=>"-",
						"/"=>"-",
						"â"=>"a",
						"à"=>"a",
						"ä"=>"a",
						"é"=>"e",
						"è"=>"e",
						"ê"=>"e",
						"ë"=>"e",
						"ï"=>"i",
						"ì"=>"i",
						"ù"=>"u",
						"ü"=>"u",
						"ô"=>"o",
						"ò"=>"o",
						"ö"=>"o",
						"ÿ"=>"y");
		foreach($chars as $key=>$change)
			$pFileName = str_replace($key, $change, $pFileName);
		return $pFileName;
	}
	
	
	/**
	 * Méthode de récupération d'une extension d'un fichier à partir du nom de ce même fichier
	 * @param String $pFile		Nom du fichier - peut être le chemin relatif ou absolu de celui-ci
	 * @return String(2,3)
	 */
	static public function getExtension($pFile)
	{
		preg_match("/\.([a-z]{2,3})$/", $pFile, $extracts);
		return $extracts[1];
	}
	
	/**
	 * Méthode de récupération du MimType d'un fichier à partir de son nom
	 * @param object $pFile		Nom du fichier - peut être le chemin relatif ou absolu de celui-ci
	 * @return String
	 */
	static public function getMimeType($pFile)
	{
		$extension = self::getExtension($pFile);
		switch($extension)
		{
			case "gz": 
				$type = "application/x-gzip"; break;
			case "tgz": 
				$type = "application/x-gzip"; break;
			case "zip": 
				$type = "application/zip"; break;
			case "rar": 
				$type = "application/rar"; break;
			case "pdf": 
				$type = "application/pdf"; break;
			case "png": 
				$type = "image/png"; break;
			case "gif": 
				$type = "image/gif"; break;
			case "jpg": 
				$type = "image/jpeg"; break;
			case "txt": 
				$type = "text/plain"; break;
			case "csv":
				$type = "text/csv"; break;
			default: 
				$type = "application/octet-stream"; break;
		}
		return $type;
	}
	
	
    /**
     * Méthode permettant de forcer le téléchargement d'un fichier ou un contenu via un fichier temporaire
     * Quitte l'applicatif - aucune sortie HTML générée
     * @param String 	$pFile		emplacement du fichier à télécharger
     * @param String	$pSource	contenu du fichier - peut être du contenu JSON, CSV, XML...
     * @return void
     */
	static public function download($pFile, $pSource = "")
	{
		if(empty($pFile))
			return;
		$fromSource = !empty($pSource);
		if(!$fromSource)
			$length = filesize($pFile);
		else
			$length = strlen($pSource);
        header("content-disposition: attachment; filename=\"".basename($pFile)."\"");
        header('Content-Type: application/force-download');
        header('Content-Transfer-Encoding: binary');
        header("Content-Length: ".$length);
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
        header("Expires: 0");
		if(!$fromSource)
        	readfile($pFile);
		else
			echo $pSource;
        exit();
	}
}
?>