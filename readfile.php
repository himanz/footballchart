<?php
  // $myfile = fopen("league_list.txt", "r") or die("Unable to open file!");
  $ordinalCounter = 1;

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
      return str_repeat(" ", $this->whitespacing("name")[0]) . $this->name . str_repeat(" ", $this->whitespacing("name")[1]) . 
             str_repeat(" ", $this->whitespacing("seasons")[0]) . $this->seasons . str_repeat(" ", $this->whitespacing("seasons")[1]) .
             str_repeat(" ", $this->whitespacing("games")[0]) . $this->totalGames . str_repeat(" ", $this->whitespacing("games")[1]) .
             str_repeat(" ", $this->whitespacing("wins")[0]) . $this->totalWins . str_repeat(" ", $this->whitespacing("wins")[1]) .
             str_repeat(" ", $this->whitespacing("draws")[0]) . $this->totalDraws . str_repeat(" ", $this->whitespacing("draws")[1]) .
             str_repeat(" ", $this->whitespacing("losses")[0]) . $this->totalLoses . str_repeat(" ", $this->whitespacing("losses")[1]) .
             str_repeat(" ", $this->whitespacing("goals")[0]) . $this->totalGoals . str_repeat(" ", $this->whitespacing("goals")[1]) . "- " .
             str_repeat(" ", $this->whitespacing("goalsCondeded")[0]) . $this->totalGoalsConceded . str_repeat(" ", $this->whitespacing("goalsCondeded")[1]) . 
             str_repeat(" ", $this->whitespacing("points")[0]) . $this->totalPoints . str_repeat(" ", $this->whitespacing("points")[1]) . 
             $this->seasonAverage . "\r\n";
    }

    // returns a 2 element array, first element is white space prefix, second is white space suffix
    public function whitespacing($column) {
      $nameSpace = 28;
      $prefixSpace = 0;
      $suffixSpace = 2;

      if ($column == "name") {
        $prefixSpace = 1;
        $suffixSpace = $nameSpace - strlen($this->name);
      } elseif ($column == "seasons") {
        $prefixSpace = $this->prefixHundredSpacing($this->seasons);
      } elseif ($column == "games") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalGames);
      } elseif ($column == "wins") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalWins);
      } elseif ($column == "draws") {
        $prefixSpace = $this->prefixHundredSpacing($this->totalDraws);
      } elseif ($column == "losses") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalLoses);
      } elseif ($column == "goals") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalGoals);
        $suffixSpace = 1;
      } elseif ($column == "goalsCondeded") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalGoalsConceded);
      } elseif ($column == "points") {
        $prefixSpace = $this->prefixThousandSpacing($this->totalPoints);
      }
      return array($prefixSpace, $suffixSpace);
    }
 
    // use for columns with max value less than 1000
    public function prefixHundredSpacing($number) {
      if ($number < 10) {
        return 2;
      } elseif ($number < 100) {
        return 1;
      } else {
        return 0;
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
      } else {
        return 0;
      }
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

    public function splitLineIntoArray($line) {
      return explode(' ', $line);
    }

    public function removeHyphenFromArray($array) {
      unset($array[array_search('-', $array)]);
      // reindexes the array
      array_values($array);
      return $array;
    }

    // Make new array without the name of club, just stat values
    public function arrayWithoutName($array) {
      $noNameArray = array();
      foreach ($array as $each) {
        if (preg_match('/[A-Za-z&]/', $each) == false) {
          array_push($noNameArray, $each);
        }
      }
      return $noNameArray;
    } 
  }

  class Test {
    public function testRemoveExcessWhite() {
      $line = " 1. Everton                    104  4062  1652   998  1412  6373 - 5719  4302  ";
      $expectedLine = "1. Everton 104 4062 1652 998 1412 6373 - 5719 4302";

      $line2 = "49. Brighton & Hove Albion       4   168    47    48    73   182 -  244   142";
      $expectedLine2 = "49. Brighton & Hove Albion 4 168 47 48 73 182 - 244 142";

      $testLine = Utility::removeExcessWhite($line);
      $testLine2 = Utility::removeExcessWhite($line2);

      if ($testLine == $expectedLine) {
        echo "removeExcessWhite Lines match" . "\r\n";
      } else {
        echo "removeExcessWhite Lines do not match" . "\r\n";
      }

      if ($testLine2 == $expectedLine2) {
        echo "removeExcessWhite Lines 2 match" . "\r\n";
      } else {
        echo "removeExcessWhite Lines 2 do not match" . "\r\n";
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
        echo "getName Line expected and actual match" . "\r\n";
      } else {
        echo "getName Line expected and actual DO NOT match" . "\r\n";
      }

      if ($testLine2 == $expectedLine2) {
        echo "getName Line2 expected and actual match" . "\r\n";
      } else {
        echo "getName Line2 expected and actual DO NOT match" . "\r\n";
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
        echo "testSplitLinetoArray Line expected and actual match" . "\r\n";
      } else {
        echo "testSplitLinetoArray Line expected and actual DO NOT match" . "\r\n";
      }

      if ($testLine2 == $expected2) {
        echo "testSplitLinetoArray Line2 expected and actual match" . "\r\n";
      } else {
        echo "testSplitLinetoArray Line2 expected and actual DO NOT match" . "\r\n";
      }
    }

    public function runTests() {
      // Test::testRemoveExcessWhite();
      // Test::testGetName();
      Test::testSplitLineToArray();    
    }
  }

  Test::runTests();

  $file = fopen("league_list.txt","r");
  $allClubsArray = array();
	while(! feof($file))
	  {

		  // fgets($file). "\r\n";
		  $line = fgets($file);
	    
	    // Remove white space at start and end of sentence
	    $trimmed = 	trim($line);

      // Remove all excess white space so that there is only 1 white space seperator
      $replaced = Utility::removeExcessWhite($trimmed);

      // Stores full name of club in $clubName
      $clubName = Utility::getName($replaced);

      // Split the string into cells - aka columns
	    $columns = Utility::splitLineIntoArray($replaced);

	    // Remove hyphen from array
      $columns = Utility::removeHyphenFromArray($columns);

      $newColumns = Utility::arrayWithoutName($columns);

      // print_r($newColumns);
      
      // Turn the column into key value pair
      $clubArray = Club::turnArrayToHash($newColumns);

      $club = new Club($clubName, $clubArray['totalSeasons'], $clubArray['totalGames'], $clubArray['totalWins'], $clubArray['totalDraws'], $clubArray['totalLoses'], $clubArray['totalGoals'], $clubArray['totalGoalsConceded'], $clubArray['totalPoints']);

		  array_push($allClubsArray, $club);  
		  // print_r($columns);
		  // print_r($clubArray);
		  // print_r($club);
		  // echo $club->seasonAverage;
	  }
    // print_r($myarray);
    
    // Sort club objects array in descending order of season average
    usort($allClubsArray, function($a, $b) {
	    return $b->seasonAverage > $a->seasonAverage;
	  });

    foreach ($allClubsArray as $club) {
      if ($ordinalCounter < 10) {
        $line = " " . $ordinalCounter . "." . $club->displayLine(); 
        echo " " . $ordinalCounter . "." . $club->displayLine();
      } elseif ($ordinalCounter < 100) {
        $line = $ordinalCounter . "." . $club->displayLine(); 
        echo $ordinalCounter . "." . $club->displayLine();
      }
      
    //  Uncomment this if you want a new file with the updated league list ordered by season average 
      // if(!file_put_contents("updated_league_list.txt", $line, FILE_APPEND)){
      //   // failure
      // }

      $ordinalCounter++;  
    }
    
	  // print_r($myarray);
	fclose($file);
  

?>