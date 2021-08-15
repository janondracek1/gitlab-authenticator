<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type\SubType;

use App\Helper\GitLab\Type\Group;
use App\Helper\GitLab\Type\User;

class UserGroup extends BaseSubType
{

    protected User $user;
    protected Group $group;

    public function __construct(array $values, User $user, Group $group)
    {
        $this->user = $user;
        $this->group = $group;
        parent::__construct($values);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

}