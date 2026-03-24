Javascript charts for Jaxon with Flot
=====================================

Javascript charts for Jaxon with the Flot library.
https://www.flotcharts.org

See the plugin in action with sample code.
- https://www.jaxon-php.org/examples/advanced/flot.html
- https://www.jaxon-php.org/examples/advanced/flot-types.html
- https://www.jaxon-php.org/examples/advanced/flot-axes.html

Installation
------------

Install this package with Composer.

```json
"require": {
    "jaxon-php/jaxon-flot": "^5.1"
}
```

Draw graphs
-----------

Create a new card to be displayed in a div with a given id, eg. "flot-container".

```php
    $card = $response->flot->card('flot-container');
```

Add a graph in the card, and set its parameters.

```php
    $graph = $card->graph(['label' => 'Flot example', 'lines' => ['show' => true]]);
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
    $card->xaxis()->options([
        // First X axis options.
    ])->points($ticks);
    $card->xaxis()->options([
        // Second X axis options.
    ])->loop(0, 16, 1, 'flot.xaxis.label');
```

Set the dimensions of the card.

```php
    $card->width('600px')->height('300px');
```

Finally, draw the graph.

```php
    $response->flot->draw($card);
```

You can add as many graphs as you need in a single card, and you can draw many cards in a single page.

Draw a pie
----------

Drawing a pie chart requires the pie plugin for Flot to be loaded.
Call this PHP code before loading the page, or add the Flot pie plugin manually.

```php
jaxon()->di()->g(FlotPlugin::class)->usePie(true);
```

Create a new card to be displayed in a div with a given id, eg. "flot-container".

```php
    $card = $response->flot->card('flot-container');
```

Set the card options.

```php
    $card->options([
        'series' => [
            'pie' => [
                'show' => true,
            ],
        ],
    ]);
```

Add the pie to the card

```php
    $card->pie()->slices([
        [3, 'Pt 1'],
        [8, 'Pt 2'],
        [5, 'Pt 3'],
        [13, 'Pt 4'],
    ]);
```

Finally, draw the graph.

```php
    $response->flot->draw($card);
```

Only one pie can be added to a card.

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-flot/issues
- Source Code: github.com/jaxon-php/jaxon-flot

License
-------

The project is licensed under the BSD license.
