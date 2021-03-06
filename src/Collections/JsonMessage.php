<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/9/2
 * Time: 上午11:49
 */

namespace Inhere\Library\Collections;

use Inhere\Exceptions\PropertyException;

/**
 * Class JsonMessage
 * @package slimExt\helpers
 * $mg = JsonMessage::create(['msg' => 'success', 'code' => 23]);
 * $mg->data = [ 'key' => 'test'];
 * echo json_encode($mg);
 * response to client:
 * {
 *  "code":23,
 *  "msg":"success",
 *  "data": {
 *      "key":"value"
 *  }
 * }
 */
class JsonMessage
{
    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $msg;

    /**
     * @var int|float
     */
    public $time;

    /**
     * @var array|string
     */
    public $data;

    public static function make($data = null, $msg = 'success', $code = 0)
    {
        return new static($data, $msg, $code);
    }

    /**
     * JsonMessage constructor.
     * @param null $data
     * @param string $msg
     * @param int $code
     */
    public function __construct($data = null, $msg = 'success', $code = 0)
    {
        $this->data = $data;
        $this->msg = $msg;
        $this->code = $code;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return (int)$this->code === 0;
    }

    /**
     * @return bool
     */
    public function isFailure()
    {
        return (int)$this->code !== 0;
    }

    /**
     * @param $code
     * @return $this
     */
    public function code($code)
    {
        $this->code = (int)$code;

        return $this;
    }

    /**
     * @param $msg
     * @return $this
     */
    public function msg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function add($key, $value)
    {
        if (null === $this->data) {
            $this->data = [];
        }

        $this->data[$key] = $value;
    }

    /**
     * @param array|string $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        // add a new alert message
        return [
            'code' => (int)$this->code,
            'msg' => $this->msg,
            'data' => (array)$this->data
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->all());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws PropertyException
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        throw new PropertyException(sprintf('the property is not exists: %s', $name));
    }
}
