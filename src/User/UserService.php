<?php

namespace CultuurNet\UiTPASBeheer\User;

use CultuurNet\UiTIDProvider\User\User;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use ValueObjects\Web\EmailAddress;

class UserService implements UserServiceInterface
{
    /**
     * @var \ICultureFeed
     */
    private $cultureFeed;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    /**
     * @param \ICultureFeed $cultureFeed
     * @param CounterConsumerKey $counterConsumerKey
     */
    public function __construct(
        \ICultureFeed $cultureFeed,
        CounterConsumerKey $counterConsumerKey
    ) {
        $this->cultureFeed = $cultureFeed;
        $this->counterConsumerKey = $counterConsumerKey;
    }

    /**
     * @param EmailAddress $email
     *
     * @return User
     *
     * @throws UserNotFoundException
     *   When no user was found for the given email address.
     */
    public function getUserByEmail(EmailAddress $email)
    {
        $query = new \CultureFeed_SearchUsersQuery();
        $query->mbox = $email->toNative();
        $query->mboxIncludePrivate = true;

        /* @var \CultureFeed_ResultSet $results */
        $results = $this->cultureFeed->searchUsers($query);

        $objects = $results->objects;
        if (empty($objects)) {
            throw new UserNotFoundException('email', $email->toNative());
        }

        /* @var \CultureFeed_SearchUser $cfSearchUser */
        $cfSearchUser = reset($objects);

        $cfUser = new User();
        $cfUser->id = $cfSearchUser->id;
        $cfUser->nick = $cfSearchUser->nick;

        return $cfUser;
    }
}
