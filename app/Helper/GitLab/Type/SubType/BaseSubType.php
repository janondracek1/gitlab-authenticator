<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type\SubType;

use App\Helper\GitLab\Type\BaseType;

abstract class BaseSubType extends BaseType
{

	public int $accessLevel;

	public const ACCESS_LEVEL_VALUE_NO_ACCESS = 0;
	public const ACCESS_LEVEL_VALUE_MINIMAL_ACCESS = 5;
	public const ACCESS_LEVEL_VALUE_GUEST = 10;
	public const ACCESS_LEVEL_VALUE_REPORTER = 20;
	public const ACCESS_LEVEL_VALUE_DEVELOPER = 30;
	public const ACCESS_LEVEL_VALUE_MAINTAINER = 40;
	public const ACCESS_LEVEL_VALUE_OWNER = 50;

	public const ACCESS_LEVEL_TRANSLATION_MAP = [
		self::ACCESS_LEVEL_VALUE_NO_ACCESS => 'No access',
		self::ACCESS_LEVEL_VALUE_MINIMAL_ACCESS => 'Minimal access',
		self::ACCESS_LEVEL_VALUE_GUEST => 'Guest',
		self::ACCESS_LEVEL_VALUE_REPORTER => 'Reporter',
		self::ACCESS_LEVEL_VALUE_DEVELOPER => 'Developer',
		self::ACCESS_LEVEL_VALUE_MAINTAINER => 'Maintainer',
		self::ACCESS_LEVEL_VALUE_OWNER => 'Owner',
	];

	/**
	 * @return int
	 */
	public function getAccessLevel(): int
	{
		return $this->accessLevel;
	}

	public function getAccessLevelTranslated(): string
	{
		return self::ACCESS_LEVEL_TRANSLATION_MAP[$this->accessLevel];
	}

}