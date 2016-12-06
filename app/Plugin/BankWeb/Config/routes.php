<?php
/**
 * BankWeb Routes Configuration
 *
 *
 */

// Bank index mapping
Router::connect('/bank', ['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']);

// Controller mapping
Router::connect('/bank/:controller', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action', ['plugin' => 'BankWeb']);
Router::connect('/bank/:controller/:action/**', ['plugin' => 'BankWeb']);

