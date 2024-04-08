<?php

/**
 * @file index.php
 *
 * Copyright (c) 2014-2024 Simon Fraser University
 * Copyright (c) 2003-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Wrapper for custom country list plugin.
 *
 */

require_once('CustomCountryListPlugin.inc.php');
return new CustomCountryListPlugin();

