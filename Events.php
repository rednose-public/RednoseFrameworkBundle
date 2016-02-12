<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle;

final class Events
{
    /**
     * This event occurs after a user is logged in
     * The listener receives a Rednose\FrameworkBundle\Event\UserEvent instance.
     */
    const USER_LOGIN = 'rednose_framework.user.login';

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
     * This event occurs when a user was automatically created.
     * The listener receives a Rednose\FrameworkBundle\Event\UserEvent instance.
     */
    const USER_AUTO_CREATE = 'rednose_framework.user.auto_create';

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

    /**
     * This event occurs after a new organization has been persisted to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\OrganizationEvent instance.
     */
    const ORGANIZATION_POST_PERSIST = 'rednose_framework.organization.post_persist';

    /**
     * This event occurs after an existing organization has been updated to the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\OrganizationEvent instance.
     */
    const ORGANIZATION_POST_UPDATE = 'rednose_framework.organization.post_update';

    /**
     * This event occurs after an organization has been removed from the backend.
     * The listener receives a Rednose\FrameworkBundle\Event\OrganizationEvent instance.
     */
    const ORGANIZATION_POST_REMOVE = 'rednose_framework.organization.post_remove';
}