<?php
/**
 * Classe priv�e de v�rification d'un singleton
 */
class PrivateClass{}
/**
 * Class d'impl�mentation d'un singleton PHP 5.2.x
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .2
 * @package CBi
 * @subpackage application
 */
abstract class Singleton
{
	/**
	 * Tableau contenant les instances des Singletons invoqu�s
	 * @var Array
	 */
	protected static $instances = array();
	
	/**
	 * M�thode de r�cup�ration de l'instance de la classe en cours
	 * @return Object
	 */
	public static function getInstance($pClassName = "")
	{
		if(empty($pClassName))
			return;
		$className = $pClassName;
		if(!isset(self::$instances[$className]))
		{
			self::$instances[$className] = new $className(new PrivateClass());
		}
		return self::$instances[$className];
	}
	
	/**
	 * M�thode de suppression des instances des diff�rents singletons
	 * D�clenche la m�thode __destructor() sur ces instances
	 * @return void
	 */
	public static function dispose()
	{
		self::$instances = array();
	}
	
	/**
	 * Clone
	 * @return void 
	 */
	public function __clone()
	{
		trigger_error("Impossible de cl�ner un object de type Singleton", E_USER_ERROR);
	}
}
?>