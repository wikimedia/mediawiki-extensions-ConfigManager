<?php

namespace ConfigManager\Special;

use BlueSpice\ConfigDefinition;
use BlueSpice\Services;
use BlueSpice\Special\ManagerBase;

class ConfigManager extends ManagerBase {

	public function __construct() {
		parent::__construct(
			'ConfigManager',
			'configmanager-viewspecialpage'
		);
	}

	/**
	 *
	 * @param string $param
	 */
	public function execute( $param ) {
		parent::execute( $param );

		$this->checkReadOnly();
		$this->getOutput()->enableOOUI();
	}

	/**
	 *
	 * @param ConfigDefinition $cfgDef
	 * @param array &$pathMessages
	 */
	protected function extractPathMessageKeys( $cfgDef, &$pathMessages ) {
		$msgFactory = Services::getInstance()->getService( 'BSSettingPathFactory' );
		foreach ( $cfgDef->getPaths() as $path ) {
			foreach ( explode( '/', $path ) as $section ) {
				$msgKey = $msgFactory->getMessageKey( $section );
				if ( !$msgKey ) {
					continue;
				}
				$pathMessages[$section] = $msgKey;
			}
		}
	}

	/**
	 * @return string ID of the HTML element being added
	 */
	protected function getId() {
		return 'configmanager';
	}

	/**
	 * @return array
	 */
	protected function getModules() {
		return [
			'ext.configmanager',
			'ext.configmanager.styles'
		];
	}

	/**
	 *
	 * @return array
	 */
	protected function getJSVars() {
		$cfgDefFactory = Services::getInstance()
			->getService( 'BSConfigDefinitionFactory' );
		$pathMessages = [];

		foreach ( $cfgDefFactory->getRegisteredDefinitions() as $name ) {
			$cfgDef = $cfgDefFactory->factory( $name );
			if ( !$cfgDef ) {
				continue;
			}
			$this->extractPathMessageKeys( $cfgDef, $pathMessages );
		}

		return [
			'ConfigManagerPathMessages' => $pathMessages
		];
	}
}
