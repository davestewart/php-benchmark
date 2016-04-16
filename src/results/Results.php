<?php namespace davestewart\benchmark\results;

class Results extends \ArrayObject
{
	/**
	 * The first result
	 *
	 * @return number
	 */
	public function first()
	{
		return $this[0];
	}

	/**
	 * The last result
	 *
	 * @return number
	 */
	public function last()
	{
		return $this[count($this) - 1];
	}

}
