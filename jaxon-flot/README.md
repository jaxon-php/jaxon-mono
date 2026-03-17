Javascript charts for Jaxon with Flot
=====================================

Javascript charts for Jaxon with the Flot library.
http://www.flotcharts.org

Installation
------------

Install this package with Composer.

```json
"require": {
    "jaxon-php/jaxon-flot": "^5.1"
}
```

Usage
-----

Create a new plot to be displayed in a div with a given id, eg. "flot-container".

```php
    $plot = $response->flot->plot('flot-container');
```

Add a graph in the plot, and set its parameters.

```php
    $graph = $plot->graph(['label' => 'Plot example', 'lines' => ['show' => true]]);
```
The options are defined in [the API docs](https://github.com/flot/flot/blob/master/API.md#plot-options).

Multiple data series can be added to a graph.

Each series can be defined using an array of points.

```php
    $graph->series()->points([[0, 3], [4, 8], [8, 5], [9, 13]]);
```

Or a javascript loop expression.
The loop goes from a start to an end value, with the specified step.
The last parameter is a Javascript function defined in the app, which takes a point as parameter and returns the corresponding value.

```php
    $graph->series()->loop(0, 14, 0.5, 'Math.sqrt');
```

The X axis and Y axis options can also be set. Multiple X axis and Y axis can be added.
For each axis, the labels can be optionally set either with an array of points, or with a loop expression.

```php
    $plot->xaxis()->options([
        // First X axis options.
    ])->points($ticks);
    $plot->xaxis()->options([
        // Second X axis options.
    ])->loop(0, 16, 1, 'plot.xaxis.label');
```

Optionally, set the dimensions of the plot.
If the dimensions are not set here, make sure they are in HTML or CSS code.
The Flot library requires the container to have width and height set.

```php
    $plot->width('600px')->height('300px');
```

Finally, draw the graph.

```php
    $response->flot->draw($plot);
```

You can add as many graphs as you need in a single plot, and you can draw many plots in a single page.

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-flot/issues
- Source Code: github.com/jaxon-php/jaxon-flot

License
-------

The project is licensed under the BSD license.
