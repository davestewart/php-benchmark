<?php namespace davestewart\benchmark\base;

/**
 * Base utility class to provide some basic functionality
 *
 * @package davestewart\benchmark\base
 */
class UtilityObject
{

	public function __get($name)
	{
		if(property_exists($this, $name))
		{
			return $this->$name;
		}
		throw new \Exception("Invalid property '$name'");
	}

	protected function pr()
	{
		echo '<pre style="font-size:11px">';
		$args = func_get_args();
		print_r( count($args) === 1 ? $args[0] : $args);
		echo '</pre>';
	}

}
