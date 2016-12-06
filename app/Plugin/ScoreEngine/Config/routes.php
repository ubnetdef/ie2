<?php
/**
 * ScoreEngine Routes Configuration
 */

// Scoreboard mapping
Router::connect('/scoreboard', ['plugin' => 'ScoreEngine', 'controller' => 'scoreboard', 'action' => 'index']);

// Team Panel mapping
Router::connect('/team', ['plugin' => 'ScoreEngine', 'controller' => 'team', 'action' => 'index']);
Router::connect('/team/:action', ['plugin' => 'ScoreEngine', 'controller' => 'team']);
Router::connect('/team/:action/*', ['plugin' => 'ScoreEngine', 'controller' => 'team']);
