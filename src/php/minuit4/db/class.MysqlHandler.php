<?php
/**
 * Couche d'abstraction à la base de données (type mysql)
 *
 * 
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .7
 * @package minuit4
 * @subpackage db
 */
class MysqlHandler extends Singleton
{
	
	/**
	 * Tableau de l'ensemble des requêtes effectuées
	 * @var Array
	 */
	static $queries = array();
	
	
	/**
	 * Chemin d'accès à la base de données
	 * @var String
	 */
	protected $host;
	
	
	/**
	 * Nom d'utilisateur
	 * @var String
	 */
	protected $user;
	
	
	/**
	 * Mot de passe d'accès à la base de données
	 * @var String
	 */
	protected $mdp;
	
	/**
	 * Nom de la base de données
	 * @var String
	 */
	protected $bdd;
	
	
	/**
	 * Ressource de connexion à la base de données SQL
	 * @var resource
	 */
	public $connexion;
	
	
	/**
	 * @var int
	 */
	public $lastId;
	
	
	/**
	 * Constructor
	 * Effectue la connexion avec la base
	 * @return void
	 */
	public function __construct($pInstance)
	{
		if(!$pInstance instanceOf PrivateClass)
			trigger_error("Il est interdit d'instancier un objet de type <i>Singleton</i> - Merci d'utiliser la méthode static <i>".__CLASS__."::getInstance()</i>", E_USER_ERROR);
		$this->host = DB_HOST;
		$this->user = DB_USER;
		$this->mdp = DB_PWD;
		$this->bdd = DB_NAME;
		$this->connect();
	}
	
	
	/**
	 * Destructor
	 * Clos la connexion en cours avec la base
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
	}
	
	
	/**
	 * Méthode de connexion à la base
	 * Stop l'exécution de l'application si la base n'est pas accessible
	 * @return void
	 */
	protected function connect()
	{
		if(!$this->connexion = mysql_connect($this->host, $this->user, $this->mdp))
			trigger_error("Connexion au serveur de gestion de base de données impossible", E_USER_ERROR);
		if(!@mysql_select_db($this->bdd, $this->connexion))
			trigger_error("Impossible de trouver la base de données demandée", E_USER_ERROR);
	}	
	
	
	/**
	 * Méthode permettant de récupérer les donnée d'une requêtes SQL
	 * Renvoie les données renvoyées sous forme d'un tableau associatif
	 * @param String $pQuery				Requête SQL brute
	 * @return array
	 */
	public function fromQuery($pQuery)
	{
		$result = $this->make($pQuery);
		if(!$result)
			trigger_error("Une erreur est apparue lors de la requête <b>".$pQuery."</b>", E_USER_ERROR);
		$return = array();
		while($donnee = @mysql_fetch_assoc($result))
		{
			array_push($return, $donnee);
		}
		return $return;
	}	
	
	
	/**
	 * Méthode de récupération de la clé primaire générée à la suite d'une insertion
	 * @return Number
	 */
	public function getInsertId()
	{
		return mysql_insert_id($this->connexion);
	}
	
	
	/**
	* Méthode permettant de clore la connexion établie avec la base de donnée
	* @return void
	**/
	protected function close()
	{
		@mysql_close($this->connexion);
	}
	
	
	/**
	 * Méthode permettant de centraliser les commandes à effectuer avant l'excécution d'une requête
	 * @param String $pQuery				Requête à excécuter
	 * @return resource
	 */
	public function make($pQuery)
	{
		array_push(self::$queries,$pQuery);
		return mysql_query($pQuery, $this->connexion);
	}
	
	
	/**
	 * Méthode permettant de filtrer une valeur avant son utilisation dans une requête à la base de données
	 * @param String $pValue				Valeur à filtrer
	 * @return String
	 */
	public function filterIn($pValue)
	{
		if(is_array($pValue))
		{
			foreach($pValue as $key=>$value)
				$pValue[$key] = $this->filterIn($value);
			return $pValue;
		}
		else
			return mysql_real_escape_string($pValue, $this->connexion);
	}
	
	
	/**
	 * Méthode permettant de filtrer les données lorsqu'on les récupère via une requête à la base de données
	 * @param String $pValue				Valeur à filtrer
	 * @return String
	 */
	static public function filterOut($pValue)
	{
		return stripcslashes($pValue);
	}
	
	
	/**
	 * Méthode de conversion d'une date provenant de la base de données en tableau associatif
	 * @param String $pDate				Date à convertir
	 * @return array
	 */
	static public function convertDateFrom($pDate)
	{
		$dateTime = explode(" ", $pDate);
		$date = explode("-", $dateTime[0]);
		$dateConverted = array("annee"=>$date[0],"mois"=>$date[1], "jour"=>$date[2]);
		if(isset($dateTime[1]))
		{
			$time = explode(":", $dateTime[1]);
			$dateConverted["heure"] = $time[0];
			$dateConverted["minute"] = $time[1];
			$dateConverted["seconde"] = $time[2];
		}
		return $dateConverted;
	}
	
	
	/**
	 * Méthode de conversion d'un tableau associatif en date au format attendu par la base de données
	 * @param Array $pDate				Date à convertir
	 * @return String
	 */
	static public function convertDateTo(array $pDate)
	{
		$dateConverted = $pDate['annee']."-".$pDate['mois']."-".$pDate['jour'];
		if(isset($pDate['heure']))
			$dateConverted .= " ".$pDate['heure'].":".$pDate['minute'].":".$pDate['seconde'];
		return $dateConverted;
	}

	
	/**
	 * Méthode comparant deux dates, permet de savoir si $pDate1 est plus récente que $pDate2
	 * @param String $pDate1					Date au format de la base
	 * @param String $pDate2					Date au format de la base
	 * @return boolean
	 */
	static public function compare2Date($pDate1, $pDate2)
	{
		$date1 = self::convertDateFrom($pDate1);
		$date2 = self::convertDateFrom($pDate2);
		return mktime($date1["heure"],$date1['minute'],$date1['seconde'],$date1['mois'],$date1['jour'],$date1['annee']*1) > mktime($date2["heure"],$date2['minute'],$date2['seconde'],$date2['mois'],$date2['jour'],$date2['annee']*1);
	}
	
	
	/**
	 * Singleton
	 * @param String $pClassName [optional]
	 * @return MysqlHandler
	 */
	static public function getInstance($pClassName = "")
	{
		if($pClassName=="")
			$pClassName = __CLASS__;
		return parent::getInstance($pClassName);
	}
	
	
	/**
	 * ToString()
	 * @return String
	 */
	public function __toString()
	{
		return "Objet MysqlHandler";
	}
}