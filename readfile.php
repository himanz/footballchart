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

  $file = fopen("league_list.txt","r");
  $clubsArray = array();
	while(! feof($file))
	  {

		  // fgets($file). "\r\n";
		  $line = fgets($file);
	    
	    // Remove white space at start and end of sentence
	    $trimmed = 	trim($line);

      // Remove all excess white space so that there is only 1 white space seperator
	    $replaced = preg_replace('/\s\s+/', ' ', $trimmed);

      // Stores full name of club in $nameMatch
	    $nameHold = preg_match('/\b[A-Z][A-Za-z \'&]+/', $replaced, $nameMatch);

      // Trim white spacing from name
      $nameMatch[0] = trim($nameMatch[0]);

      // Split the string into cells - aka columns
	    $columns = explode(' ', $replaced);

	    // Remove hyphen from array
	    unset($columns[array_search('-', $columns)]);

      // Re indexes the club columns array
	    $columns = array_values($columns);
      
      // Remove any element relating to name from index
      $newColumns = array();
      foreach ($columns as $each) {
      	if (preg_match('/[A-Za-z&]/', $each) == false) {
          array_push($newColumns, $each);
      	}
      }

      // print_r($newColumns);
      
      // Turn the column into key value pair
      $clubArray = array();

	    $arrayCounter = 0;
	    foreach ($newColumns as $each) {
	    	switch ($arrayCounter):
	    	  // case 1:
        //     $clubArray["name"] = $each;
        //     break;
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

      $club = new Club($nameMatch[0], $clubArray['totalSeasons'], $clubArray['totalGames'], $clubArray['totalWins'], $clubArray['totalDraws'], $clubArray['totalLoses'], $clubArray['totalGoals'], $clubArray['totalGoalsConceded'], $clubArray['totalPoints']);
      
		  array_push($clubsArray, $club);  
		  // print_r($columns);
		  // print_r($clubArray);
		  // print_r($club);
		  // echo $club->seasonAverage;
	  }
    // print_r($myarray);
    
    // Sort club objects array in descending order of season average
    usort($clubsArray, function($a, $b) {
	    return $b->seasonAverage > $a->seasonAverage;
	  });

    foreach ($clubsArray as $club) {
      if ($ordinalCounter < 10) {
        echo " " . $ordinalCounter . "." . $club->displayLine();
      } elseif ($ordinalCounter < 100) {
        echo $ordinalCounter . "." . $club->displayLine();
      }
      $ordinalCounter++;  
    }
    
	  // print_r($myarray);
	fclose($file);
  

?>