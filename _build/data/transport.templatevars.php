<?php
$tvs = array();
$tmp = array(
//	'modExtra' => array(
//		'caption'   => '',
//		'description' => '',
//		'type'      => 'textfield',
//		'display' => 'default',
//		'elements' => '',  /* input option values */
//		'locked' => 0,
//		'rank' => 0,
//		'display_params' => '',
//		'default_text' => '',
//		'properties' => array(),
//		'input_properties'  => array(
//			'allowBlank'    => true,
//			'maxLength'     => '',
//			'minLength' => '',
//		),
//	),
);


foreach ($tmp as $k => $v) {
	/* @avr modTemplate $template */
	$tv = $modx->newObject('modTemplateVar');
	$tv->fromArray(array(
		'id' => 0,
		'name' => $k,
		'caption'   => @$v['caption'],
		'description' => @$v['description'],
		'type'      => @$v['type'],
		'display' => @$v['display'],
		'elements' => @$v['elements'],
		'locked' => @$v['locked'],
		'rank' => @$v['rank'],
		'display_params' => @$v['display_params'],
		'default_text' => @$v['default_text'],
		'properties' => @$v['properties'],
		'input_properties'  => @$v['input_properties'],
		'static' => BUILD_TV_STATIC,
	),'',true,true);
	$tvs[] = $tv;
}
unset($tmp);
return $tvs;