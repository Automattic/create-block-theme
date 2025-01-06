/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Modal } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { fetchThemeJson } from '../resolvers';
import { CodeMirrorDiffViewer } from './code-mirror-diff-viewer';

const ThemeJsonEditorModal = ( { onRequestClose } ) => {
	const [ themeData, setThemeData ] = useState( '' );
	const themeName = useSelect( ( select ) =>
		select( 'core' ).getCurrentTheme()
	)?.name?.raw;
	const fetchThemeData = async () => {
		setThemeData( await fetchThemeJson() );
	};
	const handleSave = () => {};

	useEffect( () => {
		fetchThemeData();
	} );

	return (
		<Modal
			size="large"
			title={ sprintf(
				// translators: %s: theme name.
				__( 'theme.json for %s', 'create-block-theme' ),
				themeName
			) }
			onRequestClose={ onRequestClose }
			className="create-block-theme__theme-json-modal"
		>
			<CodeMirrorDiffViewer oldCode={JSON.stringify(themeData, null, 2)} newCode={JSON.stringify(themeData, null, 2)} />
		</Modal>
	);
};

export default ThemeJsonEditorModal;
