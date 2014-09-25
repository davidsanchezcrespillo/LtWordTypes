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
   * @return array a list containing several constants
   */
  private function _getType($flags)
  {
      if (strpos($flags, "M") !== false) {
          return array(self::IRREGULAR_FEMENINE_NOUN);
      }

      if (strpos($flags, "V") !== false) {
          return array(self::IRREGULAR_MASCULINE_NOUN);
      }

      if (strpos($flags, "D") !== false) {
          return array(self::REGULAR_NOUN);
      }
      
      return array();
  }

  /**
   * Get the type of a Lithuanian word.
   * The input is a word in Lithuanian.
   * The output will be the type.
   */
  public function getWordType($word)
  {
    $returnType = array(self::UNKNOWN_WORD_TYPE);

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
