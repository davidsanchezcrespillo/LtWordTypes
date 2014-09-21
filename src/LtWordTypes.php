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

  /**
   * Select a type depending on the flags received from the DB.
   * The flags are encoded following the ispell LT dictionary.
   * @param string $flags the flags stored in the DB
   * @return int a constant
   */
  private function _getType($flags)
  {
      if (strstr("D", $flags)) {
          return self::REGULAR_NOUN;
      }
      
      if (strstr("V", $flags)) {
          return self::IRREGULAR_MASCULINE_NOUN;
      }

      if (strstr("M", $flags)) {
          return self::IRREGULAR_FEMENINE_NOUN;
      }
  }

  /**
   * Get the type of a Lithuanian word.
   * The input is a word in Lithuanian.
   * The output will be the type.
   */
  public function getWordType($word)
  {
    $returnType = self::UNKNOWN_WORD_TYPE;

    try {
      //Connect to the database and open connections

      $dbHandler = new PDO('sqlite:words.sqlite3');
      // Set errormode to exceptions
      $dbHandler->setAttribute(
          PDO::ATTR_ERRMODE, 
          PDO::ERRMODE_EXCEPTION
      );
 
      // Select all data from memory db messages table 
      $statement = $dbHandler->prepare(
          "SELECT * FROM words WHERE words.word = ?"
      );

      if ($statement->execute(array($word))) {
          while ($row = $statement->fetch()) {
              //echo "Id: " . $row['id'] . "\n";
              //echo "Word: " . $row['word'] . "\n";
              //echo "Flags: " . $row['flags'] . "\n";
              //echo "\n";
              $returnType = $this->_getType($row['flags']);
          }
      }

      //Close db connections
      $dbHandler = null;
    }
    catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage() . "\n";
    }
    
    return $returnType;
  }
}
