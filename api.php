<?php

/**
 * Article Search and Retrieval API
 * @file api.php
 */

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();

// Define useful variables
$title = $_GET['title'];
$prefixSearch = $_GET['prefixsearch'];

header( 'Content-Type: application/json' );
$response = routeRequest($app);
echo json_encode($response);

/**
 * Handles prefix search with case-insensitive matching
 *
 * @param App $app
 * @param string $prefix
 * @return array
 */
function handlePrefixSearch(App $app, string $prefix): array
{
	$articlesList = $app->getListOfArticles();
	$matchingArticles = [];

	foreach ($articlesList as $article) {
		if (strpos(strtolower($article), strtolower($prefix)) === 0) {
			$matchingArticles[] = $article;
		}
	}

	return $matchingArticles;
}

/**
 * Route the request based on parameters
 *
 * @param $app
 * @return array
 */
function routeRequest(App $app): array
{
	$hasTitle = isset($_GET['title']);
	$hasPrefixSearch = isset($_GET['prefixsearch']);

	if (!$hasTitle && !$hasPrefixSearch) {
		// No parameters - list all articles
		return ['content' => $app->getListOfArticles()];
	}

	if ($hasPrefixSearch) {
		// Prefix search route
		return ['content' => handlePrefixSearch($app, $_GET['prefixsearch'])];
	}

	return ['content' => $app->fetch($_GET)];
}
