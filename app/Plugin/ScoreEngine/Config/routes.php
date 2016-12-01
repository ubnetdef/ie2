<?php
/**
 * ScoreEngine Routes Configuration
 *
 *
 */

// Scoreboard mapping
Router::connect('/scoreboard', ['plugin' => 'ScoreEngine', 'controller' => 'scoreboard', 'action' => 'index']);

// Team Panel mapping
Router::connect('/team/engine', ['plugin' => 'ScoreEngine', 'controller' => 'team', 'action' => 'index']);
