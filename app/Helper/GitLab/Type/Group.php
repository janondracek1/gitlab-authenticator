<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type;

class Group extends BaseType
{

	public int $id;
	public string $name;
	public string $path;
	public string $fullPath;

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
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getFullPath(): string
	{
		return $this->fullPath;
	}

}