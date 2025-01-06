/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import apiFetch from '@wordpress/api-fetch';
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalVStack as VStack,
	PanelBody,
	Button,
	CheckboxControl,
} from '@wordpress/components';
import { archive } from '@wordpress/icons';
import { store as preferencesStore } from '@wordpress/preferences';

/**
 * Internal dependencies
 */
import ScreenHeader from './screen-header';

const PREFERENCE_SCOPE = 'create-block-theme';
const PREFERENCE_KEY = 'save-changes';

export const SaveThemePanel = () => {
	const preference = useSelect( ( select ) => {
		const _preference = select( preferencesStore ).get(
			PREFERENCE_SCOPE,
			PREFERENCE_KEY
		);
		return {
			saveStyle: _preference?.saveStyle ?? true,
			saveTemplates: _preference?.saveTemplates ?? true,
			processOnlySavedTemplates:
				_preference?.processOnlySavedTemplates ?? true,
			savePatterns: _preference?.savePatterns ?? true,
			saveFonts: _preference?.saveFonts ?? true,
			removeNavRefs: _preference?.removeNavRefs ?? false,
			localizeText: _preference?.localizeText ?? false,
			localizeImages: _preference?.localizeImages ?? false,
			removeTaxQuery: _preference?.removeTaxQuery ?? false,
		};
	}, [] );

	const { createErrorNotice } = useDispatch( noticesStore );
	const { set: setPreference } = useDispatch( preferencesStore );

	const handleTogglePreference = ( key ) => {
		setPreference( PREFERENCE_SCOPE, PREFERENCE_KEY, {
			...preference,
			[ key ]: ! preference[ key ],
		} );
	};

	const handleSaveClick = () => {
		apiFetch( {
			path: '/create-block-theme/v1/save',
			method: 'POST',
			data: preference,
			headers: {
				'Content-Type': 'application/json',
			},
		} )
			.then( () => {
				// eslint-disable-next-line no-alert
				window.alert(
					__(
						'Theme saved successfully. The editor will now reload.',
						'create-block-theme'
					)
				);

				const searchParams = new URLSearchParams(
					window?.location?.search
				);
				// If user is editing a pattern and savePatterns is true, redirect back to the patterns page.
				if (
					preference.savePatterns &&
					searchParams.get( 'postType' ) === 'wp_block' &&
					searchParams.get( 'postId' )
				) {
					window.location =
						'/wp-admin/site-editor.php?postType=wp_block';
				} else {
					// If user is not editing a pattern, reload the editor.
					window.location.reload();
				}
			} )
			.catch( ( error ) => {
				const errorMessage =
					error.message ||
					__(
						'An error occurred while attempting to save the theme.',
						'create-block-theme'
					);
				createErrorNotice( errorMessage, { type: 'snackbar' } );
			} );
	};

	return (
		<PanelBody>
			<ScreenHeader
				title={ __( 'Save Changes', 'create-block-theme' ) }
			/>
			<VStack spacing={ 4 }>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __( 'Save Fonts', 'create-block-theme' ) }
					help={ __(
						'Save activated fonts in the Font Library to the theme. Remove deactivated theme fonts from the theme.',
						'create-block-theme'
					) }
					checked={ preference.saveFonts }
					onChange={ () => handleTogglePreference( 'saveFonts' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __( 'Save Style Changes', 'create-block-theme' ) }
					help={ __(
						'Save Global Styles values set in the Editor to the theme.',
						'create-block-theme'
					) }
					checked={ preference.saveStyle }
					onChange={ () => handleTogglePreference( 'saveStyle' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __(
						'Save Template Changes',
						'create-block-theme'
					) }
					help={ __(
						'Save Template and Template Part changes made in the Editor to the theme.',
						'create-block-theme'
					) }
					checked={ preference.saveTemplates }
					onChange={ () => handleTogglePreference( 'saveTemplates' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __(
						'Process Only Modified Templates',
						'create-block-theme'
					) }
					help={ __(
						'Process only templates you have modified in the Editor. Any templates you have not modified will be left as is.',
						'create-block-theme'
					) }
					disabled={ ! preference.saveTemplates }
					checked={
						preference.saveTemplates &&
						preference.processOnlySavedTemplates
					}
					onChange={ () =>
						handleTogglePreference( 'processOnlySavedTemplates' )
					}
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __( 'Save Synced Patterns', 'create-block-theme' ) }
					help={ __(
						'Any synced patterns created in the Editor will be moved to the theme. Note that this will delete all synced patterns from the Editor and any references in templates will be made relative to the theme.',
						'create-block-theme'
					) }
					checked={ preference.savePatterns }
					onChange={ () => handleTogglePreference( 'savePatterns' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __( 'Localize Text', 'create-block-theme' ) }
					help={ __(
						'Any text in a template or pattern will be localized in a pattern.',
						'create-block-theme'
					) }
					disabled={
						! preference.saveTemplates && ! preference.savePatterns
					}
					checked={
						( preference.saveTemplates ||
							preference.savePatterns ) &&
						preference.localizeText
					}
					onChange={ () => handleTogglePreference( 'localizeText' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __( 'Localize Images', 'create-block-theme' ) }
					help={ __(
						'Any images in a template or pattern will be copied to a local /assets folder and referenced from there via a pattern.',
						'create-block-theme'
					) }
					disabled={
						! preference.saveTemplates && ! preference.savePatterns
					}
					checked={
						( preference.saveTemplates ||
							preference.savePatterns ) &&
						preference.localizeImages
					}
					onChange={ () =>
						handleTogglePreference( 'localizeImages' )
					}
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __(
						'Remove Navigation Refs',
						'create-block-theme'
					) }
					help={ __(
						'Remove Navigation Refs from the theme returning your navigation to the default state.',
						'create-block-theme'
					) }
					disabled={
						! preference.saveTemplates && ! preference.savePatterns
					}
					checked={
						( preference.saveTemplates ||
							preference.savePatterns ) &&
						preference.removeNavRefs
					}
					onChange={ () => handleTogglePreference( 'removeNavRefs' ) }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					label={ __(
						'Remove Taxonomy Query',
						'create-block-theme'
					) }
					help={ __(
						'Remove the taxonomy query from the query loop block attributes.',
						'create-block-theme'
					) }
					disabled={
						! preference.saveTemplates && ! preference.savePatterns
					}
					checked={ preference.removeTaxQuery }
					onChange={ () =>
						handleTogglePreference( 'removeTaxQuery' )
					}
				/>
				<Button
					variant="primary"
					icon={ archive }
					onClick={ handleSaveClick }
				>
					{ __( 'Save Changes', 'create-block-theme' ) }
				</Button>
			</VStack>
		</PanelBody>
	);
};
