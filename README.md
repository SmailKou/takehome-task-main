This repository contains an application for viewing and editing text documents. Users of the application
can:

* Create new pages
* Edit any existing page
* View a list of existing content

## Your task

After reading through the code and familiarizing yourself with the repository, use not more than 3 hours of your time to address the following questions:

Question 1

In `api.php`, work through the TODOs using your
creativity and judgment. Please only update the api.php file.

Question 2

Anyone who visits our site can make an edit. We would like to store the IP
address of everyone who has made an edit, and which article they have edited so
that we can guess which articles may have been vandalized. For example, imagine
we have a classroom in a school, where there are a number of students who are
vandalizing articles at once, with IP addresses with IP addresses in the range
72.1.3.4 to 72.1.3.89, and we'd like a list of which articles they have edited.

Design a database table (or tables) and provide example queries to efficiently
look up:
* For a given article, the IP addresses of all the people who have edited it
* For a given IP range, which articles have been edited from IP addresses in that
range.

Please describe your table(s) in markdown form, describe any indexes you would
add, and provide example queries. (Don't actually implement a database.)
For example:

|Column name|Data type|Description          |
|-----------|---------|---------------------|
|article_id |integer  |The ID of the article|
|...        |...      |...                  |

Primary key index on article_id.
...

{{ add your commentary here }}
{{ add example queries here }}


## How your response will be evaluated

We are most interested in seeing how you think about the TODOs rather than whether
you have written perfect code in a very limited amount of time. We will use this exercise to ask related questions in the
in-person technical interview. Specifically, we will discuss code quality, accessibility, security and performance. 

We will also evaluate the messages you use in your Git commits, and that you have made your changes in small, logical commits.

### Note

Please do not use any additional external libraries for this exercise.

## Usage

Download [composer](https://getcomposer.org/), then:

1. `composer install` – installs dependencies for the application
2. `composer serve` - Serves the application
   1. Web UI is available at http://localhost:8989.
   2. API is available at http://localhost:8989/api.php
   3. If you need to change the port, you can do that in `composer.json`
3. `composer seed` – Generate seed content for the application
4. `composer test` – Lint files and run tests

## Submission

Please create a ZIP file of the git repository and send back to the recruiter.
