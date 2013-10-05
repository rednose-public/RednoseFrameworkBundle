<?php

namespace Rednose\FrameworkBundle\Notification;

interface NotificationTypeInterface
{
    /**
     * @return string
     */
    public function getMessage();
}