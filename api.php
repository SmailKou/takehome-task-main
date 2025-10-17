<?php

/**
 * Article Search and Retrieval API
 * @file api.php
 */

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();

header( 'Content-Type: application/json' );
$response = routeRequest($app);
echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

/**
 * Sanitize user input
 */
function sanitizeInput(string $input): string
{
	$input = trim($input);

	// Remove any remaining control characters
	$input = preg_replace('/[\\x00-\\x08\\x0b-\\x1f\\x7f]/', '', $input);

	return $input;
}

/**
 * Validate prefix search parameter
 */
function isValidPrefix(string $prefix): bool
{
	if ($prefix === '' || strlen($prefix) > 255) {
		return false;
	}

	// Check for possible path traversal attempts
	if (strpos($prefix, '..') !== false || strpos($prefix, "\0") !== false) {
		return false;
	}

	// Check for potentially dangerous patterns
	if (preg_match('/[<>"`|\\x00-\\x08\\x0b-\\x1f\\x7f]/', $prefix)) {
		return false;
	}

	return true;
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
	// Validate input before processing
	if (!isValidPrefix($prefix)) {
		return ['error' => 'Invalid search parameter'];
	}

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
		$prefix = sanitizeInput($_GET['prefixsearch']);
		return ['content' => handlePrefixSearch($app, $prefix)];
	}

	return ['content' => $app->fetch($_GET)];
}
