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
import { useSelect } from '@wordpress/data';
import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import {
	Spinner,
	SelectControl,
	PanelBody,
	ToggleControl,
	Flex,
	FlexBlock,
	FlexItem,
	TextControl,
	RangeControl,
	FormTokenField
} from '@wordpress/components';

export default function FeedEdit( props ) {
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;

	const {
		usernameColor,
		lang,
		liveColor,
		displayLive,
		displayTopic,
		displayGenre,
		displayUsers,
		bgColor,
		genre,
		age,
		topicColor,
		link,
		targetNew,
		labelBgColor,
		imageWidth,
		imageHeight,
		columnGap,
		autoRefresh,
		api,
		count,
		textShadowColor,
		textShadowValue,
		camLang,
		tags,
		order,
	} = attributes;

	const textShadow = '1px 1px';

	const [ loading, setLoading ] = useState( true );
	const [ data, setData ] = useState( null );
	const [ whiteLabel ] = useEntityProp( 'root', 'site', 'amateurtv_whitelabel' );

	const apiUrl = new URL( api );
	if ( !! whiteLabel ) {
		apiUrl.searchParams.set( 'wl', whiteLabel );
	}

	const [ url, setURL ] = useState( apiUrl );

	const changeURL = ( args ) => {
		let val = args.val;
		if ( !! args.multiple ) {
			val = val.join( ',' );
		}
		if ( !! val ) {
			url.searchParams.set( args.name, val );
		} else {
			url.searchParams.delete( args.name );
		}

		setData( null );
		setURL( url );
		setLoading( true );
	};

	const onChangeUsernameColor = ( color ) => {
		setAttributes( { usernameColor: color } );
	};
	const onChangeTopicColor = ( color ) => {
		setAttributes( { topicColor: color } );
	};
	const onChangeBgColor = ( color ) => {
		setAttributes( { bgColor: color } );
	};
	const onChangeLabelBgColor = ( color ) => {
		setAttributes( { labelBgColor: color } );
	};
	const onChangeLiveColor = ( color ) => {
		setAttributes( { liveColor: color } );
	};
	const onChangeTextShadowColor = ( color ) => {
		setAttributes( { textShadowColor: color } );
		if(typeof color !== 'undefined'){
			setAttributes( { textShadowValue: [ textShadow, color ].join(' ') } );
		} else {
			setAttributes( { textShadowValue: null } );
		}
	};

	const onChangeLang = ( lang ) => {
		setAttributes( { lang: lang } );
		changeURL( { name: 'lang', val: lang, multiple: false } );
	};
	const onChangeGender = ( gender ) => {
		setAttributes( { genre: gender } );
		changeURL( { name: 'genre', val: gender, multiple: true } );
	};
	const onChangeAge = ( age ) => {
		setAttributes( { age: age } );
		changeURL( { name: 'age', val: age, multiple: true } );
	};
	const onChangeLink = ( link ) => {
		setAttributes( { link: link } );
	};
	const onChangeTarget = ( target ) => {
		setAttributes( { targetNew: target } );
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
	const onChangeOrder = ( order ) => {
		setAttributes( { order: order } );
		changeURL( { name: 'order', val: order, multiple: false } );
	};

	// allow only Latin alphabets
	const validateTag = ( tag ) => {
		return /^[aA-zZ]+$/.test(tag)
	};

	const onChangeDisplayLive = ( val ) => {
		setAttributes( { displayLive: ! displayLive } );
	};
	const onChangeDisplayTopic = ( val ) => {
		setAttributes( { displayTopic: ! displayTopic } );
	};
	const onChangeDisplayGenre = ( val ) => {
		setAttributes( { displayGenre: ! displayGenre } );
	};
	const onChangeDisplayUsers = ( val ) => {
		setAttributes( { displayUsers: ! displayUsers } );
	};
	const onChangeGap = ( val ) => {
		setAttributes( { columnGap: val } );
	};
	const onChangeImageHeight = ( val ) => {
		setAttributes( { imageHeight: val } );
	};
	const onChangeImageWidth = ( val ) => {
		setAttributes( { imageWidth: val } );
	};
	const onChangeAutoRefresh = ( val ) => {
		setAttributes( { autoRefresh: val } );
	};
	const onChangeCount = ( val ) => {
		setAttributes( { count: val } );
	};

	const siteLang = useSelect( ( select ) => {
		let lang = select( 'core' ).getSite()?.language;
		return lang && lang.split( '_' )[ 0 ];
	} );

	useEffect( () => {
		const options = {
			method: 'GET',
		};

		if ( ! loading ) return;

		fetch( url, options )
			.then( ( response ) => response.json() )
			.then( ( response ) => {
				let newData = { ...response };
				setLoading( false );
				setData( response.body );
			} )
			.catch( ( err ) => console.error( err ) );
	}, [ loading, url ] );

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
						label={ __( 'Number of cams', 'amateur-tv' ) }
						value={ count }
						initialPosition={ !! data ? data.length : 0 }
						onChange={ onChangeCount }
						min={ 0 }
						max={ !! data ? data.length : 0 }
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
								value: 'cn',
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

					<SelectControl
						label={ __( 'Order', 'amateur-tv' ) }
						value={ order }
						options={ [
							{
								label: __( 'Featured', 'amateur-tv' ),
								value: 'highlighted',
							},
							{
								label: __( 'Viewers', 'amateur-tv' ),
								value: 'realviewers',
							},
							{
								label: __( 'New', 'amateur-tv' ),
								value: 'new',
							},
						] }
						onChange={ onChangeOrder }
					/>

					<ToggleControl
						label={ __( 'Show Live Label', 'amateur-tv' ) }
						checked={ !! displayLive }
						onChange={ onChangeDisplayLive }
					/>
					<ToggleControl
						label={ __( 'Show Gender', 'amateur-tv' ) }
						checked={ !! displayGenre }
						onChange={ onChangeDisplayGenre }
					/>
					<ToggleControl
						label={ __( 'Show Users', 'amateur-tv' ) }
						checked={ !! displayUsers }
						onChange={ onChangeDisplayUsers }
					/>
					<ToggleControl
						label={ __( 'Show Topic', 'amateur-tv' ) }
						checked={ !! displayTopic }
						onChange={ onChangeDisplayTopic }
					/>
					<Flex>
						<FlexBlock>
							<FlexItem>
								<TextControl
									label={ __( 'Link', 'amateur-tv' ) }
									value={ link }
									onChange={ onChangeLink }
									help={ __(
										'Absolute or relative URL. Leave blank to use the link of the cam. Placeholders supported: {camname}, {affiliate}',
										'amateur-tv'
									) }
								/>
							</FlexItem>
							<FlexItem>
								<ToggleControl
									label={ __(
										'Open in new tab',
										'amateur-tv'
									) }
									checked={ !! targetNew }
									onChange={ onChangeTarget }
								/>
							</FlexItem>
						</FlexBlock>
					</Flex>

					<PanelColorSettings
						title={ __( 'Color Settings', 'amateur-tv' ) }
						initialOpen={ false }
						colorSettings={ [
							{
								value: usernameColor,
								onChange: onChangeUsernameColor,
								label: __( 'Username/Gender', 'amateur-tv' ),
								enableAlpha: true
							},
							{
								value: liveColor,
								onChange: onChangeLiveColor,
								label: __( 'Live Label', 'amateur-tv' ),
								enableAlpha: true
							},
							{
								value: topicColor,
								onChange: onChangeTopicColor,
								label: __( 'Topic', 'amateur-tv' ),
								enableAlpha: true
							},
							{
								value: bgColor,
								onChange: onChangeBgColor,
								label: __( 'Background', 'amateur-tv' ),
								enableAlpha: true
							},
							{
								value: labelBgColor,
								onChange: onChangeLabelBgColor,
								label: __( 'Username/Topic Background', 'amateur-tv' ),
								colors: [
									{ color: '#00000021' },
									{ color: '#0000FF21' },
									{ color: '#FF000021' },
									{ color: '#00FF0021' },
								],
								enableAlpha: true
							},
							{
								value: textShadowColor,
								onChange: onChangeTextShadowColor,
								label: __( 'Text Shadow', 'amateur-tv' ),
								enableAlpha: false
							},
						] }
					/>
					<RangeControl
						label={ __( 'Column Gap', 'amateur-tv' ) }
						value={ columnGap }
						initialPosition={ 3 }
						onChange={ onChangeGap }
						min={ 0 }
						max={ 10 }
					/>
					<RangeControl
						label={ __( 'Image Height', 'amateur-tv' ) }
						value={ imageHeight }
						initialPosition={ 115 }
						onChange={ onChangeImageHeight }
						min={ 115 }
						max={ 500 }
					/>
					<RangeControl
						label={ __( 'Image Width', 'amateur-tv' ) }
						value={ imageWidth }
						initialPosition={ 216 }
						onChange={ onChangeImageWidth }
						min={ 216 }
						max={ 500 }
					/>
					<RangeControl
						label={ __( 'Auto Refresh (minutes)', 'amateur-tv' ) }
						value={ autoRefresh }
						initialPosition={ 0 }
						onChange={ onChangeAutoRefresh }
						min={ 0 }
						max={ 10 }
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
				<div
					className="atv-cams-list"
					style={ { backgroundColor: bgColor, gap: columnGap } }
				>
					{ !! data &&
						data
							.slice( 0, count > 0 ? count : data.length )
							.map( ( item, i ) => {
								return (
									<a
										key={ i }
										target="_blank"
										className="atv-cam"
									>
										<img
											src={ item.image }
											width={ imageWidth }
											height={ imageHeight }
											style={ { maxHeight: imageHeight } }
										/>
										<div className="atv-annotations">
										{ !! displayLive && (
											<span
												className="atv-live atv-padding"
												style={ {
													color: liveColor,
													textShadow: textShadowValue,
												} }
											>
												{ __( 'Live', 'amateur-tv' ) }
											</span>
										) }
										{ !! displayGenre && (
											<span
												className="atv-genre atv-padding"
												style={ {
													color: usernameColor,
													textShadow: textShadowValue,
												} }
											>
												{ __(
													item.genre,
													'amateur-tv'
												) }
											</span>
										) }
										{ !! displayUsers && (
											<span
												className="atv-viewers atv-padding"
												style={ {
													color: liveColor,
													textShadow: textShadowValue,
												} }
											>
												<span className="dashicons dashicons-visibility"></span>
												<span>{ item.viewers }</span>
											</span>
										) }
										<span
											className="atv-username atv-padding atv-rounded"
											style={ {
												color: usernameColor,
												backgroundColor: labelBgColor,
												textShadow: textShadowValue,
											} }
										>
											{ item.username }
										</span>
										{ !! displayTopic && (
											<div
												className="atv-topic atv-padding atv-rounded"
												style={ {
													color: topicColor,
													backgroundColor:
														labelBgColor,
													textShadow: textShadowValue,
												} }
											>
												{
													item.topic[
														!! lang ? lang : 'en'
													]
												}
											</div>
										) }
										</div>
									</a>
								);
							} ) }
				</div>
			</div>
		</>
	);
}
