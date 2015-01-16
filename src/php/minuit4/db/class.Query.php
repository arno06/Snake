<?php
/**
 * 
 * @author Arno
 *
 */
class Query
{
	/**
	 * @var String
	 */
	const LIKE 				= 	" LIKE ";
	/**
	 * @var String
	 */
	const EQUAL				= 	" = ";
	/**
	 * @var String
	 */
	const UPPER 			= 	" > ";
	/**
	 * @var String
	 */
	const UPPER_EQUAL		=	" >= ";
	/**
	 * @var String
	 */
	const LOWER 			= 	" < ";
	/**
	 * @var String
	 */
	const LOWER_EQUAL		=	" >= ";
	
	/**
	 * @var String
	 */
	const IS				=	" IS ";
	/**
	 * @var String
	 */
	const IS_NOT			=	" IS NOT ";
	/**
	 * @var String
	 */
	const JOIN				=	" JOIN ";
	/**
	 * @var String
	 */
	const JOIN_NATURAL		=	" NATURAL JOIN ";
	/**
	 * @var String
	 */
	const JOIN_INNER 		= 	" INNER JOIN ";
	/**
	 * @var String
	 */
	const JOIN_OUTER_FULL 	= 	" FULL OUTER JOIN ";
	/**
	 * @var String
	 */
	const JOIN_OUTER_LEFT 	= 	" LEFT OUTER JOIN ";
	/**
	 * @var String
	 */
	const JOIN_OUTER_RIGHT 	= 	" RIGHT OUTER JOIN ";
	/**
	 * @var String
	 */
	const JOIN_CROSS 		= 	" CROSS JOIN ";
	/**
	 * @var String
	 */
	const JOIN_UNION 		= 	" UNION JOIN ";
	
	/**
	 * @var Array
	 */
	static private $specials = array(
						"NOW()",
						"NULL"
	);
	
	/**
	 * M�thode de cr�ation d'une requ�te SQL SELECT
	 * @param String $pFields
	 * @param String $pTables
	 * @return QuerySelect
	 */
	static public function select($pFields, $pTables)
	{
		$i = new QuerySelect($pFields, $pTables);
		return $i;
	}
	
	/**
	 * M�thode de cr�ation d'une condition SQL ind�pendante (instructions WHERE, ORDER BY, LIMIT...)
	 * @return QueryCondition
	 */
	static public function condition()
	{
		return new QueryCondition();
	}
	
	/**
	 * M�thode de cr�ation d'une requ�te 'INSERT' d'insertion d'une tuple
	 * @param Array $pValues
	 * @return QueryInsert
	 */
	static public function insert($pValues)
	{
		return new QueryInsert($pValues, QueryInsert::UNIQUE);
	}

	/**
	 * M�thode de cr�ation d'une requ�te 'INSERT' d'insertion de N tuples
	 * @param Array $pValues
	 * @return QueryInsert
	 */
	static public function insertMultiple($pValues)
	{
		return new QueryInsert($pValues, QueryInsert::MULTIPLE);
	}
	
	/**
	 * M�thode de cr�ation d'une requ�te DELETE
	 * @return QueryDelete
	 */
	static public function delete()
	{
		return new QueryDelete();
	}
	
	/**
	 * M�thode de cr�ation d'une requ�te UPDATE
	 * @param String $pTable
	 * @return QueryUpdate
	 */
	static public function update($pTable)
	{
		return new QueryUpdate($pTable);
	}
	
	/**
	 * M�thode d'�chappement d'une valeur (simple quote, double quote...)
	 * @param String $pValue
	 * @return String
	 */
	static public function escapeValue($pValue)
	{
		if(!in_array(strtoupper($pValue), self::$specials))
			return "'".addslashes($pValue)."'";
		else
			return strtoupper($pValue);
	}
}

class QueryCondition
{
	/**
	 * @var Array
	 */
	private $ands = array();
	/**
	 * @var Array
	 */
	private $or = array();
	/**
	 * @var String
	 */
	private $order = "";
	/**
	 * @var String
	 */
	private $limit = "";
	/**
	 * @var String
	 */
	private $group = "";
	
	/**
	 * M�thode d'ajout d'une condition 'OR' � l'instance de condition en cours
	 * @param String $pField
	 * @param String $pType
	 * @param String $pValue
	 * @return QueryCondition
	 */
	public function orWhere	($pField, $pType, $pValue)
	{
		array_push($this->or, $pField.$pType.Query::escapeValue($pValue));
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une condition 'AND' � l'instance de condition en cours
	 * @param String $pField
	 * @param String $pType
	 * @param String $pValue
	 * @return QueryCondition
	 */
	public function andWhere($pField, $pType, $pValue)
	{
		array_push($this->ands, $pField.$pType.Query::escapeValue($pValue));
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'un 'GROUP BY'
	 * @param String $pField
	 * @return QueryCondition
	 */
	public function groupBy($pField)
	{
		$this->group = " GROUP BY ".$pField;
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'un 'ORDER BY'
	 * @param String $pField	
	 * @param String $pType		ASC|DESC
	 * @return QueryCondition
	 */
	public function order($pField, $pType = "ASC")
	{
		$this->order = " ORDER BY ".$pField." ".$pType;
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une LIMIT
	 * @param Int $pFirst
	 * @param Int $pNumber
	 * @return QueryCondition
	 */
	public function limit($pFirst, $pNumber)
	{
		$this->limit = " LIMIT ".$pFirst.",".$pNumber;
		return $this;
	}
	
	/**
	 * M�thode de g�n�ration de la condition
	 * @return String
	 */
	public function get()
	{
		$where = "";
		$ands = implode($this->ands," AND ");
		$or = implode($this->or, " OR ");
		if(!empty($ands))
			$where .= " WHERE ".$ands;
		if(!empty($or))
		{
			if(empty($ands))
				$where .= " WHERE ".$or;
			else
				$where .= " OR ".$or;
		}
		return $where.$this->group.$this->order.$this->limit;
	}
}

class QueryWithCondition
{
	
	/**
	 * @var QueryCondition
	 */
	protected $condition;

	
	/**
	 * M�thode de d�finition de la condition d'une requ�te SELECT
	 * @param QueryCondition $pConditionInstance
	 * @return QuerySelect
	 */
	public function setCondition($pConditionInstance)
	{
		if(!$pConditionInstance instanceof QueryCondition)
			return $this;
		$this->condition = $pConditionInstance;
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une condition 'WHERE' � la requ�te SELECT en cours
	 * @param String $pField
	 * @param String $pType
	 * @param String $pValue
	 * @return QuerySelect
	 */
	public function where($pField, $pType, $pValue)
	{
		$this->getCondition()->andWhere($pField, $pType, $pValue);
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une condition 'AND' � la requ�te SELECT en cours
	 * @param String $pField
	 * @param String $pType
	 * @param String $pValue
	 * @return QuerySelect
	 */
	public function andWhere($pField, $pType, $pValue)
	{
		$this->getCondition()->andWhere($pField, $pType, $pValue);
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une condition 'OR' � la requ�te SELECT en cours
	 * @param String $pField
	 * @param String $pType
	 * @param String $pValue
	 * @return QuerySelect
	 */
	public function orWhere($pField, $pType, $pValue)
	{
		$this->getCondition()->orWhere($pField, $pType, $pValue);
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'un 'ORDER BY'
	 * @param String $pField	
	 * @param String $pType		ASC|DESC
	 * @return QuerySelect
	 */
	public function order($pField, $pType = "ASC")
	{
		$this->getCondition()->order($pField, $pType);
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une LIMIT
	 * @param Int $pFirst
	 * @param Int $pNumber
	 * @return QuerySelect
	 */
	public function limit($pFirst, $pNumber)
	{
		$this->getCondition()->limit($pFirst, $pNumber);
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'un 'GROUP BY'
	 * @param String $pField
	 * @return QuerySelect
	 */
	public function groupBy($pField)
	{
		$this->getCondition()->groupBy($pField);
		return $this;
	}
	
	/**
	 * @return QueryCondition
	 */
	protected function getCondition()
	{
		if(!$this->condition)
			$this->condition = Query::condition();
		return $this->condition;
	}
}

class QuerySelect extends QueryWithCondition
{
	/**
	 * @var Array
	 */
	private $tables = array();
	/**
	 * @var Array
	 */
	private $fields = array();
	/**
	 * @var String
	 */
	private $joins = "";
	
	/**
	 * Constructor
	 * @param String $pFields
	 * @param String $pTables
	 * @return void
	 */
	public function __construct($pFields, $pTables)
	{
		$this->addFrom($pFields, $pTables);
	}
	
	/**
	 * 
	 * @param $pTable
	 * @param $pType
	 * @param $pOn
	 * @return QuerySelect
	 */
	public function join($pTable, $pType = " NATURAL JOIN ", $pOn = "")
	{
		if(!empty($pOn))
			$pOn = "ON ".$pOn;
		$this->joins .= $pType.$pTable." ".$pOn;
		return $this;
	}
	
	/**
	 * M�thode d'ajout d'une table et de champs au SELECT en cours
	 * @param String $pFields
	 * @param String $pTables
	 * @return QuerySelect
	 */
	public function addFrom($pFields, $pTables)
	{
		if(!in_array($pTables, $this->tables))
			array_push($this->tables, $pTables);
		if(!in_array($pFields, $this->fields))
			array_push($this->fields, $pFields);
		return $this;
	}
	
	/**
	 * M�thode de g�n�ration de la requ�te SELECT
	 * @return String
	 */
	public function get()
	{
		$field = implode($this->fields, ",");
		$table = implode($this->tables, ",");
		$joins = $this->joins." ";
		$condition = $this->getCondition()->get();
		return "SELECT ".$field." FROM ".$table." ".$joins.$condition.";";
	}

}

class QueryUpdate extends QueryWithCondition
{
	/**
	 * Nom de la table
	 * @var String
	 */
	private $table = "";
	/**
	 * Tableau des valeurs � mettre-�-jour
	 * @var unknown_type
	 */
	private $values = array();
	/**
	 * Constructor
	 * @param String $pTable
	 * @return void
	 */
	public function __construct($pTable)
	{
		$this->table = $pTable;
	}
	
	/**
	 * M�thode de d�finition des champs � mettre-�-jour
	 * @param Array $pValues
	 * @return QueryUpdate
	 */
	public function values($pValues)
	{
		foreach($pValues as $field=>$value)
			array_push($this->values, $field."=".Query::escapeValue($value));
		return $this;
	}
	
	/**
	 * M�thode de g�n�ration de la m�thode 'UPDATE'
	 * @return String
	 */
	public function get()
	{
		$values = implode($this->values, ",");
		$condition = $this->getCondition()->get();
		return "UPDATE ".$this->table." SET ".$values.$condition.";";
	}
}

class QueryInsert
{
	/**
	 * @var String
	 */
	const UNIQUE = "UNIQUE";
	/**
	 * @var String
	 */
	const MULTIPLE = "MULTIPLE";
	/**
	 * Nom de la table
	 * @var String
	 */
	private $table = "";
	/**
	 * Chaine de caract�res des champs de la table � remplir
	 * @var String
	 */
	private $fields = "";
	/**
	 * Tableau de chaines de caract�res des valeurs � ins�rer
	 * @var Array
	 */
	private $values = array();
	
	/**
	 * Constructor
	 * @param Array $pValues
	 * @param String $pType
	 * @return void
	 */
	public function __construct($pValues, $pType = "")
	{
		switch($pType)
		{
			case QueryInsert::MULTIPLE:
				$this->setFields($pValues[0]);
				$this->setValues($pValues);
			break;
			case QueryInsert::UNIQUE:
			default:
				$this->setFields($pValues);
				$this->setValues(array($pValues));
			break;
		}
	}
	
	/**
	 * M�thode de d�finition des champs de la table en fonction des cl�s du tableau de valeurs envoy�es
	 * @param Array $pTuple
	 * @return QueryInsert
	 */
	private function setFields($pTuple)
	{
		$f = array();
		foreach($pTuple as $field=>$value)
			array_push($f, $field);
		$this->fields = "(".implode($f, ",").")";
	}
	
	/**
	 * M�thode de d�finition et d'�chappement des valeurs � ins�rer
	 * @param Array $pTuples
	 * @return QueryInsert
	 */
	private function setValues($pTuples)
	{
		$this->values = array();
		for($i = 0, $max = count($pTuples); $i<$max; $i++)
		{
			$pTuples[$i] = array_map("Query::escapeValue", $pTuples[$i]);
			array_push($this->values, "(".implode($pTuples[$i], ",").")");
		}
	}
	
	/**
	 * M�thode de d�finition du nom de la table dans laquelle ins�rer les valeurs
	 * @param String $pTable
	 * @return QueryInsert
	 */
	public function into($pTable)
	{
		$this->table = $pTable;
		return $this;
	}
	
	/**
	 * M�thode de g�n�ration de la requ�te 'INSERT'
	 * @return String
	 */
	public function get()
	{
		$values = implode($this->values, ",");
		return "INSERT INTO ".$this->table." ".$this->fields." VALUES ".$values.";";
	}
}

class QueryDelete extends QueryWithCondition
{
	/**
	 * Nom de la table
	 * @var String
	 */
	private $table;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct(){}
	
	/**
	 * M�thode de d�finition de la table � cibler pour la suppression
	 * @param String $pTable
	 * @return QueryDelete
	 */
	public function from($pTable)
	{
		$this->table = $pTable;
		return $this;
	}
	
	/**
	 * M�thode de g�n�ration de la requ�te 'DELETE'
	 * @return String
	 */
	public function get()
	{
		$condition = $this->getCondition()->get();
		return "DELETE FROM ".$this->table.$condition.";";
	}
}