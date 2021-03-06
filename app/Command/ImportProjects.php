<?php

declare(strict_types=1);

namespace App\Command;

use App\Helper\GitLab\Api;
use App\Helper\GitLab\Type\Group;
use App\Helper\GitLab\Type\SubType\UserGroup;
use App\Helper\GitLab\Type\SubType\UserProject;
use App\Helper\GitLab\Type\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProjects extends Command
{

    const INPUT_ARGUMENT_GROUP_ID = 'groupId';
    protected static $defaultName = 'gitlab:import';
    protected static $defaultDescription = 'Imports data about projects';

    protected Api $gitLabApi;

    /**
     * ImportProjects constructor.
     * @param Api $gitLabApi
     */
    public function __construct(Api $gitLabApi)
    {
        $this->gitLabApi = $gitLabApi;
        parent::__construct(self::$defaultName);
    }

    public function configure()
    {
        $this
            ->addArgument(self::INPUT_ARGUMENT_GROUP_ID, InputArgument::REQUIRED, 'ID of required group');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $groupId = (int)$input->getArgument(self::INPUT_ARGUMENT_GROUP_ID);
        $groupArray = [];
        $groupType = $this->gitLabApi->getGroupById($groupId);
        if (!$groupType instanceof Group) {
            $output->writeln('Group with ID '.$groupId.' not found, check /log/'.Api::LOG_SEVERITY.'.log for further information');

            return self::FAILURE;
        }
        $arraySubGroup[] = $groupType;
        $this->gitLabApi->getGroupsRecursive($groupType, $arraySubGroup);
        foreach ($arraySubGroup as $subGroup) {
            $groupArray[$subGroup->getId()] = $subGroup;
        }

        $projectArray = [];
        foreach ($groupArray as $group) {
            $groupProjectArray = $this->gitLabApi->getProjectsOfGroup($group);
            foreach ($groupProjectArray as $project) {
                $projectArray[$project->getId()] = $project;
            }
        }

        /** @var User[] $userArray */
        $userArray = [];
        foreach ($projectArray as $project) {
            $userProjectArray = $this->gitLabApi->getMembersOfProject($project);
            foreach ($userProjectArray as $userProject) {
                $userType = $userProject->getUser();
                if (!isset($userArray[$userType->getId()])) {
                    $userArray[$userType->getId()] = $userType;
                }
                $userArray[$userType->getId()]->addProject($userProject);
            }
        }

        foreach ($groupArray as $group) {
            $userGroupArray = $this->gitLabApi->getMembersOfGroup($group);
            foreach ($userGroupArray as $userGroup) {
                $userType = $userGroup->getUser();
                if (!isset($userArray[$userType->getId()])) {
                    $userArray[$userType->getId()] = $userType;
                }
                $userArray[$userType->getId()]->addGroup($userGroup);
            }
        }

        foreach ($userArray as $user) {
            $this->outputUser($output, $user);
            $output->writeln('');
        }

        $output->writeln('Total users: ' . count($userArray));

        return self::SUCCESS;
    }

    protected function outputUser(OutputInterface $output, User $user): void
    {
        $output->writeln(sprintf('%s (@%s)', $user->getName(), $user->getUsername()));

        $arrayGroupString = array_map(function (UserGroup $userGroup) {
            return sprintf('%s (%s)', $userGroup->getGroup()->getFullPath(), $userGroup->getAccessLevelTranslated());
        },
            $user->getGroups()
        );
        $output->writeln(sprintf('Groups: [%s]', implode(', ', $arrayGroupString)));

        $arrayProjectString = array_map(function (UserProject $userGroup) {
            return sprintf(
                '%s (%s)',
                $userGroup->getProject()->getPathWithNamespace(),
                $userGroup->getAccessLevelTranslated()
            );
        },
            $user->getProjects()
        );
        $output->writeln(sprintf('Projects: [%s]', implode(', ', $arrayProjectString)));
    }


}