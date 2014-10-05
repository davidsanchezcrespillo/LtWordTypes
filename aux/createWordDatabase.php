<?php 

  function collateWord($word)
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

  // Set default timezone
  date_default_timezone_set('UTC');
 
  try {
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:words.sqlite3');
    $file_db->exec("pragma synchronous = off;");
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
 
    /**************************************
    * Create tables                       *
    **************************************/
 
    // Create table messages
    $file_db->exec("CREATE TABLE IF NOT EXISTS words (
                    id INTEGER PRIMARY KEY, 
                    word TEXT,
                    asciiword TEXT, 
                    flags TEXT)");
 
    /**************************************
    * Set initial data                    *
    **************************************/
 
    $allWords = file("liet-utf8.dict");

    // Array with some test data to insert to database             
    $words = array(
        array(
            'word' => 'Vyras',
            'flags' => 'D'
        ),
        array(
            'word' => 'Vaikas',
            'flags' => 'D'
        ),
    );
 
 
    /**************************************
    * Play with databases and tables      *
    **************************************/
 
    // Prepare INSERT statement to SQLite3 file db
    $insert = "INSERT INTO words (word, asciiword, flags) 
                VALUES (:word, :asciiword, :flags)";
    $stmt = $file_db->prepare($insert);
 
    // Bind parameters to statement variables
    $stmt->bindParam(':word', $word);
    $stmt->bindParam(':asciiword', $asciiword);
    $stmt->bindParam(':flags', $flags);
 
    // Loop thru all messages and execute prepared insert statement
    foreach ($allWords as $w) {
      $w = trim($w);

      // Set values to bound variables
      $fields = split("/", $w);
      $word = $fields[0];
      $asciiword = collateWord($word);
      $flags = "";
      if (isset($fields[1])) {
        $flags = $fields[1];
      }
      
      echo "$word / $asciiword / $flags\n";

      //$word = $w['word'];
      //$flags = $w['flags'];
 
      // Execute statement
      $stmt->execute();
    }
 
    // Select all data from memory db messages table 
    $result = $file_db->query('SELECT * FROM words');
 
    $numRows = 0;
    foreach($result as $row) {
      $numRows++;
      //echo "Id: " . $row['id'] . "\n";
      //echo "Title: " . $row['word'] . "\n";
      //echo "Flags: " . $row['flags'] . "\n";
      //echo "\n";
    }
    echo "Number of words: $numRows\n"; 

    /**************************************
    * Drop tables                         *
    **************************************/
 
    // Drop table messages from file db
    //$file_db->exec("DROP TABLE words");
 
    /**************************************
    * Close db connections                *
    **************************************/
 
    // Close file db connection
    $file_db = null;
  }
  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage() . "\n";
  }
?>
