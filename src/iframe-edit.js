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
	PanelBody,
	TextControl,
	RangeControl,
	FormTokenField
} from '@wordpress/components';

export default function IframeEdit( props ) {
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;

	const [ loading, setLoading ] = useState( false );
	const { genre, age, iframeHeight, camType, camName, iframeUrl, camLang, tags } = attributes;
	const camTypeHelp = {
		'popular': __( 'It will randomly show a live cam from the most popular cams according to your filters', 'amateur-tv' ),
		'camname': __( 'It will show the cam of the below mentioned username, even if it is offline. If the name doesn\'t exist, it will show a random cam from the same genre', 'amateur-tv' ),
		'camparam': __( 'It will show the cam from the parameter on the URL with the name "livecam". If the name doesn\'t exist, it will show a random cam from the same genre', 'amateur-tv' ),
	};

	const [ url, setURL ] = useState( new URL( iframeUrl + iframeHeight ) );

	let iframe =
		'<iframe width="100%" height="' + iframeHeight + '" src=' +
		url.toString() +
		' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>';

	
	const [ html, setHTML ] = useState( iframe );

	const resetIframe = () => {
		setHTML(
			'<iframe width="100%" height="' + iframeHeight + '" src=' +
				url.toString() +
				' frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>'
		);
	};

	const changeURL = ( args ) => {
		let _url = url;
		let val = args.val;
		if ( !! args.multiple ) {
			val = val.join( ',' );
		}
		if ( !! val ) {
			_url.searchParams.set( args.name, val );
		} else {
			_url.searchParams.delete( args.name );
		}

		setURL( url );
		resetIframe();
	};

	const onChangeGender = ( gender ) => {
		setAttributes( { genre: gender } );
		changeURL( { name: 'genre', val: gender, multiple: true } );
	};
	const onChangeAge = ( age ) => {
		setAttributes( { age: age } );
		changeURL( { name: 'age', val: age, multiple: true } );
	};
	const onChangeCamType = ( type ) => {
		setAttributes( { camType: type } );
		changeURL( { name: 'livecam', val: '' } );
	};
	const onChangeCamName = ( name ) => {
		setAttributes( { camName: name } );
		changeURL( { name: 'livecam', val: name } );
	};
	const onChangeCamLamg = ( lang ) => {
		setAttributes( { camLang: lang } );
		changeURL( { name: 'camLang', val: lang, multiple: true } );
	};
	const onChangeTags = ( tags ) => {
		const lowerCaseTags = tags.map((tag) => tag.toLowerCase());
		setAttributes( { tags: lowerCaseTags } );
		changeURL( { name: 'tag', val: lowerCaseTags, multiple: true } );
	};
	const onChangeIframeHeight = ( height ) => {
		setAttributes( { iframeHeight: height } );
		resetIframe();
	};

	// allow only Latin alphabets
	const validateTag = ( tag ) => {
		return /^[aA-zZ]+$/.test(tag)
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

					<SelectControl
						label={ __( 'Language', 'amateur-tv' ) }
						value={ camLang }
						multiple={ true }
						help={ __( 'Language spoken by model', 'amateur-tv' ) }
						options={ [
							{
								label: __( 'English' ),
								value: 'en',
							},
							{
								label: __( 'Spanish' ),
								value: 'es',
							},
							{
								label: __( 'French' ),
								value: 'fr',
							},
							{
								label: __( 'German' ),
								value: 'de',
							},
							{
								label: __( 'Russian' ),
								value: 'ru',
							},
							{
								label: __( 'Italian' ),
								value: 'it',
							},
							{
								label: __( 'Portugese' ),
								value: 'pt',
							},
							{
								label: __( 'Chinese' ),
								value: 'zh',
							},
						] }
						onChange={ onChangeCamLamg }
					/>

					<FormTokenField
						label={ __( 'Tags', 'amateur-tv' ) }
						value={ tags }
						tokenizeOnSpace={ true }
						__experimentalValidateInput={ validateTag }
						onChange={ onChangeTags }
					/>

					<SelectControl
						label={ __( 'Cam Type', 'amateur-tv' ) }
						value={ camType }
						options={ [
							{ label: __( 'Most Popular', 'amateur-tv' ), value: 'popular' },
							{ label: __( 'Specific Camname', 'amateur-tv' ), value: 'camname' },
							{ label: __( 'Camname Parameter', 'amateur-tv' ), value: 'camparam' },
						] }
						help={ camTypeHelp[camType] }
						onChange={ onChangeCamType }
					/>

					{ ('camname' === camType) && (
						<TextControl
							label={ __( 'Camname', 'amateur-tv' ) }
							value={ camName }
							onChange={ onChangeCamName }
						/>
					)}
				</PanelBody>

				<PanelBody
					title={ __( 'Display Settings', 'amateur-tv' ) }
					initialOpen={ true }
				>
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
