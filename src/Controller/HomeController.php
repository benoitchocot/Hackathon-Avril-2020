<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\MuseumManager;
use App\Model\RoomManager;

class HomeController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        return $this->twig->render('Home/index.html.twig');
    }

    public function replay()
    {

        $message="Congratulations " .$_SESSION['login_name']. ", you have spent ".$_SESSION['roundCount']." hours in the museum.";
        if ($_SESSION['roundCount']<10) {
            $message.=" You are the fastest robber we've ever seen !";
        } elseif ($_SESSION['roundCount']>20) {
            $message.=" You really have to love art to take so many risks.";
        }

        return $this->twig->render('Home/replay.html.twig', ['message' => $message]);
    }

    public function restartFast()
    {
        unset($_SESSION['objectTaken']);
        unset($_SESSION['goal']);
        unset($_SESSION['exit']);
        unset($_SESSION['roundCount']);
        unset($_SESSION['start']);
        unset($_SESSION['120times']);
        unset($_SESSION['login_name']);
        unset($_SESSION['target']);
        header('location:/./home/index');
    }

    public function restart()
    {
        $this->clearSession();
        header('location:/./home/index');
    }

    private function clearSession()
    {
        unset($_SESSION['arts']);
        unset($_SESSION['target']);
        unset($_SESSION['objectTaken']);
        unset($_SESSION['goal']);
        unset($_SESSION['exit']);
        unset($_SESSION['roundCount']);
        unset($_SESSION['start']);
        unset($_SESSION['120times']);
        unset($_SESSION['login_name']);
        session_destroy();
    }
}
