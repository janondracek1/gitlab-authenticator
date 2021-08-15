<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type\SubType;

use App\Helper\GitLab\Type\Project;
use App\Helper\GitLab\Type\User;

class UserProject extends BaseSubType
{

	protected User $user;
	protected Project $project;

	public function __construct( array $values, User $user, Project $project )
	{
		$this->user = $user;
		$this->project = $project;
		parent::__construct( $values );
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @return Project
	 */
	public function getProject(): Project
	{
		return $this->project;
	}

}