<?php
App::uses('ClassRegistry', 'Utility');

class BankWebSchema extends CakeSchema {

    public $account_mappings = [
        'id' => [
            'type' => 'integer',
            'null' => false,
            'key'  => 'primary',
        ],
        'group_id' => [
            'type' => 'integer',
            'null' => false,
        ],
        'username' => [
            'type' => 'text',
        ],
        'password' => [
            'type' => 'text',
        ],

        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unqiue' => true],
        ],
    ];

    public $bank_products = [
        'id' => [
            'type' => 'integer',
            'null' => false,
            'key'  => 'primary',
        ],
        'enabled' => [
            'type'    => 'boolean',
            'default' => false,
        ],
        'name' => [
            'type' => 'text',
            'null' => false,
        ],
        'description' => [
            'type' => 'text',
            'null' => false,
        ],
        'cost' => [
            'type' => 'integer',
            'null' => false,
        ],
        'message_user' => [
            'type' => 'text',
        ],
        'message_slack' => [
            'type' => 'text',
        ],

        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unqiue' => true],
        ],
    ];

    public $bank_purchases = [
        'id' => [
            'type' => 'integer',
            'null' => false,
            'key'  => 'primary',
        ],
        'product_id' => [
            'type' => 'integer',
            'null' => false,
        ],
        'user_id' => [
            'type' => 'integer',
            'null' => false,
        ],
        'group_id' => [
            'type' => 'integer',
            'null' => false,
        ],
        'time' => [
            'type' => 'integer',
            'null' => false,
        ],
        'completed' => [
            'type'    => 'boolean',
            'default' => false,
        ],
        'completed_by' => [
            'type' => 'integer',
        ],
        'completed_time' => [
            'type' => 'integer',
        ],

        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unqiue' => true],
        ],
    ];

    // ================================

    public function before($event = []) {
        ConnectionManager::getDataSource('default')->cacheSources = false;

        return true;
    }

    public function after($event = []) {
        if (!isset($event['create'])) {
        	return;
        }

        switch ($event['create']) {
            case 'account_mappings':
                $this->_create('AccountMapping', [
                    'group_id' => env('GROUP_STAFF'),
                    'username'  => 'admin',
                    'password'  => 'admin',
                ]);
                break;

            case 'bank_products':
                $this->_create('BankProduct', [
                    'enabled'      => true,
                    'name'         => 'BankWeb Example Product',
                    'description'  => 'This is an example product. SO COOL.',
                    'cost'         => 1337,
                    'message_user' => 'Congrats! You purchased an example product!',
                ]);
                break;
        }
    }

    private function _create($tbl, $data) {
        $table = ClassRegistry::init($tbl);

        $table->create();
        $table->save([$tbl => $data]);
    }
}
