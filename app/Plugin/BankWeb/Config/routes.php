<?php
/**
 * BankWeb Routes Configuration
 *
 *
 */

// Bank index mapping
Router::connect('/bank', ['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']);

// Bank admin mapping
Router::connect('/admin/bank', ['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
Router::connect('/admin/bank/:action/*', ['plugin' => 'BankWeb', 'controller' => 'bankadmin']);

// Bank overview mapping
Router::connect('/staff/bank', ['plugin' => 'BankWeb', 'controller' => 'overview', 'action' => 'index']);
Router::connect('/staff/bank/:action/*', ['plugin' => 'BankWeb', 'controller' => 'overview']);

// Controller mapping
Router::connect('/bank/:controller', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action/**', ['plugin' => 'BankWeb']);
