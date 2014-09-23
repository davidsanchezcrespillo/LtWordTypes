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
      $this->assertEquals(LtWordTypes::UNKNOWN_WORD_TYPE, $this->_ltWordTypes->getWordType(""));
      
      $this->assertEquals(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("vyras"));
      $this->assertEquals(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdė"));
      $this->assertEquals(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdĖ"));

      $this->assertEquals(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("vanduo"));
      $this->assertEquals(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("DanTis"));

  }
}
