<?php

/**
 * @file CustomCountryListPlugin.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CustomCountryListPlugin
 * @brief Plugin allowing adjustments to be made to the country list.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class CustomCountryListPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled($mainContextId)) {
				HookRegistry::register('IsoCodesFactory::getCountries', [&$this, 'processCountryList']);
				HookRegistry::register('IsoCodesFactory::getLocalName', [&$this, 'processCountryCode']);
			}
			return true;
		}
		return false;
	}

	/**
	 * @copydoc Plugin::isSitePlugin()
	 */
	function isSitePlugin() {
		return true;
	}

	/**
	 * Choose the best translation for a country entry given the current (user), context, and site locales.
	 * @param $countryEntry array
	 * @param $locale string
	 * @param $contextLocale string
	 * @param $siteLocale string
	 */
	protected function getBestTranslation($countryEntry, $locale, $contextLocale, $siteLocale) {
		$translationCandidates = [null, null, null]; // Reserve space for current, context, and site locale translations
		foreach ($countryEntry['translations'] as $translationEntry) switch ($translationEntry['locale']) {
			case $locale: $translationCandidates[0] = $translationEntry['translation']; break;
			case $contextLocale: $translationCandidates[1] = $translationEntry['translation']; break;
			case $siteLocale: $translationCandidates[2] = $translationEntry['translation']; break;
			default: array_push($translationCandidates, $translationEntry['translation']); break; // Provide a fallback: Any translation
		}
		$translationCandidates = array_filter($translationCandidates); // Remove empty reserved spaces
		return array_shift($translationCandidates);
	}

	/**
	 * Hook handler to process the country list.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	public function processCountryList($hookName, $args) {
		$countries =& $args[0];
		$customCountryList = json_decode($this->getSetting(CONTEXT_SITE, 'customCountryData'), true);

		// Get potentially applicable locales
		$locale = AppLocale::getLocale();
		$siteLocale = Application::getRequest()->getSite()->getPrimaryLocale();
		$context = Application::getRequest()->getContext();
		$contextLocale = $context ? $context->getPrimaryLocale() : $siteLocale;

		// Merge custom country list onto ISO3166 country list, using translations in precedence order with fallback
		if (is_array($customCountryList)) foreach ($customCountryList as $countryEntry) {
			$countries[$countryEntry['code']] = $this->getBestTranslation($countryEntry, $locale, $contextLocale, $siteLocale);
		}
		return false;
	}

	/**
	 * Hook handler to process a country code into a localized name.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	public function processCountryCode($hookName, $args) {
		$countryName =& $args[0];
		$countryCode =& $args[1];

		$customCountryList = json_decode($this->getSetting(CONTEXT_SITE, 'customCountryData'), true);
		if (is_array($customCountryList)) foreach ($customCountryList as $countryEntry) {
			if ($countryCode != $countryEntry['code']) continue;

			$locale = AppLocale::getLocale();
			$siteLocale = Application::getRequest()->getSite()->getPrimaryLocale();
			$context = Application::getRequest()->getContext();
			$contextLocale = $context ? $context->getPrimaryLocale() : $siteLocale;
			$countryName = $this->getBestTranslation($countryEntry, $locale, $contextLocale, $siteLocale);
			return false;
		}

		return false;
	}

	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.customCountryList.name');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.customCountryList.description');
	}

	/**
	 * @copydoc Plugin::manage()
	 */
	function manage($args, $request) {
		$this->import('CustomCountryListSettingsForm');
		$settingsForm = new CustomCountryListSettingsForm($this);
		switch($request->getUserVar('verb')) {
			case 'settings':
				$settingsForm->initData();
				return new JSONMessage(true, $settingsForm->fetch($request));
			case 'save':
				$settingsForm->readInputData();
				if ($settingsForm->validate()) {
					$settingsForm->execute();
					$notificationManager = new NotificationManager();
					$notificationManager->createTrivialNotification(
						$request->getUser()->getId(),
						NOTIFICATION_TYPE_SUCCESS,
						array('contents' => __('plugins.generic.customCountryList.settings.saved'))
					);
					return new JSONMessage(true);
				}
				return new JSONMessage(true, $settingsForm->fetch($request));
		}
		return parent::manage($args, $request);
	}


	//
	// Implement template methods from GenericPlugin.
	//
	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $verb)
		);
	}

}

