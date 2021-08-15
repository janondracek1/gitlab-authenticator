<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type;

use App\Helper\GitLab\Type\SubType\UserGroup;
use App\Helper\GitLab\Type\SubType\UserProject;

class User extends BaseType
{

	//API properties
	public int $id;
	public string $name;
	public string $username;

	//Computed properties
	/** @var UserGroup[] */
	protected array $groups = [];
	/** @var UserProject[] */
	protected array $projects = [];

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	public function addProject(UserProject $project): self
	{
		$this->projects[] = $project;
		return $this;
	}

	/**
	 * @return UserProject[]
	 */
	public function getProjects(): array
	{
		return $this->projects;
	}

	public function addGroup(UserGroup $group): self
	{
		$this->groups[] = $group;
		return $this;
	}

	/**
	 * @return UserGroup[]
	 */
	public function getGroups(): array
	{
		return $this->groups;
	}
}