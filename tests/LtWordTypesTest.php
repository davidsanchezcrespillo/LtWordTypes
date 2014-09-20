<?php
 
use LtWords\LtWordTypes\LtWordTypes;

/**
 * Unit tests for the LtWordTypes class.
 */ 
class LtWordTypesTest extends PHPUnit_Framework_TestCase
{
 
  private $_ltWordTypes;
  
  public function setUp()
  {
	  $this->_ltWordTypes = new LtWordTypes;
  }
  
  public function testBasic()
  {
	  $this->assertEquals("", $this->_ltWordTypes->getWordType(""));
  }
}
