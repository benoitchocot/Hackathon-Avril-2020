<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 15:38
 * PHP version 7
 */

namespace App\Controller;

use App\Model\MuseumManager;
use App\Model\RoomManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 *
 */
abstract class AbstractController
{
    /**
     * @var Environment
     */
    protected $twig;


    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        $status = session_status();
        if ($status == PHP_SESSION_NONE) {
            session_start();
        }

        $museumManager=new MuseumManager();

        if (!empty($_POST['login_name'])) {
            $_SESSION['login_name'] = $_POST['login_name'];
            header("Location:/game/index");
        }

        if (empty($_SESSION['arts'])) {
            $_SESSION['arts']=array();
            $roomManager = new RoomManager();
            $roomNumbers = $roomManager->getRoomNumbers();
            $getID = $museumManager->getIdFromDpt(count($roomNumbers));

            $countID=count($getID);

            for ($i=0; $i<$countID; $i++) {
                $roomNumber = $roomNumbers[$i];
                $artwork = $getID[$i];
                $_SESSION['arts'][$roomNumber]=$artwork;
            }
        }

        if (empty($_SESSION['goal'])) {
            $_SESSION['goal'] = array_rand($_SESSION['arts'], 1);
        }

        if (empty($_SESSION['target'])) {
            $targetId=$_SESSION['arts'][$_SESSION['goal']];
            $targetData=$museumManager->getObject($targetId);
            $_SESSION['target']=$targetData['primaryImageSmall'];
        }

        if (empty($_SESSION['login_name'])) {
            $_SESSION['login_name']='Wilder';
        }

        if (empty($_SESSION['exit'])) {
            $random = rand(0, 1);
            if ($random < 0.5) {
                $_SESSION['exit'] = 135;
            } else {
                $_SESSION['exit'] = 100;
            }
        }

        if (empty($_SESSION['roundCount'])) {
            $_SESSION['roundCount']=0;
            $_SESSION['120times']=0;
            $_SESSION['start']=0;
            $_SESSION['pocket']="";
            $_SESSION['objectTaken'] = false;
        }

        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => !APP_DEV,
                'debug' => APP_DEV,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
    }
}
