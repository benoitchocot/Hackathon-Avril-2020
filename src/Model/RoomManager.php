<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class RoomManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'room';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    public function getRoomNumbers(): array
    {
        $rooms=$this->pdo->query('SELECT * FROM ' . $this->table)->fetchAll();

        $roomIds=array();
        foreach ($rooms as $room) {
            $roomIds[]=$room['number'];
        }
        return $roomIds;
    }

    public function getAccessibleRooms(int $roomId): array
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT door.to FROM $this->table 
            JOIN door ON room.number=door.from
            WHERE room.number=:id");
        $statement->bindValue('id', $roomId, \PDO::PARAM_INT);
        $statement->execute();
        $accessibleRooms=$statement->fetchAll();

        $roomIds=array();
        foreach ($accessibleRooms as $room) {
            $roomIds[]=$room['to'];
        }

        return $roomIds;
    }
}
