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

// Controller mapping
Router::connect('/bank/:controller', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action/**', ['plugin' => 'BankWeb']);
