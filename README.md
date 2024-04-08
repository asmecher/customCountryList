# Custom Country List Plugin
An OJS/OMP/OPS plugin to permit end users to extend and override the ISO3166 country list.

### Installation

At the moment this plugin is *not* included in the Plugin gallery because it requires patches to be applied to the application. To install the plugin:

1. Download the `.tar.gz` package of the plugin the Releases area, making sure that it's noted compatible with your OJS/OMP/OPS.
2. Apply the patches from the `patches/` directory inside the `.tar.gz` package. The `ojs.diff` patch applies to the OJS installation directory; the `pkp-lib.diff` patch applies in `lib/pkp`. (OMP and OPS patches are not yet prepared -- please request them if needed by [filing an issue](https://github.com/asmecher/customCountryList/issues).) **If you are not sure how to apply patches, it's best to stop here -- the plugin will not work without this step.**
3. Log into OJS as a Site Administrator. (A Journal Manager account will not be enough -- this plugin affects all journals in the site!)
4. Go to Settings > Website > Plugins. Use the "Upload A New Plugin" tool to upload the `.tar.gz` file you downloaded in step 1.
5. Enable the plugin in the list.
6. Click "Settings" next to the plugin to enter country list settings.
7. Enter the desired changes using the form, then click Save.

The country list should be customized as specified.
