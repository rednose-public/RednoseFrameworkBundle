<?php

namespace Rednose\FrameworkBundle;

final class Events
{
    /**
     * This event occurs after a new user has been persisted to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\UserEvent instance.
     */
    const USER_POST_PERSIST = 'rednose_framework.user.post_persist';

    /**
     * This event occurs after an existing user has been updated to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\UserEvent instance.
     */
    const USER_POST_UPDATE = 'rednose_framework.user.post_update';

    /**
     * This event occurs after a user has been removed from the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\UserEvent instance.
     */
    const USER_POST_REMOVE = 'rednose_framework.user.post_remove';

    /**
     * This event occurs after a new group has been persisted to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\GroupEvent instance.
     */
    const GROUP_POST_PERSIST = 'rednose_framework.group.post_persist';

    /**
     * This event occurs after an existing group has been updated to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\GroupEvent instance.
     */
    const GROUP_POST_UPDATE = 'rednose_framework.group.post_update';

    /**
     * This event occurs after a group has been removed from the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\GroupEvent instance.
     */
    const GROUP_POST_REMOVE = 'rednose_framework.group.post_remove';
}