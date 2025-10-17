<?php

/**
 * Article Search and Retrieval API
 * @file api.php
 */

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();

header('Content-Type: application/json');
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
 * Simple file-based cache for article list since I am not allowed to use a library
 */
function getCachedArticleList(App $app, int $ttl = 300): array // 5 minutes TTL
{
	$cacheFile = __DIR__ . '/cache/articles_list.cache';
	$cacheDir = dirname($cacheFile);

	if (!is_dir($cacheDir)) {
		mkdir($cacheDir, 0755, true);
	}

	if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
		$cachedData = file_get_contents($cacheFile);
		$data = json_decode($cachedData, true);
		if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
			return $data;
		}
	}

	// Cache miss or invalid - fetch fresh data
	$articles = $app->getListOfArticles();

	try {
		file_put_contents($cacheFile, json_encode($articles), LOCK_EX);
	} catch (Exception $e) {
		error_log($e->getMessage());
	}

	return $articles;
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

	$articlesList = getCachedArticleList($app);
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
		return ['content' => getCachedArticleList($app)];
	}

	if ($hasPrefixSearch) {
		// Prefix search route
		$prefix = sanitizeInput($_GET['prefixsearch']);
		return ['content' => handlePrefixSearch($app, $prefix)];
	}

	return ['content' => $app->fetch($_GET)];
}
