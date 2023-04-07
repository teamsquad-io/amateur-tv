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
import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { createBlocksFromInnerBlocksTemplate, getBlockType } from '@wordpress/blocks';
import { useEntityProp } from '@wordpress/core-data';
import {
	Spinner,
	SelectControl,
	PanelBody,
	TextControl,
} from '@wordpress/components';

export default function CamDetailsEdit( props ) {
	const { attributes, setAttributes, clientId } = props;
	const { replaceInnerBlocks } = useDispatch("core/block-editor");

	const {
        lang,
        api,
        camType,
        camName,
		fields,
	} = attributes;

	
	const [ loading, setLoading ] = useState( true );
	const [ template, setTemplate ] = useState( null );
	const [ data, setData ] = useState( null );
	const apiUrl = new URL( api );
	const [ url, setURL ] = useState( apiUrl );
	const [ affiliateCode ] = useEntityProp( 'root', 'site', 'amateurtv_affiliate' );

	const { inner_blocks, block } = useSelect(select => ({
		block: select("core/block-editor").getBlock(clientId),
		inner_blocks: select("core/block-editor").getBlock(clientId).innerBlocks
	}));

    const camTypeHelp = {
		'camname': __( 'It will show the details of the below mentioned username, even if it is offline', 'amateur-tv' ),
		'camparam': __( 'It will show the details from the parameter on the URL with the name "livecam"', 'amateur-tv' ),
	};

	const KEY_LABELS = [
		{ value: 'username', label: __( 'Username', 'amateur-tv' ) },
		{ value: 'profilePicture', label: __( 'Profile Picture', 'amateur-tv' ), type: 'image' },
		{ value: 'followers', label: __( 'Followers', 'amateur-tv' ) },
		{ value: 'rating', label: __( 'Rating', 'amateur-tv' ) },
		{ value: 'genre', label: __( 'Genre', 'amateur-tv' ) },
		{ value: 'seniority', label: __( 'Seniority', 'amateur-tv' ) },
		{ value: 'age', label: __( 'Age', 'amateur-tv' ) },
		{ value: 'languages', label: __( 'Language(s)', 'amateur-tv' ) },
		{ value: 'location', label: __( 'Location', 'amateur-tv' ) },
		{ value: 'interestedIn', label: __( 'Interests', 'amateur-tv' ) },
		{ value: 'tags', label: __( 'Tags', 'amateur-tv' ) },
		{ value: 'lastShow', label: __( 'Last Show', 'amateur-tv' ) },
		{ value: 'aboutMe', label: __( 'About Me', 'amateur-tv' ) },
	];

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

		if('dummy' == args.name){
			_url.searchParams.set('lang', lang);
			_url.searchParams.set('camname', camName);
		}
		_url.searchParams.set('a', affiliateCode);

		if ( !!! _url.searchParams.get('lang') ) {
			_url.searchParams.set('lang', lang);
		}

		setURL( url );
		setLoading( true );
	};

	const onChangeLang = ( lang ) => {
		setAttributes( { lang: lang } );
		changeURL( { name: 'lang', val: lang, multiple: false } );
	};
    const onChangeCamType = ( type ) => {
		setAttributes( { camType: type } );
		changeURL( { name: 'camname', val: '' } );
		replaceInnerBlocks(clientId, [], false);
	};
	const onChangeCamName = ( name ) => {
		setAttributes( { camName: name } );
		changeURL( { name: 'camname', val: name } );
	};
	const onChangeFields = ( fields ) => {
		setAttributes( { fields: fields } );
		changeURL( { name: 'dummy', val: new Date().getTime() } );
	};

	const siteLang = useSelect( ( select ) => {
		let lang = select( 'core' ).getSite()?.language;
		return lang && lang.split( '_' )[ 0 ];
	} );

	const updateTemplate = (data) => {
		if ( !data) {
			setTemplate(null);
			return;
		}

		let template = [];

		!!fields && fields.map( (key, index) => {
			let displayBlock = [ 'core/paragraph', { content: String(data[key]), "data-something": key } ];
			let label, type = null;
			
			KEY_LABELS.forEach( (item) => {
				if ( key == item.value ){
					label = item.label;
					type = item.type;
				}
			});

			switch ( type ) {
				case 'image':
					displayBlock = [ 'core/image', { url: String(data[key]), "data-something": key } ];
					break;
			}

			template.push(
				[ 'core/columns', {}, [
				[ 'core/column', {}, [
					[ 'core/paragraph', { content: label } ],
				]],
				[ 'core/column', {}, [
					displayBlock,
				]]
			]]
			);
		});

		// when an inner block already exists
		if ( inner_blocks.length > 0 ) {
			let inner_blocks_new = createBlocksFromInnerBlocksTemplate( template);
			replaceInnerBlocks(clientId, inner_blocks_new, false);
		}

		setTemplate( template );

	};

	const mockTemplate = () => {
		let data = [];
		
		KEY_LABELS.forEach( (item) => {
			data[ item.value ] = item.type === 'image' ? '' : __( 'Value for the given cam', 'amateur-tv' );
		});
		setData(true);
		updateTemplate(data);
	};
	
	useEffect( () => {
		const options = {
			method: 'GET',
		};

		if ( ! loading ) return;

		// @TODO: this spoils the manual sequencing when post is refreshed
		if ( 'camparam' === camType ) {
			mockTemplate();
			setLoading( false );
			return;
		}

		fetch( url, options )
			.then( ( response ) => response.json() )
			.then( ( response ) => {
				setLoading( false );
				setData(!!response.body);
				updateTemplate(response.body);
			} )
			.catch( ( err ) => console.error( err ) ).finally( () => {setLoading( false ); });
	}, [ loading, url ] );


	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Filters', 'amateur-tv' ) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __( 'Cam Type', 'amateur-tv' ) }
						value={ !! camType ? camType : 'camname' }
						options={ [
							{ label: __( 'Specific Camname', 'amateur-tv' ), value: 'camname' },
							{ label: __( 'Camname Parameter', 'amateur-tv' ), value: 'camparam' },
						] }
						help={ camTypeHelp[camType] }
						onChange={ onChangeCamType }
					/>

					{ ('camname' === camType || !!!camType) && (
						<TextControl
							label={ __( 'Camname', 'amateur-tv' ) }
							value={ camName }
							onChange={ onChangeCamName }
						/>
					)}

					{ ( !!data || inner_blocks.length > 0 || camType === 'camparam') && (
						<SelectControl
							label={ __( 'Fields', 'amateur-tv' ) }
							multiple= { true }
							value={ fields }
							options={ KEY_LABELS }
							help={ __( 'Select the fields to display', 'amateur-tv' ) }
							onChange={ onChangeFields }
						/>
					) }

				</PanelBody>

                <PanelBody
					title={ __( 'Display Settings', 'amateur-tv' ) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __( 'Language', 'amateur-tv' ) }
						value={ !! lang ? lang : siteLang }
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
						] }
						onChange={ onChangeLang }
					/>
                </PanelBody>
			</InspectorControls>
			
            <div { ...useBlockProps() }>
				<div
					className="atv-cam-details"
				>
					{ !! loading && (
						<div key="loading" className="wp-block-embed is-loading">
							<Spinner />
							<p>{ __( 'Fetching...', 'amateur-tv' ) }</p>
						</div>
					) }

					{ !loading && (
							<InnerBlocks
								template={ template }
								allowedBlocks={ [
									'core/paragraph',
									'core/image',
									'core/button',
									'core/heading',
								]}
							/>
					) }
				</div>
			</div>
		</>
	);
}
