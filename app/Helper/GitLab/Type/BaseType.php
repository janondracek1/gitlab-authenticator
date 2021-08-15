<?php

declare(strict_types=1);

namespace App\Helper\GitLab\Type;


use Tracy\Debugger;
use Tracy\ILogger;

abstract class BaseType
{

	public function __construct(array $values)
	{
		$reflectionClass = new \ReflectionClass($this);
		foreach ($values as $key => $value) {
			$camelCaseKey = str_replace('_', '', ucwords($key, '_'));
			$camelCaseKey = lcfirst($camelCaseKey);

			try {
				$reflectionProperty = $reflectionClass->getProperty($camelCaseKey);
			} catch (\ReflectionException $exception) {
				Debugger::log('Missing ' . $camelCaseKey . ' getter method', ILogger::ERROR);
				continue;
			}

			$getterMethod = $reflectionClass->getMethod('get' . ucfirst($camelCaseKey));
			$valueToSet = $value;
			if(is_null($value)){
				continue;
			}
			$type = $getterMethod->getReturnType()->getName();
			if ($type === \DateTime::class) {
				$valueToSet = new \DateTime($value);
			} elseif ($type === 'int') {
				$valueToSet = (int)$valueToSet;
			}
			$reflectionProperty->setValue($this, $valueToSet);
		}
	}


}