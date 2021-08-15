<?php

declare(strict_types=1);

namespace App\Command;

use App\Helper\GitLab\Api;
use App\Helper\GitLab\Type\SubType\UserGroup;
use App\Helper\GitLab\Type\SubType\UserProject;
use App\Helper\GitLab\Type\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProjects extends Command
{

	protected static $defaultName = 'gitlab:import';
	protected static $defaultDescription = 'Imports data about projects';

	protected Api $gitLabApi;

	/**
	 * ImportProjects constructor.
	 * @param Api $gitLabApi
	 */
	public function __construct( Api $gitLabApi )
	{
		$this->gitLabApi = $gitLabApi;
		parent::__construct(self::$defaultName);
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		//TODO: command arg
		$groupId = 10975505;
		$groupArray = [];
		$groupType = $this->gitLabApi->getGroupById($groupId);
		$arraySubGroup = $this->gitLabApi->getDescendantGroupsOfGroup($groupType);
		foreach( $arraySubGroup as $subGroup){
			$groupArray[$subGroup->getId()] = $subGroup;
		}

		$projectArray = [];
		foreach($groupArray as $group){
			$groupProjectArray = $this->gitLabApi->getProjectsOfGroup($group);
			foreach($groupProjectArray as $project){
				$projectArray[$project->getId()] = $project;
			}
		}

		/** @var User[] $userArray */
		$userArray = [];
		foreach($projectArray as $project){
			$userProjectArray = $this->gitLabApi->getMembersOfProject($project);
			foreach($userProjectArray as $userProject){
				$userType = $userProject->getUser();
				if(!isset($userArray[$userType->getId()])){
					$userArray[$userType->getId()] = $userType;
				}
				$userType->addProject($userProject);
			}
		}

		foreach($groupArray as $group){
			$userGroupArray = $this->gitLabApi->getMembersOfGroup($group);
			foreach($userGroupArray as $userGroup){
				$userType = $userGroup->getUser();
				if(!isset($userArray[$userType->getId()])){
					$userArray[$userType->getId()] = $userType;
				}
				$userType->addGroup($userGroup);
			}
		}

		foreach($userArray as $user){
			$this->outputUser($output, $user);
			$output->writeln('');
		}

		return self::SUCCESS;
	}

	protected function outputUser(OutputInterface $output, User $user): void
	{
		$output->writeln(sprintf('%s (@%s)', $user->getName(), $user->getUsername()));

		$arrayGroupString = array_map(function(UserGroup $userGroup){
			return sprintf('%s (%s)', $userGroup->getGroup()->getFullPath(), $userGroup->getAccessLevelTranslated());
		}, $user->getGroups());
		$output->writeln(sprintf('Groups: [%s]', implode($arrayGroupString)));

		$arrayProjectString = array_map(function(UserProject $userGroup){
			return sprintf('%s (%s)', $userGroup->getProject()->getPathWithNamespace(), $userGroup->getAccessLevelTranslated());
		}, $user->getProjects());
		$output->writeln(sprintf('Projects: [%s]', implode($arrayProjectString)));
	}


}