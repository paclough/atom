<?php

class umlRequestMaterialPluginConfiguration extends sfPluginConfiguration {
	public static
		$summary = 'UML Request material',
		$version = '1.0.0';
	const REGEX_ID   = '\d+';

	public function initialize() {
		$enabledModules   = sfConfig::get( 'sf_enabled_modules' );
		$enabledModules[] = 'umlRequestMaterialPlugin';
		sfConfig::set( 'sf_enabled_modules', $enabledModules );
	}
}

