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

// Extract parameters checking for better readability
$hasTitle = isset($_GET['title']);
$hasPrefixSearch = isset($_GET['prefixsearch']);

header( 'Content-Type: application/json' );

if (!$hasTitle && !$hasPrefixSearch) {
	echo json_encode( [ 'content' => $app->getListOfArticles() ] );
} elseif ($hasPrefixSearch) {
	$matchingArticles = handlePrefixSearch($app, $prefixSearch);
	echo json_encode( [ 'content' => $matchingArticles ] );
} else {
	echo json_encode( [ 'content' => $app->fetch( $_GET ) ] );
}

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
