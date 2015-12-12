<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 12.12.15
 * Time: 11:48
 */

namespace AppBundle\Message;


class MessageSender
{
    public function SendMessage($message)
    {
        printf($message . "\n");
    }
}