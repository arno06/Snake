<?php
/**
 * Class RSS
 * Permet de générer un Flux RSS Atom 2.0
 *
 * @author Arnaud NICOLAS - arno06@gmail.com
 * @version .1
 * @package CBi
 * @subpackage utils
 */
class RSS
{
	/**
	 * 
	 * @var String
	 */
	public $title = "";
	
	/**
	 * 
	 * @var String
	 */
	public $description = "";
	
	/**
	 * 
	 * @var String
	 */
	public $rss_version = "2.0";
	
	/**
	 * 
	 * @var String
	 */
	public $link = "";
	
	/**
	 * 
	 * @var String
	 */
	public $link_rss = "";
	
	/**
	 * 
	 * @var String
	 */
	public $namespaces = array("atom"=>"http://www.w3.org/2005/Atom");
	
	/**
	 * 
	 * @var String
	 */
	private $items = array();
	
	/**
	 * Constructor
	 * @param String $pTitle
	 * @param String $pDescription
	 * @param String $pLink
	 * @return void
	 */
	public function __construct($pTitle, $pDescription, $pLink, $pUrlRss)
	{
		$this->title = $pTitle;
		$this->description = $pDescription;
		$this->link = $pLink;
		$this->link_rss = $pUrlRss;
	}
	
	/**
	 * Méthode d'ajout d'un item au contenu du flux RSS
	 * @param String $pTitle
	 * @param String $pDescription
	 * @param String $pDate
	 * @param String $pLink
	 * @param String $pGuid [optionnal]
	 * @return void
	 */
	public function addItem($pTitle, $pDescription, $pDate, $pLink, $pGuid = "")
	{
		$item = array();
		$item["title"] = array("nodeValue"=>$pTitle);
		$item["description"] = array("nodeValue"=>$pDescription);
		$item["pubDate"] = array("nodeValue"=>$pDate);
		$item["link"] = array("nodeValue"=>$pLink);
		$guid = "";
		if($pGuid == "")
			$guid = $pLink;
		else
			$guid = $pGuid;
		$item["guid"] = array("nodeValue"=>$guid);
		$this->items[] = $item;
	}
	
	/**
	 * Méthode d'ajout d'un namespace au flux RSS
	 * @param String $pName
	 * @param String $pValue
	 * @return void
	 */
	public function addNameSpace($pName, $pValue)
	{
		$this->namespaces[$pName] = $pValue;
	}
	
	/**
	 * Renvoi le contenu du flux RSS sous forme d'un tableau multidimensionnel
	 * @return array
	 */
	public function toArray()
	{
		$xml = array("rss"=>array("version"=>$this->rss_version));
		foreach($this->namespaces as $name=>$value)
			$xml["rss"]["xmlns:".$name] = $value;
		$xml["rss"]["channel"] = array();
		$chan = &$xml["rss"]["channel"];
		$chan["title"] = array("nodeValue"=>$this->title);
		$chan["description"] = array("nodeValue"=>$this->description);
		$chan["link"] = array("nodeValue"=>$this->link);
		$chan["atom:link"] = array("type"=>"application/rss+xml", "rel"=>"self","href"=>$this->link_rss);
		$chan["item"] = $this->items;
		return $xml;
	}
	
	/**
	 * Renvoi le contenu du flux RSS courant au format XML
	 * @return String
	 */
	public function toXML()
	{
		return SimpleXML::encode($this->toArray());
	}
	
}