<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;

class ImportProjects extends Command
{

	protected static $defaultName = 'gitlab:import';
	protected static $defaultDescription = 'Imports data about projects';

}