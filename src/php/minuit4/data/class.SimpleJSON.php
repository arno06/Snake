<?php
/**
 * Class SimpleJSON
 * Permet de manipuler des données au format JSON
 * 
 * Décembre 2009 - version 0.3 :
 * 			Modification de l'API
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .3
 * @package CBi
 * @subpackage data
 */
abstract class SimpleJSON
{
	/**
	 * Méthode de chargement et de décodage d'un fichier JSON
	 * @param String $pFile				Url du fichier Json à charger
	 * @return array
	 */
	static public function import($pFile)
	{
		try
		{
			$contenu = 	File::read($pFile);
		}
		catch (Exception $e)
		{
			throw new Exception("Impossible de lire le fichier source <b>".$pFile."</b>");
		}
		return self::decode($contenu);
	}
	
	
	/**
	 * Méthode de décodage d'un String en Tableau
	 * @param String $pString				donnée string à decoder
	 * @return array
	 */
	static public function decode($pString)
	{
		return json_decode($pString,true);
	}
	
	/**
	 * Méthode d'encodage d'un String en Tableau
	 * @param Array $pArray				Tableau à encoder
	 * @return String
	 */
	static public function encode(array $pArray)
	{
		return json_encode(self::parseToNumericEntities($pArray));
	}
	
	/**
	 * Méthode de parsing récursif des valeurs d'un tableau dans leur format encodé numériquement (é ==> &#233;)
	 * @param Array $pArray
	 * @return Array
	 */
	static public function parseToNumericEntities(array $pArray)
	{
		$return = array();
		foreach($pArray as $key=>$value)
		{
			$key = Encoding::toNumericEntities($key);
			$value = Encoding::toNumericEntities($value);
			$return[$key] = $value;
		}
		return $return;
	}
}
?>