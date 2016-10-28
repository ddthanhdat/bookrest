<?php
/**
 * Author: Bui Vo Quoc Bao
 */

/**
 * View class to load views files.
 */
class View
{
    /**
     * Data passed to view.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The view name.
     *
     * @var string
     */
    protected $view;

    /**
     * The path of the view file.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new View instance.
     *
     * @param string $view
     * @param string $path
     * @param array  $data
     */
    protected function __construct($view, $path, array $data = [])
    {
        $this->view = $view;
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Create a new View instance.
     *
     * @param  string $view
     * @param  array  $data
     * @return \Core\View
     */
    public static function make($view, array $data = [])
    {
        return new static($view, realpath($view), $data);
    }

    /**
     * Get the evaluated content of the view.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->evaluatePath($this->path, $this->gatherData());
    }

    /**
     * Get the evaluated content of the view at the given path.
     *
     * @param  string $path
     * @param  array  $data
     * @return string
     */
    private function evaluatePath($path, array $data = [])
    {
        $obLevel = ob_get_level();

        ob_start();

        extract($data);

        try {
            include $path;
        } catch (\Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Get the data bound to the view instance.
     *
     * @return array
     */
    protected function gatherData()
    {
        $data = $this->data;
        foreach ($data as $key => $value) {
            if ($value instanceof View) {
                $data[$key] = $value->getContent();
            }
        }

        return $data;
    }

    /**
     * Add a view instance to the view data.
     *
     * @param  string  $key
     * @param  string  $view
     * @param  array   $data
     * @return $this
     */
    public function nest($key, $view, array $data = [])
    {
        return $this->with($key, static::make($view, $data));
    }

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array  $key
     * @param  mixed   $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function getName()
    {
        return $this->view;
    }

    /**
     * Get the array of view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the path to the view file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path to the view.
     *
     * @param  string  $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the string content of the view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Get a piece of data from the view.
     *
     * @param  string  $key
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a piece of data on the view.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Check if a piece of data is bound to the view.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a piece of bound data from the view.
     *
     * @param  string  $key
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}
