<?php namespace davestewart\benchmark\results;

use davestewart\benchmark\results\Results;

class NumericResults extends Results
{
	/**
	 * The difference between the first and the last result
	 *
	 * @return number
	 */
	public function delta()
	{
		return $this->last() - $this->first();
	}

	/**
	 * The average time for all results
	 *
	 * @return float
	 */
	public function average()
	{
		return $this->delta() / (count($this) - 1); // don't count first timestamp, as it's 0
	}

	/**
	 * An array of the differences between steps
	 *
	 * @return array
	 */
	public function steps()
	{
		// variables
		$steps      = [];
		$lastValue  = $this[0];

		// calculate
		for ($i = 1; $i < count($this); $i++)
		{
			$value      = $this[$i];
			$steps[]    = $value - $lastValue;
			$lastValue  = $value;
		}

		// return
		return $steps;
	}
}
