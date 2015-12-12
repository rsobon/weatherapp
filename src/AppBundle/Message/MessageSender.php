<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 12.12.15
 * Time: 11:48
 */

namespace AppBundle\Message;


/**
 * This service is supposed to send external messages (like push notifications) with weather updates
 *
 * Class MessageSender
 * @package AppBundle\Message
 */
class MessageSender
{
    /**
     * @param array $message_array
     */
    public function SendMessage(array $message_array)
    {
        foreach ($message_array as $key => $value) {
            if ($value) {
                printf($value . "\n");
            }
        }
    }
}