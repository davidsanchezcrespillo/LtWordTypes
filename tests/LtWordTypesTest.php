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
      $emptyType = $this->_ltWordTypes->getWordType("");
      
      $this->assertInternalType('array', $emptyType);
      $this->assertContains(LtWordTypes::UNKNOWN_WORD_TYPE, $emptyType);
      
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("vyras"));
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdė"));
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdĖ"));

      $this->assertContains(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("vanduo"));
      $this->assertContains(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("DanTis"));

      $this->assertContains(LtWordTypes::IRREGULAR_FEMENINE_NOUN, $this->_ltWordTypes->getWordType("sesuo"));

      $this->assertContains(LtWordTypes::IRREGULAR_FEMENINE_NOUN, $this->_ltWordTypes->getWordType("ausis"));
      $this->assertContains(LtWordTypes::HARD_GENITIVE_NOUN, $this->_ltWordTypes->getWordType("ausis"));
  }
}
