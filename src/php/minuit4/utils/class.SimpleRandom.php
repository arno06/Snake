<?php
/**
 * Class SimpleRandom
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .1
 * @package CBi
 * @subpackage utils
 */
abstract class SimpleRandom
{
	
	/**
	 * Méthode static de génération d'une chaine de caractères aléatoires (majuscule, minuscule, chiffre)
	 * @param Number $pLength		Longueur souhaitée de la chaine
	 * @return String
	 */
	static public function string($pLength)
	{
		if(!is_numeric($pLength))
			return false;
		$chars = array_merge(range("A", "Z"), range(0,9));
		$chars = array_merge($chars, range("a","z"));
		$maxChars = count($chars);
		$string = "";
		$i = 0;
		for(;$i<$pLength;++$i)
			$string .= $chars[rand(0, $maxChars-1)];
		return $string;
	}
}