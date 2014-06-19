<?php

/**
 *
 * Array 2 XML class
 * Convert an array or multi-dimentional array to XML
 *
 * @author Kevin Waterson
 * @copyright 2009 PHPRO.ORG
 * http://www.phpro.org/classes/PHP-Recursive-Array-To-XML-With-DOM.html
 *
 */

class Array2xml extends DomDocument {

    public $nodeName;
    private $xpath;
    private $root;
    private $node_name;

    public function __construct($root='root', $node_name='node')
    {
        parent::__construct();

        $this->encoding = "UTF-8";
        $this->formatOutput = true;
        $this->node_name = $node_name;
        $this->root = $this->appendChild($this->createElement( $root ));

        $this->xpath = new DomXPath($this);
    }

    // creates the XML representation of the array
    public function createNode( $arr, $node = null)
    {
        if (is_null($node))
        {
            $node = $this->root;
        }
        foreach($arr as $element => $value) 
        {
            $element = is_numeric( $element ) ? $this->node_name : $element;

            $child = $this->createElement($element, (is_array($value) ? null : $value));
            $node->appendChild($child);

            if (is_array($value))
            {
                self::createNode($value, $child);
            }
        }
    }
    
    public function __toString()
    {
        return $this->saveXML();
    }

    public function query($query)
    {
        return $this->xpath->evaluate($query);
    }

}

?>
