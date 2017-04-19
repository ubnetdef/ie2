<?php
/**
 * ScoreEngine Routes Configuration
 */

// Scoreboard mapping
Router::connect('/scoreboard', [
    'plugin' => 'ScoreEngine',
    'controller' => 'scoreboard',
    'action' => 'index'
]);
Router::connect('/scoreboard/api', [
    'plugin' => 'ScoreEngine',
    'controller' => 'scoreboard',
    'action' => 'api'
]);
Router::connect('/scoreboard/overview', [
    'plugin' => 'ScoreEngine',
    'controller' => 'scoreboard',
    'action' => 'overview'
]);

// ScoreEngine admin mapping
Router::connect('/admin/scoreengine', [
    'plugin' => 'ScoreEngine',
    'controller' => 'scoreadmin',
    'action' => 'index'
]);
Router::connect('/admin/scoreengine/:action/*', [
    'plugin' => 'ScoreEngine',
    'controller' => 'scoreadmin'
]);

// Team Panel mapping
Router::connect('/team', [
    'plugin' => 'ScoreEngine',
    'controller' => 'team',
    'action' => 'index'
]);
Router::connect('/team/:action', [
    'plugin' => 'ScoreEngine',
    'controller' => 'team'
]);
Router::connect('/team/:action/*', [
    'plugin' => 'ScoreEngine',
    'controller' => 'team'
]);
