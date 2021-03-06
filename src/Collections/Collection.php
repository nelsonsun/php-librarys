<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2016/3/14
 * Time: 19:44
 */

namespace Inhere\Library\Collections;

use Inhere\Library\Files\File;
use Inhere\Library\Files\Parsers\IniParser;
use Inhere\Library\Files\Parsers\JsonParser;
use Inhere\Library\Files\Parsers\YmlParser;
use Inhere\Library\Helpers\Arr;
use Inhere\Library\Helpers\DataHelper;
use RuntimeException;

/**
 * Class DataCollector - 数据收集器 (数据存储器 - DataStorage) complex deep
 * @package Inhere\Library\Collections
 * 支持 链式的子节点 设置 和 值获取
 * e.g:
 * ```
 * $data = [
 *      'foo' => [
 *          'bar' => [
 *              'yoo' => 'value'
 *          ]
 *       ]
 * ];
 * $config = new DataCollector();
 * $config->get('foo.bar.yoo')` equals to $data['foo']['bar']['yoo'];
 * ```
 * 简单的数据对象可使用  @see SimpleCollection
 * ```
 * $config = new SimpleCollection($data)
 * $config->get('foo');
 * ```
 */
class Collection extends SimpleCollection
{
    /**
     * @var array
     */
//    protected $files = [];

    /**
     * Property separator.
     * @var  string
     */
    protected $separator = '.';

    /**
     * name
     * @var string
     */
    protected $name;

    /**
     * formats
     * @var array
     */
    protected static $formats = ['json', 'php', 'ini', 'yml'];

    const FORMAT_JSON = 'json';
    const FORMAT_PHP = 'php';
    const FORMAT_INI = 'ini';
    const FORMAT_YML = 'yml';

    /**
     * __construct
     * @param mixed $data
     * @param string $format
     * @param string $name
     * @throws \RangeException
     */
    public function __construct($data = null, $format = 'php', $name = 'box1')
    {
        // Optionally load supplied data.
        $this->load($data, $format);

        parent::__construct();

        $this->name = $name;
    }

    /**
     * @param mixed $data
     * @param string $format
     * @param string $name
     * @return static
     */
    public static function make($data = null, $format = 'php', $name = 'box1')
    {
        return new static($data, $format, $name);
    }

    /**
     * set config value by path
     * @param string $path
     * @param mixed $value
     * @return mixed
     */
    public function set($path, $value)
    {
//        if (is_array($value) || is_object($value)) {
//            $value = DataHelper::toArray($value, true);
//        }

        Arr::setByPath($this->data, $path, $value, $this->separator);

        return $this;
    }

    /**
     * get value by path
     * @param string $path
     * @param string $default
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        return Arr::getByPath($this->data, $path, $default, $this->separator);
    }

    public function exists($path)
    {
        return $this->get($path) !== null;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function has(string $path)
    {
        return $this->exists($path);
    }

    public function reset()
    {
        $this->data = [];

        return $this;
    }

    /**
     * Clear all data.
     * @return  static
     */
    public function clear()
    {
        return $this->reset();
    }

    /**
     * @param $class
     * @return mixed
     */
    public function toObject($class = \stdClass::class)
    {
        return DataHelper::toObject($this->data, $class);
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @return array
     */
    public static function getFormats()
    {
        return static::$formats;
    }

    /**
     * setName
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * load
     * @param string|array $data
     * @param string $format = 'php'
     * @return static
     * @throws \RuntimeException
     * @throws \RangeException
     */
    public function load($data, $format = 'php')
    {
        if (!$data) {
            return $this;
        }

        if (\is_string($data) && \in_array($format, static::$formats, true)) {
            switch ($format) {
                case static::FORMAT_YML:
                    $this->loadYaml($data);
                    break;

                case static::FORMAT_JSON:
                    $this->loadJson($data);
                    break;

                case static::FORMAT_INI:
                    $this->loadIni($data);
                    break;

                case static::FORMAT_PHP:
                default:
                    $this->loadArray($data);
                    break;
            }

        } elseif (\is_array($data) || \is_object($data)) {
            $this->bindData($this->data, $data);
        }

        return $this;
    }

    /**
     * @param $file
     * @param string $format
     * @return array|mixed
     */
    public static function read($file, $format = self::FORMAT_PHP)
    {
        return File::load($file, $format);
    }

    /**
     * load data form yml file
     * @param $data
     * @throws RuntimeException
     * @return static
     */
    public function loadYaml($data)
    {
        return $this->bindData($this->data, static::parseYaml($data));
    }

    /**
     * load data form php file or array
     * @param array|string $data
     * @return static
     * @throws \InvalidArgumentException
     */
    public function loadArray($data)
    {
        if (\is_string($data) && is_file($data)) {
            $data = require $data;
        }

        if (!\is_array($data)) {
            throw new \InvalidArgumentException('param type error! must is array.');
        }

        return $this->bindData($this->data, $data);
    }

    /**
     * load data form php file or array
     * @param mixed $data
     * @return static
     * @throws \InvalidArgumentException
     */
    public function loadObject($data)
    {
        if (!\is_object($data)) {
            throw new \InvalidArgumentException('param type error! must is object.');
        }

        return $this->bindData($this->data, $data);
    }

    /**
     * load data form ini file
     * @param string $string
     * @return static
     */
    public function loadIni($string)
    {
        return $this->bindData($this->data, self::parseIni($string));
    }

    /**
     * load data form json file
     * @param $data
     * @return Collection
     * @throws RuntimeException
     */
    public function loadJson($data)
    {
        return $this->bindData($this->data, static::parseJson($data));
    }

    /**
     * @param $parent
     * @param $data
     * @param bool|false $raw
     * @return $this
     */
    protected function bindData(&$parent, $data, $raw = false)
    {
        // Ensure the input data is an array.
        if (!$raw) {
            $data = DataHelper::toArray($data);
        }

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (\is_array($value)) {
                if (!isset($parent[$key])) {
                    $parent[$key] = array();
                }

                $this->bindData($parent[$key], $value);
            } else {
                $parent[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator($this->data);
    }

    /**
     * Unset an offset in the iterator.
     * @param   mixed $offset The array offset.
     * @return  void
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function __clone()
    {
        $this->data = unserialize(serialize($this->data), ['allowed_classes' => self::class]);
    }

//////
///////////////////////////// helper /////////////////////////
//////

    /**
     * @param $string
     * @param bool $enhancement
     * @param callable|null $pathHandler
     * @param string $fileDir
     * @return array
     */
    public static function parseIni($string, $enhancement = false, callable $pathHandler = null, $fileDir = '')
    {
        return IniParser::parse($string, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * @param $data
     * @param bool $enhancement
     * @param callable|null $pathHandler
     * @param string $fileDir
     * @return array
     */
    public static function parseJson($data, $enhancement = false, callable $pathHandler = null, $fileDir = '')
    {
        return JsonParser::parse($data, $enhancement, $pathHandler, $fileDir);
    }

    /**
     * parse YAML
     * @param string|bool $data Waiting for the parse data
     * @param bool $enhancement Simple support import other config by tag 'import'. must is bool.
     * @param callable $pathHandler When the second param is true, this param is valid.
     * @param string $fileDir When the second param is true, this param is valid.
     * @return array
     */
    public static function parseYaml($data, $enhancement = false, callable $pathHandler = null, $fileDir = '')
    {
        return YmlParser::parse($data, $enhancement, $pathHandler, $fileDir);
    }
}
