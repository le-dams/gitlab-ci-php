<?php

require_once 'vendor/autoload.php';

use GitLab\Command\CreateTagCommand;
use GitLab\Command\CreateVarCommand;
use GitLab\Command\RemovePipelinesCommand;
use Gitlab\Command\RemoveTagCommand;
use GitLab\GitLab;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

if (file_exists(__DIR__.'/.env') && class_exists(Dotenv::class)) {
    (new Dotenv())->load(__DIR__.'/.env');
}

$gitlabUrl = $_ENV['GITLAB_URL'];
$gitlabToken = $_ENV['GITLAB_TOKEN'];
$gitlabProjects = $_ENV['GITLAB_PROJECTS'];

$application = new Application();

$application->add(new CreateTagCommand());
//$application->add(new RemoveTagCommand());
$application->add(new RemovePipelinesCommand());
$application->add(new CreateVarCommand());

$gitlab = GitLab::getInstance($gitlabUrl, $gitlabToken, GitLab::formatProjectsStringToArray($gitlabProjects));

$args = getopt(null, ["action:"]);
$action = $args['action'] ?? null;

switch ($action) {
    case 'create-var':
        $args = getopt(null, ["key:", "value:", "env:"]);

        $key = $args['key'] ?? null;
        $value = $args['value'] ?? null;
        $env = $args['env'] ?? null;

        if (is_null($key) || is_null($value) || is_null($env)) {
            throw new Exception('Missing params key and/or value and/or env');
        }

        (new CreateVarCommand())->exec($gitlab, $key, $value, $env);

        break;
    case 'create-tag':
        $args = getopt(null, ["tag:", "branch:", "message:"]);

        $tag = $args['tag'] ?? null;
        $branch = $args['branch'] ?? null;
        $message = $args['message'] ?? null;

        if (is_null($tag) || is_null($branch)) {
            throw new Exception('Missing params value');
        }

        (new CreateTagCommand())->exec($gitlab, $branch, $tag, $message);

        break;
    default:
        throw new Exception('Need to feel an existing action!');
}


//(new RemovePipelinesCommand())->exec($gitlab, 1, [1]);
//(new RemoveTagCommand())->exec($gitlab, 1, 'test-tag-1');
