<?php

/**
 * @file plugins/generic/customCountryList/CustomCountryListSettingsForm.inc.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CustomCountryListSettingsForm
 * @ingroup plugins_generic_customCountryList
 *
 * @brief Form for journal managers to modify usage statistics plugin settings.
 */

import('lib.pkp.classes.form.Form');

class CustomCountryListSettingsForm extends Form {

	/** @var $plugin CustomCountryListPlugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $plugin CustomCountryListPlugin
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * @copydoc Form::initData()
	 */
	function initData() {
		$plugin = $this->plugin;
		$this->setData('customCountryData', $plugin->getSetting(CONTEXT_SITE, 'customCountryData'));
	}

	/**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign([
			'pluginName' => $this->plugin->getName(),
		]);
		return parent::fetch($request, $template, $display);
	}
	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars([
			'customCountryData',
		]);
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
		$plugin = $this->plugin;

		$request = Application::get()->getRequest();
		$plugin->updateSetting(CONTEXT_SITE, 'customCountryData', $this->getData('customCountryData'), 'string');

		parent::execute(...$functionArgs);
	}
}
