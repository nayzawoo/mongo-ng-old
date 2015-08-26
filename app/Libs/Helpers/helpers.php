<?php

/**
 * isset() ? some : some;
 */
if (!function_exists('issetOr')) {
	function issetOr(&$var, $default = null) {
		return isset($var) ? $var : $default;
	}
}

// route ignore for #
function route_to($route, $params = []) {
	if ($route == "#") {
		return "#";
	}

	return route($route, $params);
}

/**
 * JavaScripts shortcut
 */
if (!function_exists('js')) {
	function js($scripts, $default='') {
		$html = '';
		foreach ($scripts as $script) {
			$html .= HTML::script($default . $script . '.js');
		}
		return $html;
	}
}

/*
 * CSS shortcut
 */
if (!function_exists('css')) {
	function css($styles, $default='') {
		$html = '';
		foreach ($styles as $style) {
			$html .= HTML::style($default . $style . '.css');
		}
		return $html;
	}
}

/**
 * Die and dump all request inputs
 */
if (!function_exists('inputs')) {
	function inputs($name = null) {
		if ($name) {
			dd(\Request::get($name));
		}

		dd(\Request::all());
	}
}

if (!function_exists('c')) {
	function c($config, $default = null) {
		return config($config, $default);
	}
}

/**
 * 1 minutes ago, 2 hours ago, 4 years ago from now
 */
if (!function_exists('getTimeElapsedString')) {
	function getTimeElapsedString($ptime) {
		if ($ptime === '-') {
			return '-';
		}

		$etime = time() - $ptime;

		if ($etime < 1) {
			return '0 seconds';
		}

		$a        = array(365 * 24 * 60 * 60 => 'year', 30 * 24 * 60 * 60 => 'month', 24 * 60 * 60 => 'day', 60 * 60 => 'hour', 60 => 'minute', 1 => 'second');
		$a_plural = array('year' => 'years', 'month' => 'months', 'day' => 'days', 'hour' => 'hours', 'minute' => 'minutes', 'second' => 'seconds');

		foreach ($a as $secs => $str) {
			$d = $etime / $secs;
			if ($d >= 1) {
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
			}
		}
	};
}

if (!function_exists('isTryly')) {
	function isTryly($value) {
		if (
			strtolower($value) == 'false' || strtolower($value) == 'no' || !$value
		) {
			return false;
		}
		return true;
	}
}

if (!function_exists('generateRandomString')) {
	function generateRandomString($length = 10) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString     = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}