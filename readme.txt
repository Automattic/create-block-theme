=== Create Block Theme ===
Contributors: wordpressdotorg, mikachan, onemaggie, pbking, scruffian, mmaattiiaass, jffng, madhudollu, egregor, vcanales, jeffikus, cwhitmore
Tags: themes, theme, block-theme
Requires at least: 6.6
Tested up to: 6.7
Stable tag: 2.6.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin to create block themes.

== Description ==

This plugin allows you to:

- Create a blank theme
- Create a new theme based on the currently active theme
- Create a child theme of the active parent theme
- Create a new style variation
- Export a theme
- Save user changed templates and styles to the active theme

All newly created themes or style variations will include changes made within the WordPress Editor.

This plugin also makes several changes to the contents of a theme, including:

- Adds all images used in templates to the theme's `assets` folder.
- Ensures the block markup used in templates and patterns is export-ready.
- Ensures most strings used in templates and patterns are translate-ready.

*Disclaimer:* The Create Block Theme plugin offers critical developer-friendly features; you can think of it as a Development Mode for WordPress, and you should *keep in mind that changes made through this plugin could change your site and/or theme permanently*.

(Make sure you know what you're doing before hitting that 'Save' button 😉)

= Step 1 – Setup =
Install and activate the [Create Block Theme](https://wordpress.org/plugins/create-block-theme) plugin.

There will be a new panel accessible from the WordPress Editor, which you can open by clicking on a new icon to the right of the "Save" button, at the top of the Editor.

= Step 2 – Style Customizations =
Make changes to your site styles, fonts and templates using the Editor.

= Step 3 – Export =
Still in the WordPress Editor, navigate to the Create Block Theme menu at the top of the Editor.

To save recent changes made in the Editor to the currently active theme:

- Select "Save Changes" to save any recent changes to the currently active theme.

To install and uninstall fonts:

- Install and activate a font from any source using the WordPress Font Library.
- Select "Save Changes To Theme" and select "Save Fonts" to save all of the active fonts to the currently active theme. These fonts will then be activated in the theme and deactivated in the system (and may be safely deleted from the system).
- Any fonts that are installed in the theme that have been deactivated with the WordPress Font Library will be removed from the theme.

or export the theme:

- Select "Export Zip" to export the theme as a zip file.

To edit the theme metadata:

- Select "Edit Theme Metadata" to edit the metadata for the theme. These details will be used in the style.css file.

To inspect the active theme's theme.json contents:

- Select "Inspect Theme JSON"

To create a new blank theme:

- Select "Create Blank Theme"
- Supply a name for the new theme (and optional additional Metadata)
- Click "Create Blank Theme"

To create a variation:

- Select "Create Theme Variation"
- Provide a name for the new Variation
- Click "Create Theme Variation"

To create a new Clone of the current theme or to create a Child of the current theme:

- Click "Create Theme"
- Click "Clone Theme" to create a new Theme based on the active theme with your changes
- Click "Create Child Theme" to create a new Child Theme with the active theme as a parent with your changes

Many of these options are also available under the older, deprecated Create Block Theme page under Appearance > Create Block Theme.

== Frequently Asked Questions ==

= How do I get support? =

If you run into an issue, you should check the [Support forum](https://wordpress.org/support/plugin/create-block-theme/) first. The forum is a great place to get help.

= How do I report an issue? =

If you have a bug to report, please submit it to the [GitHub repository](https://github.com/WordPress/create-block-theme/issues) as an issue. Please search prior to creating a new bug to confirm its not a duplicate.

== General Troubleshooting ==

If you are having problems, please try the following:

- Make sure you have the latest version of WordPress installed.
- Make sure you have the latest version of the plugin installed.
- Deactivate all plugins and see if this resolves the problem. If this works, re-activate the plugins one by one until you find the problematic plugin(s).
- Switch the theme to the default theme to rule out any theme-related problems.
- Check the [Support forum](https://wordpress.org/support/plugin/create-block-theme/) for similar issues.

== I'm getting a corrupt zip file when I export my theme ==

- First follow the general troubleshooting steps above.
- Please make sure you `WP_DEBUG` setting in your `wp-config.php` file is set to `false` like this: `define( 'WP_DEBUG', false );`.
- If your theme includes PHP files, ensure those files do **not** use PHP closing tags `?>` at the end of the file. If they do, remove them.


== Screenshots ==
1. Create Block Theme panel in the WordPress Editor
2. Create Block Theme save panel in the WordPress Editor
3. Theme Metadata editing panel in the WordPress Editor
4. theme.json inspector in the WordPress Editor
5. Create Theme panel 1 in the WordPress Editor
6. Create Theme panel 2 in the WordPress Editor
7. Page under Appearance > Create Block Theme


== Changelog ==

= 2.6.0 =
* Process inner html of blocks when escaping text content (#719)
* Removed all of the code relating to 'folder management' of a theme. (#723)
* Fix Image URL Localization URI (#720)
* Add __nextHasNoMarginBottom to BaseControl-based components (#729)

= 2.5.0 =
* Copy font assets to the local theme folder when creating a style variation (#713)
* Adds minimum WordPress version to theme metadata (#715)
* Add a main tag wrapper around the query loop (#726)
* Remove "Categories: hidden" from default pattern header (#718)
* Fix "troobleshooting" typo in readme.txt (#721)
* Update node dependencies (#717)
* Try: Add synced patterns to theme on save (#675)
* Rename font assets when theme is saved and/or exported (#712)
* Update escaping function (#683)
* Redirect to proper editor_url (#708)
* Update README.md disclaimer wording and formatting (#703)

= 2.4.0 =
* Bump minimum required WordPress version to 6.6 (#704)
* Don't enable sidebar UI in classic theme (#705)
* README markdown formatting (#702)
* Update broken link in README (#701)
* Update blank themes theme json version (#700)
* Global Styles JSON data inspector (#697)
* Delete only theme-related caches when saving changes (#685)
* Update "Tested up to" version to 6.6 (#694)
* Add theme reset section (#687)
* Sanitize DOS (Windows) new line style on readme.txt (#681)

= 2.3.0 =
* Persist font settings when cloning a theme (#678)
* Landing Page: Improve design (#673)
* Fix small readme typo (#674)
* A11y: Improve color contrast for help button (#672)
* Quality: Fix warning error when exporting theme (#671)
* Remove unused REST API endpoint (#670)
* Refactor theme fonts class for readability (#661)
* Check if theme fonts present before removing (#660)
* Add an about section in the editor (#667)
* Update escaping function (#665)
* Make external links translatable (#663)
* Update url for blueprint (#658)
* Add image credits edit capabilities to the edit theme modal (#662)
* Quality: Remove unused PHP classes (#664)

= 2.2.0 =
* Update modal width to 65vw (#638)
* Fixed font utilities to work with font sources as an (optional) array. (#645)
* Handle font licenses when editing theme metadata (#649)
* Adds an endpoints that returns a list of font families used by a theme (#648)
* Flush cache after creating new themes (#654)
* Replace/admin interface (#637)
* Added subfolder to initial theme state to eliminate render error (#652)
* Fix the jslint warning (or infinate loop error when fixed) from useSelect usage (#651)
* Enforce specifying which eslint rule is disabled when using eslint disable comments (#650)
* Handle font credits in the backend (#647)
* Move lib-font and add GPL license text (#646)

= 2.1.4 =
* Fix template texts localizing/escaping (#641)
* Use only major.minor version in 'Tested up to' field (#635)
* Don't Clobber Metadata (#634)
* Clean and complete the changelog (#636)
* Add prefix to the names in the PHP global namespace. (#628)
* Improve tags UI (#630)
* Refactor Theme_Readme (readme.txt) PHP class (#626)
* Metadata screenshot (#621)
* Allow spaces in slugs.  Changed logic to correctly replace functions.  Updated tests. (#622)
* Reset changelog and version on clone and theme creation (#623)
* Use non-default port for wp-env (#611)
* Update package-lock.json (#620)
* Tests: use tests-wordpress wp-env for phpunit (#618)
* Add Repository Management section to contributing docs (#614)
* Update wp-env version (#619)
* Update Node version to 20 (#617)
* ESLint: Add new rules (#616)

= 2.1.3 =
* Editor Sidebar: Persist "Save Changes" panel settings (#607)
* Fix problem with zip file creation on Windows (#606)
* Fix custom fonts assets path (#601)
* Remove unused `UpdateThemePanel` component (#608)
* Check ZipArchive class before zip export (#609)
* Editor Sidebar: Make save panel text translatable (#603)
* Editor Sidebar: Improve screen title UI (#605)
* Move files (#598)

= 2.1.2 =
* Document the release process (#594)
* Make sure code is being deployed to the directory only on Release PR Merge (#593)
* Remove font management (#595)

= 2.1.1 =
* Process group background image when saving theme (#586)
* Removed unnecessary filter rejecting unsafe URLs (#588)
* Fix/cover-block-content-stripped (#587)
* When there are no fonts to export an error is thrown (null ref).  This change checks for fonts to copy to the theme before trying to. (#582)
* Fix hardcoded wp-admin URLs (#576)
* Code Quality: Remove `no-undef` eslint rule (#577)
* Move screenshot refs to screenshot section (#580)

= 2.1.0 =
* Save only templates that have been changed (#572)
* I18n: Make modal titles translatable (#575)
* Update readme with changes from UI changes and updated screenshots (#571)
* Fix concatenation of translation strings (#554)
* Include activated Fonts on theme zip export functions (#564)
* Fix/un transposed patterns (#567)
* Try/refactor editor UI (#563)
* Update readme files with editor-specific steps and screenshot references (#555)

= 2.0.2 =
* Update readme, remove test files from release build (#548)

= 2.0.1 =
* Add missing build step to deploy workflow (#546)

= 2.0.0 =
* Remove reviewer addition (#544)
* Refactor GitHub release workflows (#542)
* Fix changelog creation script (#541)
* Add theme json inspector (#520)
* Add deprecation notice in theme export admin screen (#540)
* Replace font management with screen pointing to native font library (#539)
* Added creation of theme validation to site editor interface (#532)
* Add/child theme creation (#531)
* Add blueprint.json file to enable plugin previews (#511)
* Extracted any logic that may need to be tested from the api class (#522)
* Use CORE for Font Management (#518)
* Add integration tests (#393)
* Remove CODE_OF_CONDUCT.md from .distignore. (#515)
* Remove repo specific CoC. (#514)
* GitHub Actions: Add JavaScript Unit Test (#508)
* Add files and directories not needed for release to `.distignore` (#512)
* Replace dash icon with SVG icon (#506)
* Fix browser warning error when clicking the reset button (#505)
* Add markdown and package.json lint command (#504)
* Fix react warning error on font upload page (#502)
* Fix dynamic property deprecation (#501)
* Add text domain to translation target (#499)

= 1.13.8 =
* Remove the development-only warning

= 1.13.7 =
* docs: Add recent release notes to the changelog

= 1.13.6 =
* Fix manage fonts page

= 1.13.5 =
* Make form files more specific to form page
* Set page titles that set only within CBT

= 1.13.4 =
* Bump "tested up to" to 6.4
* Update Google Fonts JSON data from API
* Update theme form markup and styles
* Fix/child export
* Corrects malformed input tag
* Add quotes to font family names

= 1.13.3 =

* Update "Tested up to" version to 6.3
* Add `.wp-env.override.json` to `.gitignore`
* Use wp_add_inline_script for createBlockTheme object
* Update Google Fonts JSON data from API
* Updated Requires PHP version to 7.4
* Fix PHP 8.1 errors caused by missing page titles

= 1.13.2 =
* Update Google Fonts JSON data from API
* Set the initial version to 1.0.0 instead of 0.0.1
* Fix: react render warning
* Introduce basic wp-env environment

= 1.13.1 =
* Add default value for recommended plugins section
* Update Google Fonts JSON data from API

= 1.13.0 =
* Persist copyright info on new theme creation
* Update Google Fonts JSON data from API
* Move check for `download_url` higher up
* Avoid white spaces or other weird characters on font asset paths.
* Adding files to zip subfolder called as theme slug
* Update Google Fonts JSON data from API

= 1.12.1 =
* Fix double replacement in replace_namespace

= 1.12.0 =
* Add image credits input
* Update theme `version` logic to use isset()
* Update Google Fonts JSON data from API

= 1.11.0 =
* Update Google Fonts JSON data from API
* Add linebreaks before hyphen lists in readme to fix plugin repository display
* Prevent additional white space in font credits in readme.txt
* Google fonts: Change onClick handlers to onChange
* Escape special characters to avoid syntax errors
* Update required node version and update dependencies

= 1.10.0 =
* Update Google Fonts JSON data from API
* Adding troubleshooting FAQs
* Updating "Requires at least" field of generated themes
* Improve handling of font license errors
* Fix `tabIndex` prop
* Automatically add font license info for local fonts

= 1.9.0 =
* Handle Google Font Credits
* Update Google Fonts JSON data from API
* Fix console error in `prepareThemeNameValidation` function
* Add FAQ section to readme.txt
* Automatically add font license info for Google fonts
* Removing donate link

= 1.8.2 =
* Bump tested version
* Updating Tested up to: 6.2 WordPress version
* fix tag duplication in exported theme
* Fixing error checking
* Update Google Fonts JSON data from API
* Refactor react app code for general purpose
* add build directory to php exclude list
* Do not call replace_template_namespace when overwrting theme
* Fix error when switching to template edit mode in the post editor
* Add useRootPaddingAwareAlignments to blank theme
* Update Google Fonts JSON data from API
* Avoid adding Template info to style.css if it's empty
* Fix delete font family/face when name is different from family
* Add theme name validation
* Fix export theme from Site Editor
* Strip escaping characters before printing stylesheet
* Linting unlinted file

= 1.8.1 =
* Add current WordPress version to style.css and readme.txt
* Add labels around Google font family checkbox controls
* Fix theme slug, textdomain, and template for cloned, child and sibling themes.
* Replace theme slug in templates after getting media urls from them

= 1.8.0 =
* Export style variations just with the changes made by the user
* fix issue where package-lock is not updated on version bump
* Adding default value to an to avoid error when calling export_theme_data()
* Fixing image downloading not working in some cases
* Update Google Fonts JSON data from API
* Add Export (Clone) to site editor

= 1.7.1 =
* Update screenshots
* Fix manage fonts UI and backend when no settings are defined in theme.json
* Variable font weight range

= 1.7.0 =
* Manage fonts minor refactor. Move elements from PHP to react
* Allow otf font file upload
* Local fonts section implementation in React
* Fonts outline sidebar
* Update Google Fonts JSON data from API

= 1.6.3 =
* (Fix refactor regression) Remove white spaces from theme slug

= 1.6.2 =
* Refactor font-management class
* Refactor create-block-theme class
* fix manage theme font menu casing
* Add phpcs exception to avoid PHP8.0 incompatibility errors
* Fix blank theme screenshot fatal error
* Fix lint issues using auto fixer tool
* Update Google Fonts JSON data from API
* Fix CSS lint issues
* Fix PHP lint issues
* Fix JS lint issues
* Add lint validation to PR workflows
* fix package lock sync issue

= 1.6.1 =
* Add: input for theme tags
* Placeholder URL change to TT3
* Remove white spaces from theme slugs

= 1.6.0 =
* Update main readme and add supporting docs
* Fix Depreciation Warning
* Cleanup Manage Theme Fonts UI
* Bundle template images into theme assets and make their urls relative
* Cloned themes: Add original theme name to readme.txt and style.css
* Font families collapsed by default
* Fix: Unexpected action when clicking Collapse chevron
* Lint all CSS files
* Fix: composer scripts doesn't work on Windows
* Use Gutenberg Theme JSON resolver if its available
* Update Google Fonts JSON data from API
* Replacing mkdir() calls with WordPress wp_mkdir_p() function

= 1.5.1 =
* check for DISALLOW_FILE_EDIT and simplify permission check logic
* Load google fonts data from url
* Separate styles and templates reset
* Add spinner while google fonts load instead of showing a blank page
* Add: code linting scripts
* Update Google Fonts JSON data from API
* Avoid pre commit verifications on Github actions to prevent action errors caused by linting problems

= 1.5.0 =
* Fix: browser console errors
* Fix: Adding or removing fonts fails in some Windows environments
* Add placeholder screenshot to boilerplate theme
* Refactor: Add Google Fonts section from vanilla JS to React app
* Adding demo text settings

= 1.4.0 =
* Specify node and npm versions, add nvmrc file
* Add theme screenshot uploading
* Manage fonts: Demo text editable
* Update Google Fonts JSON data from API

= 1.3.10 =
* Remove font face: avoid unwanted removal of  fontfamily.
* Add missing spaces to option labels

= 1.3.9 =
* Updating Tested up to: 6.1
* I18N: Some new UI strings are not translatable
* Replace "current theme" with "active theme" (or "currently active theme")
* Improve translation process by removing trailing spaces
* Fonts: remove font files from theme assets folder if the font face/family is removed.
* Refactor to read raw theme.json data instead of using core methods
* Update Google Fonts JSON data from API

= 1.3.8 =
* Fixes the spelling of definition
* Fixing readme contributors
* Add contributor username to readme
* Update GitHub action to avoid deprecation warning
* Update Google Fonts JSON data from API
* Update Google Fonts JSON data from API
* Check permission before running functions that need file write permissions
* Allow previewing system font

= 1.3.7 =
* Moving assets files to be auto updated by the release action

= 1.3.6 =
* Auto update assets using a github action

= 1.3.5 =
* Auto release: commit updated php file with the new version

= 1.3.4 =
* auto update version of php file

= 1.3.3 =
* Automatic release improvements

= 1.3.2 =
* Automatic release improvements

= 1.3.1 =
* Update .distignore

= 1.3.0 =
* Updating google fonts data
* Force https to load Google fonts preview
* Add the ability to select/unselect all google font variants
* Update google fonts JSON data automatically using a repo action
* Manage theme fonts
* Automate release: build, version bump, changelog, deploy to wp.org
* Automate release

= 1.2.3 =
* Add translation domain (#121)
* Check for nonce index (#120)
* Validating mime type of font file on server side (#119)

= 1.2.2 =
* Add capabilities and nonce checks (#118)

= 1.2.1 =
* Correcting version number

= 1.2.0 =
* Embed Google fonts and local font files in theme (#113)
* Change button text (#112)
* Add check and directory creation for template and parts folders. (#110)
* Change theme.json schema of blank theme if Gutenberg isn't installed. (#107)

= 1.1.3 =
* update links, screenshots of the new changes (#97)
* Add $schema and use Gutenberg classes (#99)
* Update readme to include latest features (#100)
* Generate $schema URL in the same way as core. (#105)

= 1.1.2 =
* Save a theme variation (#90)
* Make UI string 'Create Block Theme' can be translatable (#92)

= 1.0.1 =
* Add option to create blank theme. (#70)
* Improve form instructions (#76)
* Form cleanup and Theme name check (#77)
* Get the correct merged theme.json data (#88)

= 1.0 =
* Initial version.
