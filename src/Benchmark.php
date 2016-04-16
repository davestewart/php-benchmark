<?php namespace davestewart\benchmark;

use davestewart\benchmark\base\UtilityObject;
use davestewart\benchmark\results\NumericResults;
use davestewart\benchmark\results\Results;

/**
 * Benchmarking class
 *
 * @package App\Http\Controllers\test
 *
 * @property    string          $name
 * @property    string          $description
 * @property    bool            $active
 * @property    float           $time
 * @property    NumericResults  $times
 */
class Benchmark extends UtilityObject
{

	// ------------------------------------------------------------------------------------------------
	// PROPERTIES

		/** @var string  */
		protected $name;

		/** @var string  */
		protected $description;

		/** @var bool  */
		protected $active;

		/** @var float */
		protected $timeStart;

		/** @var float  */
		protected $time;

		/** @var Results  */
		protected $times;


	// ------------------------------------------------------------------------------------------------
	// INSTANTIATION

		public function __construct($name = '', $description = '')
		{
			$this->name         = $name;
			$this->description  = $description;
			$this->reset();
		}

		public static function make($name = '', $description = '')
		{
			$bm = new self($name, $description);
			return $bm->start();
		}

		public static function compare($name, $benchmarks = [])
		{
			return new BenchmarkComparison($name, $benchmarks);
		}
	

	// ------------------------------------------------------------------------------------------------
	// STARTING AND STOPPING

		public function start($print = false)
		{
			if( ! $this->active )
			{
				$this->timeStart = microtime(true);
				$this->update(0, $print);
			}
			$this->active = true;
			return $this;
		}

		public function stop($print = false)
		{
			if( $this->active )
			{
				$this->mark($print);
			}
			$this->active   = false;
			return $this;
		}

		public function mark($print = false)
		{
			$this->update(microtime(true) - $this->timeStart);
			return $this;
		}

		public function reset()
		{
			$this->timeStart    = 0;
			$this->time         = 0;
			$this->times        = new NumericResults();
			return $this;
		}

	
	// ------------------------------------------------------------------------------------------------
	// COMPARISONS

		public function compareTo(Benchmark $bm)
		{
			return new BenchmarkComparison($this, $bm);
		}

		public function summary($print = false)
		{
			// data
			$data =
			[
				'name'      => $this->name,
				'desc'      => $this->description,
				'time'      => $this->time,
				'average'   => $this->time / (count($this->times) - 1),
				'times'     => (array) $this->times,
				'steps'     => $this->times->steps(),
	        ];

			// print
			if($print)
			{
				$this->pr($data);
			}

			// return
			return $data;
		}


	// ------------------------------------------------------------------------------------------------
	// UTILITIES

		protected function update($time, $print = false)
		{
			// update values
			$this->times[]  = $time;
			$this->time     = $time;

			// print
			if($print)
			{
				$this->output();
			}
		}

		protected function output()
		{
			$this->pr($this->name . ' : ' . $this->time);
		}

}
