<?php
/**
 * Classe privée de vérification d'un singleton
 */
class PrivateClass{}
/**
 * Class d'implémentation d'un singleton PHP 5.2.x
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .2
 * @package CBi
 * @subpackage application
 */
abstract class Singleton
{
	/**
	 * Tableau contenant les instances des Singletons invoqués
	 * @var Array
	 */
	protected static $instances = array();
	
	/**
	 * Méthode de récupération de l'instance de la classe en cours
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
	 * Méthode de suppression des instances des différents singletons
	 * Déclenche la méthode __destructor() sur ces instances
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
		trigger_error("Impossible de clôner un object de type Singleton", E_USER_ERROR);
	}
}
?>