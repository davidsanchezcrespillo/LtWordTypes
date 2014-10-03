<?php 
namespace LtWords\LtWordTypes;

use \PDO;

/**
 * Class to identify word types in Lithuanian language.
 */
class LtWordTypes
{
  const UNKNOWN_WORD_TYPE = -1;
  const REGULAR_NOUN = 0;
  const IRREGULAR_MASCULINE_NOUN = 1;
  const IRREGULAR_FEMENINE_NOUN = 2;
  const SOFT_GENITIVE_NOUN = 3;
  const HARD_GENITIVE_NOUN = 4;

  /**
   * Select a type depending on the flags received from the DB.
   * The flags are encoded following the ispell LT dictionary.
   * @param string $flags the flags stored in the DB
   * @return array a list containing several constants
   */
  private function _getType($flags)
  {
      $returnArray = array();
      if (strpos($flags, "M") !== false) {
          array_push($returnArray, self::IRREGULAR_FEMENINE_NOUN);
      }

      if (strpos($flags, "V") !== false) {
          array_push($returnArray, self::IRREGULAR_MASCULINE_NOUN);
      }

      if (strpos($flags, "D") !== false) {
          array_push($returnArray, self::REGULAR_NOUN);
      }
      
      if (strpos($flags, "I") !== false) {
          array_push($returnArray, self::SOFT_GENITIVE_NOUN);
      }
      
      if (strpos($flags, "K") !== false) {
          array_push($returnArray, self::HARD_GENITIVE_NOUN);
      }
      
      return $returnArray;
  }

  /**
   * Get a word record from the database;
   * @param string $word the word to check
   * @return array the whole record
   */
  private function getWordFromDb($word)
  {
    $retrievedRecord = array();

    try {
      //Connect to the database and open connections

      $connectionString = 'sqlite:' . __DIR__ . '/../words.sqlite3';
      
      //echo "CONNECTION: $connectionString\n";

      $dbHandler = new PDO($connectionString);
      // Set errormode to exceptions
      $dbHandler->setAttribute(
          PDO::ATTR_ERRMODE, 
          PDO::ERRMODE_EXCEPTION
      );
 
      // Select all data from memory db messages table 
      $statement = $dbHandler->prepare(
          "SELECT * FROM words WHERE words.word = ?"
      );

      if ($statement->execute(array(mb_strtolower($word, 'UTF-8')))) {
          while ($row = $statement->fetch()) {
              //echo "Id: " . $row['id'] . "\n";
              //echo "Word: " . $row['word'] . "\n";
              //echo "Flags: " . $row['flags'] . "\n";
              //echo "\n";
              $retrievedRecord = $row;
          }
      }

      //Close db connections
      $dbHandler = null;
    }
    catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage() . "\n";
    }
    
    return $retrievedRecord;
  }

  /**
   * Get the type of a Lithuanian word.
   * @param string $word a word in Lithuanian.
   * @return array all the types for that word
   */
  public function getWordType($word)
  {
    //echo "WORD: '$word'\n";
    $returnType = array(self::UNKNOWN_WORD_TYPE);
    //print_r($returnType);

    $wordRecord = $this->getWordFromDb($word);
    //print_r($wordRecord);

    if (isset($wordRecord['flags'])) {
      $returnType = $this->_getType($wordRecord['flags']);
    }

    //print_r($returnType);

    return $returnType;
  }
  
  public function getSimilarWords($word)
  {
  }
}
