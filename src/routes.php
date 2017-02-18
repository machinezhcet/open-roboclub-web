<?php

use App\Repo;

// Routes

$app->get('/[home]', App\Page\HomePage::class);
$app->get('/announcements', App\Page\AnnouncementsPage::class);
$app->get('/contributions', App\Page\ContributionsPage::class);
$app->get('/projects', App\Page\ProjectsPage::class);
$app->get('/projects/{id}', App\Page\ProjectPage::class)->setName('projects');
$app->get('/downloads', App\Page\DownloadsPage::class);
$app->get('/team', App\Page\TeamPage::class);
$app->get('/robocon', App\Page\RoboconPage::class);
$app->get('/developers', App\Page\DevelopersPage::class);

$app->get('/img/{path:.*}', App\ImageServer::class);

$app->get('/signin', App\Page\SigninPage::class);

$app->get('/admin', App\Page\AdminPage::class);

$app->post('/send_notification', App\NotificationSender::class);

// Cache Control

$app->get('/cache[/{option}[/{parameter}]]', App\CacheControl::class);
