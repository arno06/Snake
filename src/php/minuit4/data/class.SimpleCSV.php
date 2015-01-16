<?php
/**
 * Class de gestion des fichiers CSV
 * 
 * D�cembre 2009 - version 0.2 :
 * 			Modification de l'API
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .2
 * @package CBi
 * @subpackage data
 */
abstract class SimpleCSV
{
	/**
	 * Caract�re de s�paration des champs
	 * @var String
	 */
	const SEPARATOR = ";";
	
	/**
	 * M�thode de conversion de donn�es au format Tableau en chaine de caract�res format�e en CSV
	 * @param Array $pData				Donn�e � convertir
	 * @return String
	 */
	static public function encode(array $pData)
	{
		if(!$pData)
			return;
		$donnees = "";
		foreach($pData[0] as $champs=>$value)
		{
			if(!empty($donnees))
				$donnees .= self::SEPARATOR.$champs;
			else
				$donnees .= $champs;
		}
		for($i=0,$max = count($pData); $i<$max; $i++)
		{
			$ligne = "";
			foreach($pData[$i] as $champs=>$value)
				$ligne .= $ligne?self::SEPARATOR.$value:$value;
			$donnees .= "\r\n".$ligne;
		}
		return $donnees;
	}
	
	/**
	 * M�thode de conversion d'une chaine de caract�res format�e en CSV vers un Tableau
	 * @param String $pString				Chaine � convertir
	 * @return Array
	 */
	static public function decode($pString)
	{
		$return = array();
		$dataArray = explode("\r\n",$pString);
		$champs = explode(self::SEPARATOR, $dataArray[0]);
		unset($dataArray[0]);
		$max = count($dataArray);
		for($i = 1; $i <= $max; $i++)
		{
			if($dataArray[$i]=="")
				continue;
			$temp = explode(self::SEPARATOR, $dataArray[$i]);
			$new = array();
			$maxChamps = count($champs);
			for($j = 0; $j<$maxChamps; $j++)
				$new[$champs[$j]] = $temp[$j];
			$return[] = $new;
		}
		return $return;
	}
	
	/**
	 * M�thode d'exportation de donn�es provenant de la base vers un fichier CSV
	 * Renvoie le r�sultat de l'�criture du fichier
	 * @param Array $pData					Tableau des donn�es
	 * @param String $pFileName				Nom du fichier
	 * @return Boolean
	 */
	static public function export(array $pData, $pFileName)
	{
		if(!$pData)
			return;
		$donnees = self::encode($pData);
		File::delete($pFileName);
		File::create($pFileName);
		return File::append($pFileName, $donnees);
	}
	
	/**
	 * M�thode d'import de donn�es � partir d'un fichier CSV
	 * @param String $pFileName				Nom du fichier
	 * @return Array
	 */
	static public function import($pFileName)
	{
		try
		{
			$dataString = File::read($pFileName);
		}
		catch (Exception $e)
		{
			return;
		}
		return self::decode($dataString);
	}
}
?>