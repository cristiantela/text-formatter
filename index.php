<?php

function TextFormatter ($input, $vars) {
	function get_all_vars ($prefix, $vars, &$all_vars = null) {
		if ($all_vars === null) {
			$all_vars = [];
		}

		foreach(array_keys($vars) as $attr) {
			if (is_array($vars[$attr])) {
				$loc = get_all_vars("$prefix$attr.", $vars[$attr], $all_vars);
			} else {
				$all_vars["$prefix$attr"] = $vars[$attr];
			}
		}

		return $all_vars;
	}

	$vars = get_all_vars('', $vars);

	$functions = [
		'toUpperCase' => 'strtoupper',
		'toLowerCase' => 'strtolower',
	];

	$output = preg_replace_callback('/{{( *[a-zA-Z0-9.\(\)]+ *)}}/', function ($expression) use ($vars, $functions) {
		$var = trim($expression[1]);

		if (preg_match('/\.([a-zA-Z]+)\(\)/', $var, $matches)) {
			$var = substr($var, 0, - strlen($matches[0]));

			$value = $vars[$var];
			$value = call_user_func($functions[$matches[1]], $value);
		} else {
			$value = $vars[$var];
		}

		return $value;
	}, $input);

	return $output;
}

$vars = [
	'name' => 'Text Formatter',
	'description' => 'This is a text formatter for PHP using JavaScript syntax.',
	'user' => [
		'name' => 'Matheus',
	],
	'lowercase' => 'lowercase text',
	'uppercase' => 'UPPERCASE TEXT'
];

$input = <<<data
<h1>Hello World!</h1>

I am {{ user.name }}.

<h1>{{ name }}</h1>

{{ description }}

<br>

And you can transform {{ lowercase.toUpperCase() }} to uppercase and {{ uppercase.toLowerCase() }} to lowercase.
data;

echo TextFormatter($input, $vars);