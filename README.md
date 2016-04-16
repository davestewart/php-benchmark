# Benchmark

## Simple benchmarking library

Simple set of benchmarking classes to time arbitrary events with a fluid syntax that doesn't get in your way.

```php
$bm = Benchmark::compare('Array tests')
    ->start('compare to range');

    // run code...

$bm->start('array_filter string keys');

    // run code...

$bm->start('array_values');

    // run code...

$bm->done();

```

When the comparison is done, it prints a summary in plain English:

```
Array
(
    [name] => Array methods
    [summary] => Array
        (
            [array_values] => Array
                (
                    [0] => took 0.0779 seconds
                )

            [compare to range] => Array
                (
                    [0] => took 0.2141 seconds
                    [1] => was 2.75 times slower
                    [2] => fastest completed in 36% of this time
                )

            [array_filter string keys] => Array
                (
                    [0] => took 0.6927 seconds
                    [1] => was 8.89 times slower
                    [2] => fastest completed in 11% of this time
                )

        )

    [results] => Array
        (
            ...

```

## Usage

### Benchmark class

The `Benchmark` class is the basic unit of benchmarking, and has 4 main methods:

- `start()` - starts benchmarking and logs a time and memory-use result
- `stop()` - stops a running benchmark and logs a time and memory-use result
- `mark()` - logs a time and memory-use result
- `reset()` - clears all marks

A new instance can be created via the `new` keyword, by by a static chainable method, which has the advantage that it can be started:

```
$bm = new Benchmark('Some task', 'Some more info');
$bm = Benchmark::make('Some task', 'Some more info')->start();
```

The following properties and methods are also available:

- `time` - the amount of time since the last mark was made
- `summary()` - prints out a summary of the times
- `compareTo()` - passes another `Benchmark` and returns a `BenchmarkComparison` object of the two


### BenchmarkComparison class

The `BenchamrkComparison` class is where things get interesting, as:

- you can add multiple benchmarks and compare them
- its `summary()` method shows you in plain English what benchamrks rand quicker and slower and by how much, compared to the fastest.

Instantiate a new instance as follows:

```
$bm = BenchmarkComparison::make('Array tests');
$bm = new BenchmarkComparison('Array tests');
$bm = Benchmark::compare('Array tests');

```

The following methods allow you to start and stop timers

- `start($name)` - stop any existing benchmark, and create and start a new one
- `add($benchmark)` - add a new benchmark, but don't stop the old one
- `addMany($benchmarks)` - adds multiple benchmarks at once
- `stop()` - stop the currently-running benchmark
- `done()` - stop the currently-running benchmark and print the summary

The following methods print out results:

- `summary()` - prints out results of all benchmarks, plus a header summary, including:

    - the time it took
    - how many times slower than the best benchmark it was
    - what percentage of time the best benchmark took compared to this one

- `results()` - the ordered list of results

## Typical use case

Consider:

```php
// variables
$loops  = 10000;
$data   = ['a', 'b', 'c', 'd', 'e'];
$state  = null;

// comparison
$bm = Benchmark::compare('Array methods');

// range
$bm->start('compare to range');
for ($i = 0; $i < $loops; $i++)
{
	$state = array_keys($data) == range(0, count($data));
}

// string
$bm->start('array_values');
for ($i = 0; $i < $loops; $i++)
{
	$state = array_values($data) == $data;
}

// string
$bm->start('array_filter: string keys');
for ($i = 0; $i < $loops; $i++)
{
	$state = count(array_filter(array_keys($data), 'is_string')) > 0;
}

// compare
$bm->complete();
```

The `Benchmark::compare()` syntax keeps things expressive by returning a `BenchmarkComparison` class, that creates and starts a single `Benchmark` instance.

The comparison object is chained and returned for ease of use.

When `start()` is next called, it triggers the the current benchmark to stop, and creates, names, and starts a new one.

Finally, when `complete()` is called, the final benchmark is stopped, and the results are compiled, compared, and the summary printed out:


```
Array
(
    [name] => Array methods
    [summary] => Array
        (
            [array_values] => Array
                (
                    [0] => took 0.075 seconds
                )

            [compare to range] => Array
                (
                    [0] => took 0.2197 seconds
                    [1] => was 2.93 times slower
                    [2] => fastest completed in 34% of this time
                )

            [array_filter: string keys] => Array
                (
                    [0] => took 0.6892 seconds
                    [1] => was 9.18 times slower
                    [2] => fastest completed in 10% of this time
                )

        )

    [results] => Array

    ... full results are listed here
```