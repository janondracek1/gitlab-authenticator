<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type;

class Project extends BaseType
{

	public int $id;
	public string $description;
	public string $name;
	public string $path;
	public string $pathWithNamespace;

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
	public function getDescription(): string
	{
		return $this->description;
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
	public function getPathWithNamespace(): string
	{
		return $this->pathWithNamespace;
	}

}