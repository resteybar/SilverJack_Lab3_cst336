<?php
    
    // Step 1: Retrieve Cards
    function initArrayWithDeck()
    {
        $deck = array();
        
        for($i = 0; $i < 52; $i++)
        {
            $temp = array(
            'points'=> 0,
            'card' =>'');
            $suit = floor(($i)/13);
            switch($suit)
            {
                case 0:
                    $temp['card'] = "./img/clubs/";
                    break;
                case 1:
                    $temp['card'] = "./img/diamonds/";
                    break;
                case 2:
                    $temp['card'] = "./img/hearts/";
                    break;
                case 3:
                    $temp['card'] = "./img/spades/";
                    break;
            }
            $cardNumber = (($i+1)%13);
            if($cardNumber == 0)
            {
                $cardNumber = 13;
            }
            $temp['card'] = $temp['card'] . $cardNumber . ".png";
            $temp['points'] = $cardNumber;
            array_push($deck,$temp);
        }
        return $deck;
    }

    function printCards($cards)
    {
        $i = 0;
        for($i = 0; $i < 52; $i++)
        {
            echo "<img src ='". $cards[$i]['card']."' /> <br/>" ;
        }
    }

    // Step 3: Prints Players Names, Pics, Points
    function printGameState($allPlayer)
    {
        echo "<div id = game>";
        echo "<table>";
        foreach($allPlayer as $player)
        {
            echo "<tr>";
            echo "<th>";
            echo "<img src ='".$player['imgURL']."' /> <br/>" ;
            echo "<div id=boxName>";
            echo $player['name'] . "<br/>";
            echo "</div>";
            echo "</th>";
            echo "<th>";
            foreach($player['hand'] as $card)
            {
                echo "<img src ='". $card ."' />" ;
            }
            echo "</th>";
            echo "<th id = points>";
            echo $player['points'] . "</br>";
            echo "</th>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    
    // Step 2: Retrieves Cards & Points from Cards
    //  - also, returns remaining cards
    function getHand($deck)
    {
        $ans = array(
            'playerHand' => array(),
            'playerPoints' => 0,
            'restOfcards' => array()
            );
        while($ans['playerPoints'] < 37)
        {
            $temp = array_pop($deck);
            
            if(count($ans) > 10) // Limit for amount of cards to pull - Reason: Display of Points will move down
                break;
                
            array_push($ans['playerHand'], $temp['card']);
            $ans['playerPoints'] =  $ans['playerPoints'] + $temp['points'];
        }
        $ans['restOfcards'] = $deck;
        return $ans;
    }
    
    function play()
    {
        $images = array('./img/user_img/Gilbert.jpg','./img/user_img/raymond.png','./img/user_img/Brian.png','./img/user_img/Daniel.jpg');
        shuffle($images);
        $player1 = array(
            'name'=>'Daniel',
            'imgURL' => '',
            'hand' => array(),
            'points' => 0
            );
        $player2 = array(
            'name'=>'Gilbert',
            'imgURL' => '',
            'hand' => array(),
            'points' => 0);
        $player3 = array(
            'name'=>'Raymond',
            'imgURL' => '',
            'hand' => array(),
            'points' => 0);
        $player4 = array(
            'name'=>'Brian',
            'imgURL' => '',
            'hand' => array(),
            'points' => 0
            );
         $player1['imgURL']= $images[count($images)-1];
         array_pop($images);
        $player2['imgURL']= $images[count($images)-1];
         array_pop($images);
         $player3['imgURL']= $images[count($images)-1];
         array_pop($images);
        $player4['imgURL']= $images[count($images)-1];
         array_pop($images);
                
        $allPlayer = array($player1, $player2, $player3, $player4);
        $deck = initArrayWithDeck();
        shuffle($deck);
        for($i = 0; $i < 4; $i++)
        {
            $temp = getHand($deck);
            $deck = $temp['restOfcards'];
            $allPlayer[$i]['hand'] = $temp['playerHand'];
            $allPlayer[$i]['points'] = $temp['playerPoints'];
            
        }
        
        shuffle($allPlayer);//Randomize Players before
        printGameState($allPlayer);//Print out the Game
        $winners=findWinner($allPlayer);//Determines the names of the winners
        $totalPoints=pointsEarned($allPlayer,$winners);//Calculates the winners points
        printWinner($winners,$totalPoints);//Prints out the Winners with points earned or prints out no winners
    }
    
    function findWinner($allPlayer){
        $winnersNum = $winnersMax = $winners = $points = array();
        $pointWinner = 0;
        $i =0;
        $j=0;
        
        //Inserts all the points from players into an array
        foreach($allPlayer as $player){
            if($player['points']<=42){
                $points[$i] = $player['points'];
            }
            $i++;
        }
        
        //Pushes players' name with 42 points into an array that holds the names of the winners
        foreach($allPlayer as $player){
            if($points[$j]==42){
                $winnersMax[]=$player['name'];
                unset($points[$j]);
            }
            $j++;
        }
        
        if(count($points)>0){ //Fixes no winner bug
            $pointWinner= max($points); //Finds the max of the array(excluding the winners' points and players' points above 42)
            
            //Finds the player with the winning points and pushes the player's name into an array
            foreach($allPlayer as $player){
                if($player['points'] == $pointWinner){
                    $winnersNum[]=$player['name'];
                }
            }
            
            //Returns the winners if players earn 42
            if(count($winnersMax)>0){
                for($i = 0;$i<count($winnersMax);$i++){
                    $winners[$i] = $winnersMax[$i];
                }
                return $winners;
            }
            //Returns the winners with points under 42
            if(count($winnersNum)>0){
                for($i = 0;$i<count($winnersNum);$i++){
                    $winners[$i]=$winnersNum[$i];
                }
                return $winners;
            }
        }
    }
    
    function pointsEarned($allPlayer,$winners){
        $losersPoints = $straightPoints = $checkPoints= array();
        $totalPoints=0;
        
        //Pushes the points in seperate array depending on the range of the value
        foreach($allPlayer as $player){
            if($player['points']==42){array_push($straightPoints, $player['points']);}
            elseif($player['points']<42){array_push($checkPoints, $player['points']);}
            else{$totalPoints += intval($player['points']);}
        }
        
        //if $straightPoints(Holds the winning number 42) has elements, then push the remaining points in the $loserPoints array
        if(count($straightPoints)>0){
            for($i=0;$i<=count($checkPoints);$i++){
                array_push($losersPoints, $checkPoints[$i]);
            }
            //Add up all losers' points into a placeholder called $totalPoints
            for($z=0;$z<=count($losersPoints);$z++){
                $totalPoints+=intval($losersPoints[$z]);
            }
            return $totalPoints;
        }
        if(count($checkPoints)>0){//Fixes no winner bug
            $pointWinner= max($checkPoints); //Gets the max of the array $checkPoints
            for($i = 0;$i<=count($checkPoints);$i++){
                //Checks if the array has the winning point
                if($checkPoints[$i]==$pointWinner){
                    unset($checkPoints[$i]); //Deletes the winning number from the array
                }
                array_push($losersPoints, $checkPoints[$i]); // Pushes the remaining points into an array that holds the losers' points
            }
            //Adds up all losers' points
            for($z=0;$z<=count($losersPoints);$z++){
                $totalPoints+=intval($losersPoints[$z]);
            }
            return $totalPoints;
        }
    }
    
    function printWinner($winners,$totalPoints){
        //If there is no winners echo No Winners
        if(count($winners)==0){
            echo "<h2>No Winners!</h2>";
        }
        //Print out all the winner(s) with points earned 
        else{
            echo "<h2>";
            for($i = 0; $i<count($winners);$i++){
                echo $winners[$i] . ", ";
            }
            if(count($winners) > 1){
                echo " each wins " . $totalPoints . " points!!!</h2>";
            }
            else{
                echo " wins " . $totalPoints . " points!!!</h2>";
            }
        }
    }
?>