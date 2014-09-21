<?php 
namespace LtWords\LtWordTypes;

use \PDO;

/**
 * Class to identify word types in Lithuanian language.
 */
class LtWordTypes
{
  /**
   * Get the type of a Lithuanian word.
   * The input is a word in Lithuanian.
   * The output will be the type.
   */
  public function getWordType($word)
  {
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
              echo "Id: " . $row['id'] . "\n";
              echo "Word: " . $row['word'] . "\n";
              echo "Flags: " . $row['flags'] . "\n";
              echo "\n";
          }
      }

      //Close db connections
      $dbHandler = null;
    }
    catch(PDOException $e) {
      // Print PDOException message
      echo $e->getMessage() . "\n";
    }
    
    return "";
  }
}
