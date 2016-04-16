<?php namespace davestewart\benchmark;

use davestewart\benchmark\base\UtilityObject;

/**
 * Class to compare Benchmarks and print out results in a meaningful manner
 *
 * @property    string      $name;
 * @property    Benchmark   $current;
 *
 * @package davestewart\benchmark
 */
class BenchmarkComparison extends UtilityObject
{

	// ------------------------------------------------------------------------------------------------
	// PROPERTIES

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var Benchmark
		 */
		protected $current;

		/**
		 * @var Benchmark[]
		 */
		protected $items;


	// ------------------------------------------------------------------------------------------------
	// INSTANTIATION
	
		public function __construct($name = 'Benchmark comparison', array $benchmarks = [])
		{
			$this->name = $name;
			$this->addMany($benchmarks);
		}

		public static function make($name = 'Benchmark comparison')
		{
			$benchmarks = array_slice(func_get_args(), 1);
			return new self($name, $benchmarks);
		}
	

	// ------------------------------------------------------------------------------------------------
	// BENCHMARK METHODS
	
		public function start($name = '', $description = '')
		{
			$this->stop();
			return $this->add($name, $description);
		}
	
		public function add($name = '', $description = '')
		{
			$this->current = Benchmark::make($name, $description)->start();
			$this->items[$name] = $this->current;
			return $this;
		}

		public function addMany(array $benchmarks = [])
		{
			foreach ($benchmarks as $value)
			{
				if($value instanceof Benchmark)
				{
					$this->add($value);
				}
			}
			return $this;
		}

		public function stop()
		{
			if($this->current)
			{
				$this->current->stop();
			}
			return $this;
		}

		public function done()
		{
			$this->stop();
			$this->summary(true);
			return $this;
		}


	// ------------------------------------------------------------------------------------------------
	// SUMMARY METHODS

		/**
		 * Return results in summary form
		 *
		 * @param bool $print
		 * @return array
		 */
		public function summary($print = false)
		{
			// data
			$items  = $this->results();
			$best   = array_values($items)[0];

			// summary header
			$summary =
			[
				$best->name => array_slice($this->getStrings($best, $best), 0, 1),
			];

			// rest of summary
			foreach ($items as $name => $item)
			{
				if($item != $best)
				{
					$summary[$name] = $this->getStrings($item, $best);
				}
			}

			// data
			$data =
			[
				'name'    => $this->name,
				'summary' => $summary,
			    'results' => array_values(array_map(function(Benchmark $item){ return $item->summary(); }, $items)),
			];

			// print
			if($print)
			{
				$this->pr($data);
			}

			// return
			return $data;
		}

		/**
		 * Get results of all benchmarks
		 *
		 * @return array
		 */
		public function results()
		{
			$items  = [] + $this->items;
			uasort($items, function ($a, $b)
			{
				if($a->time == $b->time) return 0;
				return $a->time < $b->time ? -1 : 1;
			});
			return $items;
		}


	// ------------------------------------------------------------------------------------------------
	// UTILITIES

		/**
		 * Gets human-readable strings for summary
		 *
		 * @param $bm1
		 * @param $bm2
		 * @return array
		 */
		protected function getStrings($bm1, $bm2)
		{
			// names
			$time1      = $bm1->time;
			$time2      = $bm2->time;

			// variables
			$diff1      = round($time2 / $time1, 2);
			$diff2      = round($time1 / $time2, 2);
			$pc         = floor(1 / $diff2 * 100);
			$mult       = $diff1 > 1 ? $diff1 : $diff2;
			$comp       = $diff1 > 1 ? 'faster' : 'slower';
			$time       = round($time1, 4);
	
			// return
			return
			[
				"took $time seconds",
				"was $mult times $comp",
				"fastest completed in $pc% of this time",
			];

		}

}

