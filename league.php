<?php
  class Main {
    // Runs the script
    public function start() {
      $file = fopen("league_list.txt","r") or die("Unable to open file!");
      // Stores each club object in array
      $allClubsArray = array();
      // Counter to determine ordinal value
      $ordinalCounter = 1;

      while(! feof($file))
        {
          // grabs current line in the file
          $line = fgets($file);

          // Send in a line with excess white space to get the name of the club
          $clubName = Utility::getName(Utility::removeExcessWhite($line));

          // key value of club information for easier understanding of code
          $clubArray = Utility::makeLineReady($line); 

          // Create club object
          $club = new Club($clubName, $clubArray['totalSeasons'], $clubArray['totalGames'], $clubArray['totalWins'], $clubArray['totalDraws'], $clubArray['totalLoses'], $clubArray['totalGoals'], $clubArray['totalGoalsConceded'], $clubArray['totalPoints']);

          // store all club objects in an array
          array_push($allClubsArray, $club);  
        }
        
        // Sort club objects array in descending order of season average
        usort($allClubsArray, function($a, $b) {
          return $b->seasonAverage > $a->seasonAverage;
        });

        // Creates the table to console
        foreach ($allClubsArray as $club) {
          if ($ordinalCounter < 10) {
            $line = " " . $ordinalCounter . "." . $club->displayLine(); 
            echo " " . $ordinalCounter . "." . $club->displayLine();
          } elseif ($ordinalCounter < 100) {
            $line = $ordinalCounter . "." . $club->displayLine(); 
            echo $ordinalCounter . "." . $club->displayLine();
          }
          
          $ordinalCounter++;

         // Uncomment this if you want a new file with the updated league list ordered by season average 
          // if(!file_put_contents("updated_league_list.txt", $line, FILE_APPEND)){
          //    // failure
          //   echo "Failed to put data into file";
          // } 
        }
      fclose($file);
    }  
  }

  class Club {
  	public function __construct($name, $seasons, $totalGames, $totalWins, $totalDraws, $totalLoses, $totalGoals, $totalGoalsConceded, $totalPoints) {
  		$this->name = $name;
      $this->seasons = $seasons;
      $this->totalGames = $totalGames;
      $this->totalWins = $totalWins;
      $this->totalDraws = $totalDraws;
      $this->totalLoses = $totalLoses;
      $this->totalGoals = $totalGoals;
      $this->totalGoalsConceded = $totalGoalsConceded;
      $this->totalPoints = $totalPoints;
      $this->seasonAverage = round($totalPoints / $seasons);
  	}

    // Turn a formatted array without the name but with ordinal still there to an array with key value pair
    public function turnArrayToHash($array) {
      $clubArray = array();
      $arrayCounter = 0;

      foreach ($array as $each) {
        switch ($arrayCounter):
          case 1:
            $clubArray["totalSeasons"] = $each;
            break;
          case 2:
            $clubArray["totalGames"] = $each;
            break;
          case 3:
            $clubArray["totalWins"] = $each;
            break;
          case 4:
            $clubArray["totalDraws"] = $each;
            break;
          case 5:
            $clubArray["totalLoses"] = $each;
            break;
          case 6:
            $clubArray["totalGoals"] = $each;
            break;
          case 7:
            $clubArray["totalGoalsConceded"] = $each;
            break;
          case 8;
            $clubArray["totalPoints"] = $each;
            break;  
          default:
            break;
        endswitch;
        $arrayCounter++;
      }
      return $clubArray;
    }

    // Creates a table with nice formatting
    public function displayLine() {
      return str_repeat(" ", Utility::whitespacing("name", $this)[0]) . $this->name . str_repeat(" ", Utility::whitespacing("name", $this)[1]) . 
             str_repeat(" ", Utility::whitespacing("seasons", $this)[0]) . $this->seasons . str_repeat(" ", Utility::whitespacing("seasons", $this)[1]) .
             str_repeat(" ", Utility::whitespacing("games", $this)[0]) . $this->totalGames . str_repeat(" ", Utility::whitespacing("games", $this)[1]) .
             str_repeat(" ", Utility::whitespacing("wins", $this)[0]) . $this->totalWins . str_repeat(" ", Utility::whitespacing("wins", $this)[1]) .
             str_repeat(" ", Utility::whitespacing("draws", $this)[0]) . $this->totalDraws . str_repeat(" ", Utility::whitespacing("draws", $this)[1]) .
             str_repeat(" ", Utility::whitespacing("losses", $this)[0]) . $this->totalLoses . str_repeat(" ", Utility::whitespacing("losses", $this)[1]) .
             str_repeat(" ", Utility::whitespacing("goals", $this)[0]) . $this->totalGoals . str_repeat(" ", Utility::whitespacing("goals", $this)[1]) . "- " .
             str_repeat(" ", Utility::whitespacing("goalsCondeded", $this)[0]) . $this->totalGoalsConceded . str_repeat(" ", Utility::whitespacing("goalsCondeded", $this)[1]) . 
             str_repeat(" ", Utility::whitespacing("points", $this)[0]) . $this->totalPoints . str_repeat(" ", Utility::whitespacing("points", $this)[1]) . 
             $this->seasonAverage . "\n";
    }
  }

  class Utility {
    // Remove excess white spaces (more than 1) from a line
    public function removeExcessWhite($line) {
      return trim(preg_replace('/\s\s+/', ' ', $line));
    }

    // Pass in a line removed with excess white spaces to get name
    public function getName($line) {
      preg_match('/\b[A-Z][A-Za-z \'&]+/', $line, $nameMatch);
      return trim($nameMatch[0]);
    }

    // Split a text line into an array
    public function splitLineIntoArray($line) {
      return explode(' ', $line);
    }

    // Remove hyphen from an array
    public function removeHyphenFromArray($array) {
      unset($array[array_search('-', $array)]);
      // reindexes the array
      $reindexArray = array_values($array);
      return $reindexArray;
    }

    // Make new array without the name of club, just stat values
    public function arrayWithoutName($array) {
      $noNameArray = array();
      foreach ($array as $each) {
        if (preg_match('/[0-9]/', $each)) {
          array_push($noNameArray, $each);
        }
      }
      return $noNameArray;
    }

    // Determine prefix and suffix space, takes in column name and a club object
    public function whitespacing($column, $club) {
      // Space for name from beginning till 1 space before the next number column
      $nameSpace = 27;

      // Default values
      $prefixSpace = 0;
      $suffixSpace = 2;

      if ($column == "name") {
        $prefixSpace = 1;
        $suffixSpace = $nameSpace - strlen($club->name);
      } elseif ($column == "seasons") {
        $prefixSpace = Utility::prefixHundredSpacing($club->seasons);
      } elseif ($column == "games") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalGames);
      } elseif ($column == "wins") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalWins);
      } elseif ($column == "draws") {
        $prefixSpace = Utility::prefixHundredSpacing($club->totalDraws);
      } elseif ($column == "losses") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalLoses);
      } elseif ($column == "goals") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalGoals);
        $suffixSpace = 1;
      } elseif ($column == "goalsCondeded") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalGoalsConceded);
      } elseif ($column == "points") {
        $prefixSpace = Utility::prefixThousandSpacing($club->totalPoints);
      }
      return array($prefixSpace, $suffixSpace);
    }

    // use for columns with max value less than 1000
    public function prefixHundredSpacing($number) {
      if ($number < 10) {
        return 2;
      } elseif ($number < 100) {
        return 1;
      }  elseif ($number < 1000) {
        return 0;
      } else {
        return "This number is too large for this function, use prefixThousandSpacing";
      }
    }

    // use for columns with max value less than 10000
    public function prefixThousandSpacing($number) {
      if ($number < 10) {
        return 3;
      } elseif ($number < 100) {
        return 2;
      } elseif ($number < 1000) {
        return 1;
      } elseif ($number < 10000) {
        return 0;
      } else {
        return "This number is too large for this function, use make a new function"; 
      }
    }

    // commands to turn line into a hash array for the creation of club object
    public function makeLineReady($line) {
      // Remove excess white space from line
      $replaced = Utility::removeExcessWhite($line);

      // Stores full name of club in $clubName
      $clubName = Utility::getName($replaced);

      // Split the string into cells - aka columns
      $columns = Utility::splitLineIntoArray($replaced);

      // Remove hyphen from array
      $columns = Utility::removeHyphenFromArray($columns);

      $newColumns = Utility::arrayWithoutName($columns);
      
      // Turn the column into key value pair
      $clubArray = Club::turnArrayToHash($newColumns);

      return $clubArray;
    }
  }

  // Test classes start

  class ClubTest {
    public function testTurnArrayToHash() {
      $array = array("1.", 104, 4062, 1652, 998, 1412, 6373, 5719, 4302);
      $expectedArray = array("totalSeasons"=>104, "totalGames"=>4062, "totalWins"=>1652, "totalDraws"=>998, "totalLoses"=>1412, "totalGoals"=>6373, "totalGoalsConceded"=>5719, "totalPoints"=>4302);

      $testArray = Club::turnArrayToHash($array);

      if ($testArray == $expectedArray) {
        echo "testTurnArrayToHash Array expected and actual match" . "\n";
      } else {
        echo "testTurnArrayToHash Array expected and actual DO NOT match" . "\n";
      }
    }

    public function testDisplayLine() {
      $club = new Club("Test Club", 104, 4062, 1652, 998, 1412, 6373, 5719, 4000);
      $expectedOutput = " Test Club                  104  4062  1652  998  1412  6373 - 5719  4000  38" . "\n";
      
      $testOutput = $club->displayLine();

      if ($testOutput == $expectedOutput) {
        echo "testDisplayLine Output expected and actual match" . "\n";
      } else {
        echo "testDisplayLine Output expected and actual DO NOT match" . "\n";
      }
    }

    public function runTests() {
      ClubTest::testTurnArrayToHash();
      ClubTest::testDisplayLine();
    }
  }

  class UtilityTest {
    public function testRemoveExcessWhite() {
      $line = " 1. Everton                    104  4062  1652   998  1412  6373 - 5719  4302  ";
      $expectedLine = "1. Everton 104 4062 1652 998 1412 6373 - 5719 4302";

      $line2 = "49. Brighton & Hove Albion       4   168    47    48    73   182 -  244   142";
      $expectedLine2 = "49. Brighton & Hove Albion 4 168 47 48 73 182 - 244 142";

      $testLine = Utility::removeExcessWhite($line);
      $testLine2 = Utility::removeExcessWhite($line2);

      if ($testLine == $expectedLine) {
        echo "testRemoveExcessWhite Line expected and actual match" . "\n";
      } else {
        echo "testRemoveExcessWhite Line expected and actual DO NOT match" . "\n";
      }

      if ($testLine2 == $expectedLine2) {
        echo "testRemoveExcessWhite Line2 expected and actual match" . "\n";
      } else {
        echo "testRemoveExcessWhite Line2 expected and actual DO NOT match" . "\n";
      }
    }

    public function testGetName() {
      $line = " 1. Everton                    104  4062  1652   998  1412  6373 - 5719  4302";
      $expectedLine = "Everton";

      $line2 = "49. Brighton & Hove Albion       4   168    47    48    73   182 -  244   142";
      $expectedLine2 = "Brighton & Hove Albion";

      $testLine = Utility::getName($line);
      $testLine2 = Utility::getName($line2);

      if ($testLine == $expectedLine) {
        echo "testGetName Line expected and actual match" . "\n";
      } else {
        echo "testGetName Line expected and actual DO NOT match" . "\n";
      }

      if ($testLine2 == $expectedLine2) {
        echo "testGetName Line2 expected and actual match" . "\n";
      } else {
        echo "testGetName Line2 expected and actual DO NOT match" . "\n";
      }
    }

    public function testSplitLineToArray() {
      $line = "1. Everton 104 4062 1652 998 1412 6373 - 5719 4302";
      $expected = array("1.", "Everton", 104, 4062, 1652, 998, 1412, 6373, "-", 5719, 4302);

      $line2 = "49. Brighton & Hove Albion 4 168 47 48 73 182 - 244 142";
      $expected2 = array("49.", "Brighton", "&", "Hove", "Albion", 4, 168, 47, 48, 73, 182, "-", 244, 142);

      $testLine = Utility::splitLineIntoArray($line);
      $testLine2 = Utility::splitLineIntoArray($line2);

      if ($testLine == $expected) {
        echo "testSplitLinetoArray Line expected and actual match" . "\n";
      } else {
        echo "testSplitLinetoArray Line expected and actual DO NOT match" . "\n";
      }

      if ($testLine2 == $expected2) {
        echo "testSplitLinetoArray Line2 expected and actual match" . "\n";
      } else {
        echo "testSplitLinetoArray Line2 expected and actual DO NOT match" . "\n";
      }
    }

    public function testRemoveHyphenFromArray() {
      $array = array("1.", "Everton", 104, 4062, 1652, 998, 1412, 6373, "-", 5719, 4302);
      $expected = array("1.", "Everton", 104, 4062, 1652, 998, 1412, 6373, 5719, 4302);
 
      $array2 = array("49.", "Brighton", "&", "Hove", "Albion", 4, 168, 47, 48, 73, 182, "-", 244, 142);
      $expected2 = array("49.", "Brighton", "&", "Hove", "Albion", 4, 168, 47, 48, 73, 182, 244, 142);

      $testArray = Utility::removeHyphenFromArray($array);
      $testArray2 = Utility::removeHyphenFromArray($array2);
      
      if ($testArray == $expected) {
        echo "testRemoveHyphenFromArray Array expected and actual match" . "\n";
      } else {
        echo "testRemoveHyphenFromArray Array expected and actual DO NOT match" . "\n";
      }

      if ($testArray2 == $expected2) {
        echo "testRemoveHyphenFromArray Array2 expected and actual match" . "\n";
      } else {
        echo "testRemoveHyphenFromArray Array2 expected and actual DO NOT match" . "\n";
      }      
    }

    public function testArrayWithoutName() {
      $array = array("1.", "Everton", 104, 4062, 1652, 998, 1412, 6373, 5719, 4302);
      $expected = array("1.", 104, 4062, 1652, 998, 1412, 6373, 5719, 4302);

      $array2 = array("49.", "Brighton", "&", "Hove", "Albion", 4, 168, 47, 48, 73, 182, 244, 142);
      $expected2 = array("49.", 4, 168, 47, 48, 73, 182, 244, 142);

      $testArray = Utility::arrayWithoutName($array);
      $testArray2 = Utility::arrayWithoutName($array2);

      if ($testArray == $expected) {
        echo "testArrayWithoutName Array expected and actual match" . "\n";
      } else {
        echo "testArrayWithoutName Array expected and actual DO NOT match" . "\n";
      }  

      if ($testArray2 == $expected2) {
        echo "testArrayWithoutName Array2 expected and actual match" . "\n";
      } else {
        echo "testArrayWithoutName Array2 expected and DO NOT match" . "\n";
      }
    }

    public function testPrefixHundredSpacing() {
      $number = 100;
      $expected = 0;

      $number2 = 8;
      $expected2 = 2;

      $number3 = 55;
      $expected3 = 1;

      $number4 = 1001;
      $expected4 = "This number is too large for this function, use prefixThousandSpacing";

      $testNumber = Utility::prefixHundredSpacing($number);
      $testNumber2 = Utility::prefixHundredSpacing($number2);
      $testNumber3 = Utility::prefixHundredSpacing($number3);
      $testNumber4 = Utility::prefixHundredSpacing($number4);

      if ($testNumber == $expected) {
        echo "testPrefixHundredSpacing Number expected and actual match" . "\n";
      } else {
        echo "testPrefixHundredSpacing Number expected and actual DO NOT match" . "\n";
      }

      if ($testNumber2 == $expected2) {
        echo "testPrefixHundredSpacing Number2 expected and actual match" . "\n";
      } else {
        echo "testPrefixHundredSpacing Number2 expected and actual DO NOT match" . "\n";
      }

      if ($testNumber3 == $expected3) {
        echo "testPrefixHundredSpacing Number3 expected and actual match" . "\n";
      } else {
        echo "testPrefixHundredSpacing Number3 expected and actual DO NOT match" . "\n";
      }

      if ($testNumber4 == $expected4) {
        echo "testPrefixHundredSpacing Number4 expected and actual match" . "\n";
      } else {
        echo "testPrefixHundredSpacing Number4 expected and actual DO NOT match" . "\n";
      }
    }

    public function testPrefixThousandSpacing() {
      $number = 2222;
      $expected = 0;

      $number2 = 350;
      $expected2 = 1;

      $number3 = 55;
      $expected3 = 2;

      $number4 = 7;
      $expected4 = 3;

      $number5 = 10000;
      $expected5 = "This number is too large for this function, use make a new function";

      $testNumber = Utility::prefixThousandSpacing($number);
      $testNumber2 = Utility::prefixThousandSpacing($number2);
      $testNumber3 = Utility::prefixThousandSpacing($number3);
      $testNumber4 = Utility::prefixThousandSpacing($number4);
      $testNumber5 = Utility::prefixThousandSpacing($number5);

      if ($testNumber == $expected) {
        echo "testPrefixThousandSpacing Number expected and actual match" . "\n";
      } else {
        echo "testPrefixThousandSpacing Number expected and actual DO NOT match" . "\n";
      }

      if ($testNumber2 == $expected2) {
        echo "testPrefixThousandSpacing Number2 expected and actual match" . "\n";
      } else {
        echo "testPrefixThousandSpacing Number2 expected and actual DO NOT match" . "\n";
      }

      if ($testNumber3 == $expected3) {
        echo "testPrefixThousandSpacing Number3 expected and actual match" . "\n";
      } else {
        echo "testPrefixThousandSpacing Number3 expected and actual DO NOT match" . "\n";
      }

      if ($testNumber4 == $expected4) {
        echo "testPrefixThousandSpacing Number4 expected and actual match" . "\n";
      } else {
        echo "testPrefixThousandSpacing Number4 expected and actual DO NOT match" . "\n";
      }

      if ($testNumber5 == $expected5) {
        echo "testPrefixThousandSpacing Number4 expected and actual match" . "\n";
      } else {
        echo "testPrefixThousandSpacing Number4 expected and actual DO NOT match" . "\n";
      }
    }

    public function testWhitespacing() {
      $club = new Club("Test Club", 104, 4062, 1652, 998, 1412, 6373, 5719, 950);
      
      $columnName = "name";
      $expected = array(1, 18);

      $columnName2 = "seasons";
      $expected2 = array(0, 2);

      $columnName3 = "points";
      $expected3 = array(1, 2);

      $testColumnName = Utility::whitespacing($columnName, $club);
      $testColumnName2 = Utility::whitespacing($columnName2, $club);
      $testColumnName3 = Utility::whitespacing($columnName3, $club);

      if ($testColumnName == $expected) {
        echo "testWhitespacing Array expected and actual match" . "\n";
      } else {
        echo "testWhitespacing Array expected and actual DO NOT match" . "\n";
      }

      if ($testColumnName2 == $expected2) {
        echo "testWhitespacing Array2 expected and actual match" . "\n";
      } else {
        echo "testWhitespacing Array2 expected and actual DO NOT match" . "\n";
      }

      if ($testColumnName3 == $expected3) {
        echo "testWhitespacing Array3 expected and actual match" . "\n";
      } else {
        echo "testWhitespacing Array3 expected and actual DO NOT match" . "\n";
      }
    }

    public function testMakeLineReady() {
      $line =  " 1. Everton                    104  4062  1652   998  1412  6373 - 5719  4302     ";
      $expectedArray = array("totalSeasons"=>104, "totalGames"=>4062, "totalWins"=>1652, "totalDraws"=>998, "totalLoses"=>1412, "totalGoals"=>6373, "totalGoalsConceded"=>5719, "totalPoints"=>4302);

      $testArray = Utility::makeLineReady($line);

      if ($testArray == $expectedArray) {
        echo "testMakeLineReady Array expected and actual match" . "\n";
      } else {
        echo "testMakeLineReady Array expected and actual DO NOT match" . "\n";
      }
    }

    public function runTests() {
      UtilityTest::testRemoveExcessWhite();
      UtilityTest::testGetName();
      UtilityTest::testSplitLineToArray();
      UtilityTest::testRemoveHyphenFromArray();
      UtilityTest::testArrayWithoutName();
      UtilityTest::testPrefixHundredSpacing();
      UtilityTest::testPrefixThousandSpacing();  
      UtilityTest::testWhitespacing();
      UtilityTest::testMakeLineReady();
    }
  }

  // Run Tests
  // Uncomment Lines below to run tests on classes
  // UtilityTest::runTests();
  // ClubTest::runTests();

  Main::start();
?>