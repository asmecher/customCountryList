{**
 * plugins/generic/customCountryList/templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Custom country list settings form.
 *
 *}
<script src="https://cdn.jsdelivr.net/npm/@json-editor/json-editor@latest/dist/jsoneditor.min.js"></script>
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#customCountryListSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');

		// Use an interval to make sure the JSON editor JS has been loaded from the CDN.
		var interval = setInterval(function() {ldelim}
			if (typeof JSONEditor == 'undefined') return;
			clearInterval(interval);

			// Set up the JSON editor.
			const editor = new JSONEditor(document.getElementById('jsonEditorContainer'), {ldelim}
				disable_collapse: true,
				disable_edit_json: true,
				disable_properties: true,
				disable_array_reorder: true,
				startval: JSON.parse({$customCountryData|json_encode|default:"[]"}),
				schema: {ldelim}
					"$id": "https://example.com/arrays.schema.json",
					"$schema": "http://json-schema.org/draft-07/schema#",
					"description": {translate|json_encode key="plugins.generic.customCountryList.settings.countryList.description"},
					"title": {translate|json_encode key="plugins.generic.customCountryList.settings.countryList"},
					"type": "array",
					"items": {ldelim}
						"type": "object",
						"title": {translate|json_encode key="common.country"},
						"required": [
							"code"
						],
						"properties": {ldelim}
							"code": {ldelim}
								"type": "string",
								"title": {translate|json_encode key="plugins.generic.customCountryList.settings.identifier"},
								"description": {translate|json_encode key="plugins.generic.customCountryList.settings.identifier.description"},
							{rdelim},
							"translations": {ldelim}
								"type": "array",
								"title": {translate|json_encode key="plugins.generic.customCountryList.settings.translations"},
								"items": {ldelim}
									"type": "object",
									"title": {translate|json_encode key="plugins.generic.customCountryList.settings.translation"},
									"properties": {ldelim}
										"locale": {ldelim}
											"type": "string",
											"title": "Locale",
											"description": {translate|json_encode key="plugins.generic.customCountryList.settings.localeCode.description"}
										{rdelim},
										"translation": {ldelim}
											"type": "string",
											"title": {translate|json_encode key="plugins.generic.customCountryList.settings.translation"}
										{rdelim}
									{rdelim}
								{rdelim}
							{rdelim}
						{rdelim}
					{rdelim}
				{rdelim}
			{rdelim});

			// Update a hidden field with the generated JSON on changes.
			editor.on('change', function() {
				$('#customCountryData').val(JSON.stringify(this.getValue()));
			});
		{rdelim}, 10);
	{rdelim});
</script>

<form class="pkp_form" id="customCountryListSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="save"}">
	{csrf}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customCountryListSettingsFormNotification"}

	<div id='jsonEditorContainer'></div>
	{fbvElement type="hidden" id="customCountryData" name="customCountryData" value=$customCountryData}

	{fbvFormButtons id="customCountryListSettingsFormSubmit" submitText="common.save" hideCancel=true}
</form>
