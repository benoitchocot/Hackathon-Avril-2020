<?php


namespace App\Controller;

use App\Model\MuseumManager;
use App\Model\RoomManager;

class GameController extends AbstractController
{
    /**
     * Display home page
     *
     * @param null $roomNumber
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index($roomNumber = null)
    {
        $_SESSION['roundCount']++;
        $messages = array();

        $roomManager = new RoomManager();
        $museumManager = new MuseumManager();

        if (empty($roomNumber)) {
            $roomNumbers = $roomManager->getRoomNumbers();
            $roomNumber = $roomNumbers[rand(0, count($roomNumbers)-1)];
            $_SESSION['start']=$roomNumber;
        }

        $objectId = $_SESSION['arts'][$roomNumber];
        $objectData = $museumManager->getObject($objectId);

        //needs to be after $objectData calculation and after $roomNumber
        $messages[]=$this->getMessage($roomNumber, $objectData);

        if ($_SESSION['goal'] == $roomNumber) {
            $_SESSION['objectTaken'] = true;
            $_SESSION['pocket']=$objectData['primaryImageSmall'];
            $messages[] = 'You got the object, find exit to get out';
        }

        if ($_SESSION['objectTaken'] == true && $roomNumber == $_SESSION['exit']) {
            /*header('location: /./home/replay');*/
            header("Refresh:10; url=/./home/replay", true, 303);
        }

        $accessibleRooms = $roomManager->getAccessibleRooms($roomNumber);

        if ($roomNumber==120) {
            $_SESSION['120times']++;
        }

        return $this->twig->render('Game/index.html.twig', ['accessibleRooms' => $accessibleRooms,
            'login_name' => $_SESSION['login_name'],
            'roomNumber' => $roomNumber,
            'objectData' => $objectData,
            'messages' => $messages,
            'target' => $_SESSION['target'],
            'pocket'=>$_SESSION['pocket']]);
    }

    private function getmessage($roomNumber, $objectData = null) : string
    {
        if ($roomNumber==$_SESSION['start']) {
            return "You got back to your starting point. You were stuck at home, now you are stuck at the museum.";
        }

        if ($roomNumber==$_SESSION['exit']) {
            return "You were about to get out using the main exit. But you notice a tile is 
            moving on the floor and you discover your trainers, Sylvis and Louain getting out 
            of a tunnel. After some congratulations, they help you to get out of the museum and you get a badge.";
        }

        if ($roomNumber==$_SESSION['goal'] && $_SESSION['objectTaken']==false) {
            return "You eventually discover the artwork you were searching for. Strangely, it represent something with 
            a surgical mask. They really wants to preserve their art here...";
        }

        if ($roomNumber==120 && $_SESSION['120times']>=3) {
            return "That's really strange to come here is this corner so many times. Drop it, I will tell 
            you a secret : go to the room number 103 to get it.";
        }

        return $this->getMessage2($roomNumber, $objectData);
    }

    private function getMessage2($roomNumber, $objectData = null) : string
    {
        if ($roomNumber==103 && $_SESSION['120times']>=3) {
            return "You really want to know why I am so wonderfull ? I was programmed by Adrien MAILLARD, Mao MATTER, 
            Benoit CHOCOT and Olivier MONSIRE ! That's so obvious.";
        }

        if ($roomNumber==102) {
            return "".$_SESSION['objectTaken']." ".$_SESSION['goal']." ".$_SESSION['exit'];
        }

        $class = trim($objectData['classification']);
        if (strlen($class)<2) {
            $class="...actuallay I do not know what it is. ";
        }

        $period = trim($objectData['period']);
        if (strlen($period)<2) {
            $period = "...I am not sur when it was made";
        }

        return "You get in the room and discover the astonishing ". $objectData['title']. '.
                It\'s a kind of ' .$class .'. But it is from '.$period ;
    }
}
