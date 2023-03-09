/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
import { useEffect, useState, RawHTML } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import {
	Spinner,
	SelectControl,
	CheckboxControl,
	PanelBody,
	ToggleControl,
	RangeControl
} from '@wordpress/components';

export default function IframeEdit( props ) {
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;

	const [ loading, setLoading ] = useState( false );
	const { genre, age, iframeHeight } = attributes;

	const [ url, setURL ] = useState(
		new URL(
			'https://www.amateur.tv/freecam/embed?width=890&height=' + iframeHeight + '&lazyloadvideo=1&a_mute=1'
		)
	);

	let iframe =
		'<iframe width="100%" height="' + iframeHeight + '" src=' +
		url.toString() +
		' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>';

	const [ html, setHTML ] = useState( iframe );

	const changeURL = ( args ) => {
		let _url = url;
		let val = args.val;
		if ( !! args.multiple ) {
			val = val.join( ',' );
		}
		_url.searchParams.set( args.name, val );
		setURL( url );
		resetIframe();
	};

	const resetIframe = () => {
		setHTML(
			'<iframe width="100%" height="' + iframeHeight + '" src=' +
				url.toString() +
				' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>'
		);
	};


	const onChangeGender = ( gender ) => {
		setAttributes( { genre: gender } );
		changeURL( { name: 'genre', val: gender, multiple: true } );
	};
	const onChangeAge = ( age ) => {
		setAttributes( { age: age } );
		changeURL( { name: 'age', val: age, multiple: true } );
	};
	const onChangeIframeHeight = ( height ) => {
		setAttributes( { iframeHeight: height } );
		resetIframe();
	};
	
	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Filters', 'amateur-tv' ) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __( 'Gender', 'amateur-tv' ) }
						value={ genre }
						multiple
						options={ [
							{ label: __( 'Woman', 'amateur-tv' ), value: 'W' },
							{ label: __( 'Couple', 'amateur-tv' ), value: 'C' },
							{ label: __( 'Man', 'amateur-tv' ), value: 'M' },
							{ label: __( 'Trans', 'amateur-tv' ), value: 'T' },
						] }
						onChange={ onChangeGender }
					/>
					<SelectControl
						label={ __( 'Age', 'amateur-tv' ) }
						value={ age }
						multiple
						options={ [
							{ label: '18-22', value: '18-22' },
							{ label: '23-29', value: '23-29' },
							{ label: '29-39', value: '29-39' },
							{ label: '40', value: '40' },
						] }
						onChange={ onChangeAge }
					/>
					<RangeControl
						label={ __( 'Iframe Height', 'amateur-tv' ) }
						value={ iframeHeight }
						initialPosition={ 590 }
						onChange={ onChangeIframeHeight }
						min={ 200 }
						max={ 1000 }
						step={ 50 }
						type={ "stepper" }
						allowReset={ true }
					/>
				</PanelBody>
			</InspectorControls>

			{ !! loading && (
				<div key="loading" className="wp-block-embed is-loading">
					<Spinner />
					<p>{ __( 'Fetching...', 'amateur-tv' ) }</p>
				</div>
			) }
			<div { ...useBlockProps() }>
				<RawHTML className="atv-iframe">{ html }</RawHTML>
			</div>
		</>
	);
}
