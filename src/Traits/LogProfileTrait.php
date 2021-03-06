<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-18
 * Time: 13:10
 */

namespace Inhere\Library\Traits;

use Inhere\Library\Helpers\PhpHelper;
use Monolog\Logger;

/**
 * Trait LogProfileTrait
 * @package Inhere\Library\Traits
 *
 * @method log(int $level, string $message, array $context = [])
 */
trait LogProfileTrait
{
    /**
     * @var array
     */
    private $profiles = [];

    /** @var string e.g 'application|request' */
    private $activeKey;

    /**
     * mark data analysis start
     * @param $name
     * @param array $context
     * @param string $category
     */
    public function profile($name, array $context = [], $category = 'application')
    {
        $data = [
            '_profile_stats' => [
                'startTime' => microtime(true),
                'startMem' => memory_get_usage(),
            ],
            '_profile_start' => $context,
            '_profile_end' => null,
        ];

        $this->activeKey = $category . '|' . $name;
        $this->profiles[$category][$name] = $data;
    }

    /**
     * mark data analysis end
     * @param string|null $title
     * @param array $context
     */
    public function profileEnd($title = null, array $context = [])
    {
        if (!$this->activeKey) {
            return;
        }

        list($category, $name) = explode('|', $this->activeKey);

        if (isset($this->profiles[$category][$name])) {
            $data = $this->profiles[$category][$name];

            $old = $data['_profile_stats'];
            $data['_profile_stats'] = PhpHelper::runtime($old['startTime'], $old['startMem']);
            $data['_profile_end'] = $context;

            $title = $category . ' - ' . ($title ?: $name);

            $this->activeKey = null;
            $this->log(Logger::DEBUG, $title, $data);
        }
    }

}
