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
      $this->assertContains(LtWordTypes::UNKNOWN_WORD_TYPE, $emptyType["type"]);
      $this->assertEquals("", $emptyType['word']);
      
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("vyras")["type"]);
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdė")["type"]);
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dėdĖ")["type"]);

      $this->assertContains(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("vanduo")["type"]);
      $this->assertContains(LtWordTypes::IRREGULAR_MASCULINE_NOUN, $this->_ltWordTypes->getWordType("DanTis")["type"]);

      $this->assertContains(LtWordTypes::IRREGULAR_FEMENINE_NOUN, $this->_ltWordTypes->getWordType("sesuo")["type"]);

      $this->assertContains(LtWordTypes::IRREGULAR_FEMENINE_NOUN, $this->_ltWordTypes->getWordType("ausis")["type"]);
      $this->assertContains(LtWordTypes::HARD_GENITIVE_NOUN, $this->_ltWordTypes->getWordType("ausis")["type"]);
  }
  
  public function testReplacements()
  {
      $this->assertEquals("", $this->_ltWordTypes->collateWord(""));
      $this->assertEquals("aceeisuuz", $this->_ltWordTypes->collateWord("ąčęėįšųūž"));
      $this->assertEquals("ACEEISUUZ", $this->_ltWordTypes->collateWord("ĄČĘĖĮŠŲŪŽ"));
      $this->assertEquals("zvirblis", $this->_ltWordTypes->collateWord("žvirblis"));
      $this->assertEquals("Zvirblis", $this->_ltWordTypes->collateWord("Žvirblis"));
  }
  
  public function testCollation()
  {
      $this->assertContains(LtWordTypes::REGULAR_NOUN, $this->_ltWordTypes->getWordType("dede")["type"]);
      $this->assertEquals("dėdė", $this->_ltWordTypes->getWordType("dede")["word"]);
  }
}
