<?php
/**
 * Class File
 * Surcouche aux fonctions Php permettant de g�rer les fichiers
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .3
 * @package CBi
 * @subpackage system
 */
abstract class File
{
	
	/**
	 * M�thode de cr�ation d'un nouveau fichier sur le serveur
	 * Renvoi le r�sultat du traitement - False si le fichier existe d�j�
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
	 * M�thode de r�cup�ration d'une ressource apr�s ouverture d'un fichier non binaire
	 * @param String $pFile					Chemin du fichier
	 * @param String $pMode					Mode d'ouverture, par d�fault "r" pour "read" lecture
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
	 * M�thode de r�cup�ration du contenu d'un fichier non binaire
	 * D�clenche une Exception en cas d'�chec
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
	 * M�thode d'�criture � la suite d'un fichier existant
	 * @param String $pFile					Chemin du fichier
	 * @param String $pValue				Valeur � �crire
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
	 * M�thode de suppression d'un fichier
	 * Renvoi le r�sultat de l'action, false si le fichier n'existe pas
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
	 * M�thode de renommage d'un fichier/dossier
	 * Renvoi le r�sultat de l'action, false si le fichier n'existe pas
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
	 * M�thode d'�chappement des caract�res pouvant poser probl�me dans certains syst�mes de fichiers
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
						"�"=>"a",
						"�"=>"a",
						"�"=>"a",
						"�"=>"e",
						"�"=>"e",
						"�"=>"e",
						"�"=>"e",
						"�"=>"i",
						"�"=>"i",
						"�"=>"u",
						"�"=>"u",
						"�"=>"o",
						"�"=>"o",
						"�"=>"o",
						"�"=>"y");
		foreach($chars as $key=>$change)
			$pFileName = str_replace($key, $change, $pFileName);
		return $pFileName;
	}
	
	
	/**
	 * M�thode de r�cup�ration d'une extension d'un fichier � partir du nom de ce m�me fichier
	 * @param String $pFile		Nom du fichier - peut �tre le chemin relatif ou absolu de celui-ci
	 * @return String(2,3)
	 */
	static public function getExtension($pFile)
	{
		preg_match("/\.([a-z]{2,3})$/", $pFile, $extracts);
		return $extracts[1];
	}
	
	/**
	 * M�thode de r�cup�ration du MimType d'un fichier � partir de son nom
	 * @param object $pFile		Nom du fichier - peut �tre le chemin relatif ou absolu de celui-ci
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
     * M�thode permettant de forcer le t�l�chargement d'un fichier ou un contenu via un fichier temporaire
     * Quitte l'applicatif - aucune sortie HTML g�n�r�e
     * @param String 	$pFile		emplacement du fichier � t�l�charger
     * @param String	$pSource	contenu du fichier - peut �tre du contenu JSON, CSV, XML...
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