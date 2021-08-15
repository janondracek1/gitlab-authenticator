<?php


namespace App\Helper\GitLab;


use App\Helper\GitLab\Type\Group;
use App\Helper\GitLab\Type\Project;
use App\Helper\GitLab\Type\SubType\UserGroup;
use App\Helper\GitLab\Type\SubType\UserProject;
use App\Helper\GitLab\Type\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Api
{

	protected Client $client;
	protected $defaultParams = [];

	/**
	 * ImportProjects constructor.
	 */
	public function __construct(string $baseUrl, string $accessToken)
	{
		$this->client = new Client([
			'base_uri' => $baseUrl,
		]);
		$this->defaultParams[RequestOptions::HEADERS]['Authorization'] = 'Bearer '.$accessToken;
	}

	/**
	 * Returns JSON decoded response from the API
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $additionalParams
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function sendRequest(string $method, string $url, array $additionalParams = []): array
	{
		return json_decode($this->client->request($method, $url, array_merge($this->defaultParams, $additionalParams))->getBody()->getContents(), true);
	}

	/**
	 * @param Group $group
	 * @return UserGroup[]
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getMembersOfGroup(Group $group): array
	{
		$arrayResponse = $this->sendRequest('GET', 'groups/'.$group->getId().'/members');
		$return = [];
		foreach($arrayResponse as $user){
			$userType = new User($user);
			$return[] = new UserGroup($user, $userType, $group);
		}
		return $return;
	}

	/**
	 * @param Project $project
	 * @return UserProject[]
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getMembersOfProject(Project $project): array
	{
		$arrayResponse = $this->sendRequest('GET', 'projects/'.$project->getId().'/members');
		$return = [];
		foreach($arrayResponse as $user){
			$userType = new User($user);
			$return[] = new UserProject($user, $userType, $project);
		}
		return $return;
	}

	public function getGroupById( int $groupId): Group
	{
		$arrayResponse = $this->sendRequest('GET', 'groups/'.$groupId);
		return new Group($arrayResponse);
	}

	/**
	 * @param Group $group
	 * @return Group[]
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getGroupsRecursive( Group $group): array
	{
		//TODO: implement recursive getting
		$arrayResponse = $this->sendRequest('GET', 'groups/'.$group->getId().'/subgroups');
		$return = [];
		foreach($arrayResponse as $descendantGroup){
			$return[] = new Group($descendantGroup);
		}
		return $return;
	}

	/**
	 * @param Group $group
	 * @return Project[]
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getProjectsOfGroup(Group $group): array
	{
		$arrayResponse = $this->sendRequest('GET', 'groups/'.$group->getId().'/projects');
		$return = [];
		foreach($arrayResponse as $project){
			$return[] = new Project($project);
		}
		return $return;
	}

}