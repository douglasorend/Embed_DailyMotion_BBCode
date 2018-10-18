<?php
/**********************************************************************************
* Subs-BBCode-DailyMotion.php
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

function BBCode_DailyMotion(&$bbc)
{
	// Format: [DailyMotion width=x height=x frameborder=x]{DailyMotion ID}[/DailyMotion]
	$bbc[] = array(
		'tag' => 'dailymotion',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+)'),
			'frameborder' => array('optional' => true, 'match' => '(\d+)'),
		),
		'validate' => 'BBCode_DailyMotion_Validate',
		'content' => '{width}|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [DailyMotion width=x height=x frameborder=x]{DailyMotion ID}[/DailyMotion]
	$bbc[] = array(
		'tag' => 'dailymotion',
		'type' => 'unparsed_content',
		'parameters' => array(
			'frameborder' => array('match' => '(\d+)'),
		),
		'validate' => 'BBCode_DailyMotion_Validate',
		'content' => '0|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [DailyMotion]{DailyMotion ID}[/DailyMotion]
	$bbc[] = array(
		'tag' => 'dailymotion',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_DailyMotion_Validate',
		'content' => '0|0',
		'disabled_content' => '$1',
	);
}

function BBCode_DailyMotion_Button(&$buttons)
{
	$buttons[count($buttons) - 1][] = array(
		'image' => 'DailyMotion',
		'code' => 'DailyMotion',
		'description' => 'DailyMotion',
		'before' => '[dailymotion]',
		'after' => '[/dailymotion]',
	);
}

function BBCode_DailyMotion_Validate(&$tag, &$data, &$disabled)
{
	global $txt, $modSettings;
	
	if (empty($data))
		return ($tag['content'] = $txt['DailyMotion_no_post_id']);
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	$pattern = '#(http|https)://(|(.+?).)dailymotion.com/(embed/video|video)/([\w\d]+)#i';
	if (!preg_match($pattern, $data, $parts))
		return ($tag['content'] = $txt['DailyMotion_no_post_id']);
	$data = $parts[5];

	list($width, $frameborder) = explode('|', $tag['content']);
	if (empty($width))
		$width = !empty($modSettings['DailyMotion_default_width']) ? $modSettings['DailyMotion_default_width'] : false;
	$tag['content'] = '<div style="max-width: ' . (empty($width) ? '100%;' : $width . 'px;') . '"><div class="DailyMotion-wrapper">' .
		'<iframe src="http://www.dailymotion.com/embed/video/' . $data .'?api=true" scrolling="no" frameborder="' . $frameborder . '" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div></div>';
}

function BBCode_DailyMotion_LoadTheme()
{
	global $context, $settings;
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/BBCode-DailyMotion.css" />';
	$context['allowed_html_tags'][] = '<iframe>';
}

function BBCode_DailyMotion_Settings(&$config_vars)
{
	$config_vars[] = array('int', 'DailyMotion_default_width');
}

function BBCode_DailyMotion_Embed(&$message, &$smileys, &$cache_id, &$parse_tags)
{
	if ($message === false)
		return;
	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$0[/url]' : '[dailymotion]$0[/dailymotion]');
	$pattern = '~(?<=[\s>\.(;\'"]|^)(http|https)://(|(.+?).)dailymotion.com/(embed/video|video)/([\w\d]+)\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);
	if (strpos($cache_id, 'sig') !== false)
		$message = preg_replace('#\[dailymotion.*\](.*)\[\/dailymotion\]#i', '[url]$1[/url]', $message);
}

?>