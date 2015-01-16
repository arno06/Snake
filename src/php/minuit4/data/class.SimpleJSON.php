<?php
/**
 * Class SimpleJSON
 * Permet de manipuler des donn�es au format JSON
 * 
 * D�cembre 2009 - version 0.3 :
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
	 * M�thode de chargement et de d�codage d'un fichier JSON
	 * @param String $pFile				Url du fichier Json � charger
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
	 * M�thode de d�codage d'un String en Tableau
	 * @param String $pString				donn�e string � decoder
	 * @return array
	 */
	static public function decode($pString)
	{
		return json_decode($pString,true);
	}
	
	/**
	 * M�thode d'encodage d'un String en Tableau
	 * @param Array $pArray				Tableau � encoder
	 * @return String
	 */
	static public function encode(array $pArray)
	{
		return json_encode(self::parseToNumericEntities($pArray));
	}
	
	/**
	 * M�thode de parsing r�cursif des valeurs d'un tableau dans leur format encod� num�riquement (� ==> &#233;)
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