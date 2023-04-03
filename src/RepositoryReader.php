<?php

namespace GithubReader;

use Exception;
use GithubReader\Github\Directory as GithubRepository;
use GrahamCampbell\GitHub\GitHubManager;

final class RepositoryReader
{
    const DEFAULT_CONNECTION = 'app';

    protected $manager;

    protected $connection;

    protected $organization;

    protected $repositoryName;

    /**
     * GithubReader constructor.
     *
     * @param GitHubManager $manager
     */
    public function __construct(GitHubManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Initialize the repository with organization/user and repository name
     *
     * @param $organization
     * @param $repositoryName
     * @param $connection
     *
     * @return $this
     */
    public function init($organization, $repositoryName, $connection = null)
    {
        $this->setOrganization($organization);
        $this->setRepositoryName($repositoryName);
        $this->setConnection($connection);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection ?: static::DEFAULT_CONNECTION;
    }

    /**
     * @param mixed $connection
     *
     * @return $this
     */
    public function setConnection(string $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Getter for organization.
     *
     * @return string|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Setter for organization
     *
     * @param string $organization
     * @return $this
     */
    public function setOrganization(string $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Getter for repository name.
     *
     * @return string|null
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * Set the repository name.
     *
     * @param string $repositoryName
     * @return $this
     */
    public function setRepositoryName(string $repositoryName)
    {
        $this->repositoryName = $repositoryName;

        return $this;
    }

    /**
     * Get Github Manager
     *
     * @return \GrahamCampbell\GitHub\GitHubManager
     */
    protected function getManager(): \GrahamCampbell\GitHub\GitHubManager
    {
        return $this->manager;
    }

    /**
     * Read the Repository.
     *
     * @param null $organization
     * @param null $repositoryName
     * @return \GithubReader\Github\Directory
     * @throws \Exception
     */
    public function read($organization = null, $repositoryName = null, $connection = null)
    {
        if ($organization) {
            $this->setOrganization($organization);
        }

        if ($repositoryName) {
            $this->setRepositoryName($repositoryName);
        }

        if ($connection) {
            $this->setConnection($connection);
        }

        if (! $this->getOrganization() || ! $this->getRepositoryName()) {
            throw new Exception("Organization name or Repository Name not set.");
        }

        return new GithubRepository($this);
    }

    /**
     * Read a specific path in the repository.
     *
     * @param null $path
     * @return mixed
     */
    public function readPath($path = null)
    {
        return $this->getManager()->connection($this->getConnection())->api('repo')->contents()->show($this->getOrganization(), $this->getRepositoryName(), $path);
    }

    public function extractArchive($format='zipball', $branch = null)
    {
        return $this->getManager()->connection($this->getConnection())->api('repo')->contents()->archive($this->getOrganization(), $this->getRepositoryName(), $format, $branch);
    }
}
