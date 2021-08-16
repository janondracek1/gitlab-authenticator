<?php

declare(strict_types=1);

namespace App\Helper\GitLab;

use App\Helper\GitLab\Type\Group;
use App\Helper\GitLab\Type\Project;
use App\Helper\GitLab\Type\SubType\UserGroup;
use App\Helper\GitLab\Type\SubType\UserProject;
use App\Helper\GitLab\Type\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Tracy\Debugger;

class Api
{
    public const LOG_SEVERITY = 'api';
    public const ENTRIES_PER_PAGE = 50;

    protected Client $client;
    protected array $defaultParams = [];

    /**
     * ImportProjects constructor.
     */
    public function __construct(string $baseUrl, string $accessToken)
    {
        $this->client = new Client(
            [
                'base_uri' => $baseUrl,
            ]
        );
        $this->defaultParams[RequestOptions::HEADERS]['Authorization'] = 'Bearer ' . $accessToken;
    }

    /**
     * Returns JSON decoded response from the API
     *
     * @param string $method
     * @param string $url
     * @param array $additionalParams
     * @return array
     */
    protected function sendRequest(string $method, string $url, array $additionalParams = []): array
    {
        try {
            return json_decode(
                $this->client->request($method, $url, array_merge($this->defaultParams, $additionalParams))->getBody()
                    ->getContents(),
                true
            );
        } catch (GuzzleException $ex) {
            Debugger::log(__METHOD__ . '() - ' . $ex->getMessage(), self::LOG_SEVERITY);

            return [];
        }
    }

    /**
     * @param Group $group
     * @return UserGroup[]
     */
    public function getMembersOfGroup(Group $group): array
    {
        $arrayResponse = $this->sendRequest('GET', 'groups/' . $group->getId() . '/members');
        $return = [];
        foreach ($arrayResponse as $user) {
            $userType = new User($user);
            $return[] = new UserGroup($user, $userType, $group);
        }

        return $return;
    }

    /**
     * @param Project $project
     * @return UserProject[]
     */
    public function getMembersOfProject(Project $project): array
    {
        $arrayResponse = $this->sendRequest('GET', 'projects/' . $project->getId() . '/members');
        $return = [];
        foreach ($arrayResponse as $user) {
            $userType = new User($user);
            $return[] = new UserProject($user, $userType, $project);
        }

        return $return;
    }

    /**
     * @param int $groupId
     * @return Group|null
     */
    public function getGroupById(int $groupId): ?Group
    {
        $arrayResponse = $this->sendRequest('GET', 'groups/' . $groupId);
        if (empty($arrayResponse)) {
            return null;
        }

        return new Group($arrayResponse);
    }

    /**
     * @param Group $group
     * @param array $groupArray
     * @param int $pageNumber
     * @return Group[]
     */
    public function getGroupsRecursive(Group $group, array &$groupArray = [], int $pageNumber = 1): array
    {
        do {
            $url = sprintf(
                'groups/%s/subgroups?per_page=%s&page=%s',
                $group->getId(),
                self::ENTRIES_PER_PAGE,
                $pageNumber
            );
            $arrayResponse = $this->sendRequest('GET', $url);

            foreach ($arrayResponse as $subGroup) {
                $subGroupType = new Group($subGroup);
                $groupArray[] = $subGroupType;
                $this->getGroupsRecursive($subGroupType, $groupArray);
            }

            $pageNumber++;
        } while (!empty($arrayResponse));

        return $groupArray;
    }

    /**
     * @param Group $group
     * @param int $pageNumber
     * @return Project[]
     */
    public function getProjectsOfGroup(Group $group, int $pageNumber = 1): array
    {
        $return = [];
        do {
            $url = sprintf(
                'groups/%s/projects?per_page=%s&page=%s',
                $group->getId(),
                self::ENTRIES_PER_PAGE,
                $pageNumber
            );
            $arrayResponse = $this->sendRequest('GET', $url);
            foreach ($arrayResponse as $project) {
                $return[] = new Project($project);
            }
            $pageNumber++;
        } while (!empty($arrayResponse));

        return $return;
    }

}