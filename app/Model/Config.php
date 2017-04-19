<?php
App::uses('AppModel', 'Model');

/**
 * Config Model
 *
 */
class Config extends AppModel {

    /**
     * Override the table for this model.
     * Otherwise, CakePHP would use "configs"
     */
    public $useTable = 'config';

    /**
     * Cache key template
     */
    const CACHE_KEY = 'Config.%s';

    /**
     * After Save Model Hook
     *
     * This will clear the cache of the updated
     * item when a save is completed.
     */
    public function afterSave($created, $options = []) {
        if (!$created) {
            Cache::delete(sprintf(self::CACHE_KEY, $this->data['Config']['key']));
        }
    }

    /**
     * Get Config Key
     *
     * Will attempt to use the cache first, then
     * query the database.
     *
     * @param $key The config key to get
     * @return mixed The value
     */
    public function getKey($key) {
        $data = Cache::read(sprintf(self::CACHE_KEY, $key));

        if ($data === false) {
            $data = $this->find('first', [
                'conditions' => [
                    'key' => $key,
                ],
            ]);

            Cache::write(sprintf(self::CACHE_KEY, $key), $data);
        }

        return empty($data) ? '' : $data['Config']['value'];
    }

    /**
     * Get Inject Types
     *
     * This is basically a proxy function to get the key
     * 'engine.inject_types'.  It wraps json_decode around it
     * too, so the data is actually useful.
     *
     * @return array The configured inject types
     */
    public function getInjectTypes() {
        return json_decode($this->getKey('engine.inject_types'));
    }
}
