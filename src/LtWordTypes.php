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
  const ADJECTIVE = 5;

  /*
   * Collates a word for comparison purposes.
   * @param string $word the word to collate
   * @return string the collated word
   */
  public function collateWord($word)
  {
      $trans = array(
          "ą" => "a",
          "č" => "c",
          "ę" => "e",
          "ė" => "e",
          "į" => "i",
          "š" => "s",
          "ų" => "u",
          "ū" => "u",
          "ž" => "z",
          "Ą" => "A",
          "Č" => "C",
          "Ę" => "E",
          "Ė" => "E",
          "Į" => "I",
          "Š" => "S",
          "Ų" => "U",
          "Ū" => "U",
          "Ž" => "Z",
      );

      return strtr($word, $trans);
  }

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
      
      if (strpos($flags, "B") !== false) {
          array_push($returnArray, self::ADJECTIVE);
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
          "SELECT * FROM words WHERE lower(words.asciiword) = ?"
      );

      $wordToCheck = $this->collateWord(mb_strtolower($word, 'UTF-8'));
      if ($statement->execute(array($wordToCheck))) {
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
   * Get suggested words of a given type, beginning with a certain prefix;
   * @param string $wordPrefix the word prefix
   * @param array $flags list of flags indicating the word type
   * @return array a list of suggested words
   */
  public function getSuggestions($wordPrefix, $flags)
  {
    $retrievedRecords = array();

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
          "SELECT word, flags FROM words " .
          "WHERE words.asciiword LIKE ? || '%' " .
          "ORDER BY word"
      );

      $wordPrefixToCheck = $this->collateWord(
          mb_strtolower($wordPrefix, 'UTF-8')
      );
      if ($statement->execute(array($wordPrefixToCheck))) {
          while ($row = $statement->fetch()) {
              //echo "Id: " . $row['id'] . "\n";
              //echo "Word: " . $row['word'] . "\n";
              //echo "Flags: " . $row['flags'] . "\n";
              //echo "\n";
              $retrievedWord = $row['word'];
              $retrievedFlags = $row['flags'];
              
              $flagFound = false;
              foreach ($flags as $flag) {
                  if (strpos($retrievedFlags, $flag) !== false) {
                      $flagFound = true;
                      break;
                  }
              }
              if (!$flagFound) {
                  continue;
              }
              
              array_push($retrievedRecords, $retrievedWord);
          }
      }

      //Close db connections
      $dbHandler = null;
    }
    catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage() . "\n";
    }
    
    return $retrievedRecords;
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
    $returnWord = "";

    $wordRecord = $this->getWordFromDb($word);
    //print_r($wordRecord);

    if (isset($wordRecord['flags'])) {
      $returnType = $this->_getType($wordRecord['flags']);
    }
    
    if (isset($wordRecord['word'])) {
      $returnWord = $wordRecord['word'];
    }

    //print_r($returnType);

    return array("word" => $returnWord, "type" => $returnType);
  }
  
}
