<?php
/**
 * Class regroupant des méthodes statiques "utilitaires" pour l'encodage
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .2
 * @package CBi
 * @subpackage data
 */
abstract class Encoding
{
	/**
	 * Méthode static d'encodage récursif de valeurs dans leurs valeur numériques (é ==> &#233;)
	 * @param object $pValue
	 * @return object
	 */
	static public function toNumericEntities($pValue)
	{
		$convmap = array(0x80, 0xff, 0, 0xff);
		if(!is_array($pValue))
			return mb_encode_numericentity($pValue, $convmap, "ISO-8859-1");
		foreach($pValue as $key=>$value)
		{
			if(is_array($value))
				$value = self::toNumericEntities($value);
			else
				$value = mb_encode_numericentity($value, $convmap, "ISO-8859-1");
			$pValue[$key] = $value;
		}
		return $pValue;
	}
	
	static public function fromNumericEntities($pValue)
	{
    	$convmap = array(0x80, 0xff, 0, 0xff);
		if(!is_array($pValue))
		{
			$specialChars = array("&#8221;"=>'"',
								"&#8220;"=>'"',
								"&#8222;"=>'"',
								"&#8211;"=>'-',
								"&#8212;"=>'_',
								"&#8216"=>"'",
								"&#8217"=>"'",
								"&#8218"=>"'");
			foreach($specialChars as $k=>$v)
				$pValue = preg_replace("/".$k."/",$v,$pValue);
			return mb_decode_numericentity($pValue, $convmap, "ISO-8859-1");
		}
		foreach($pValue as $key=>$value)
		{
			if(is_array($value))
				$value = self::fromNumericEntities($value);
			else
				$value = mb_decode_numericentity($value, $convmap, "ISO-8859-1");
			$pValue[$key] = $value;
		}
		return $pValue;
	}
	
	/**
	 * Méthode static d'encodage récursif de valeurs dans leurs valeur HTML (é ==> &eacute;)
	 * @param object $pValue
	 * @return object
	 */
	static public function toHTMLEntities($pValue)
	{
		if(!is_array($pValue))
			return htmlentities($pValue);
		foreach($pValue as $key=>$value)
		{
			if(is_array($value))
				$value = self::toHTMLEntities($value);
			else
				$value = htmlentities($value);
			$pValue[$key] = $value;
		}
		return $pValue;
	}
}
?>