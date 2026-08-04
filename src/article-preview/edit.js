import apiFetch from '@wordpress/api-fetch';
import { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } from '@wordpress/block-editor';
import { Button, Panel, PanelBody, PanelRow, SelectControl, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { addQueryArgs, isURL } from '@wordpress/url';

/**
 * Edit component for the Article Preview block.
 *
 * Renders a preview of an article by fetching Open Graph metadata from a given URL.
 * Displays the article's title, description, and image in either a list or card layout.
 * Provides inspector controls for configuring the article URL and layout style.
 *
 * @param {Object}   props               - Component props.
 * @param {Object}   props.attributes    - Block attributes.
 * @param {string}   props.attributes.url    - The URL of the article to preview.
 * @param {string}   props.attributes.layout - The display layout ('list' or 'card').
 * @param {Function} props.setAttributes - Function to update block attributes.
 * @return {Element} The block editor element.
 */
export default function Edit({ attributes, setAttributes }) {
	const { 
		url, 
		layout, 
		titleOverride, 
		descriptionOverride,
		imageURLOverride,
		imageAltOverride, 
	} = attributes;

	const [ title, setTitle ] = useState('');
	const [ description, setDescription ] = useState('');
	const [ imageURL, setImageURL ] = useState('');
	const [ imageAlt, setImageAlt ] = useState('');
	const [ error, setError ] = useState('');

	useEffect(() => {
		if (isURL(url)) {
			const queryParam = { url }
			apiFetch({ path: addQueryArgs('/article-preview/v1/open-graph', queryParam) })
				.then((data) => {
					if (data.title) {
						setTitle(data.title);
					}
					if (data.image) {
						setImageURL(data.image);
					}
					if (data['image:alt']) {
						setImageAlt(data['image:alt']);
					}
					if (data.description) {
						setDescription(data.description);
					}
				})
				.catch(() => setError('Could not fetch open graph data for that URL.'));
		}
	}, [url]);

	return (
		<>
			<InspectorControls>
				<Panel header={ __( 'Settings', 'tesla-takedown' ) }>
					<PanelBody>
						<PanelRow>
							<SelectControl
								label={ __( 'Layout', 'tesla-takedown' ) }
								value={ layout }
								options={[
									{ label: __( 'List', 'tesla-takedown' ), value: 'list' },
									{ label: __( 'Card', 'tesla-takedown' ), value: 'card' }
								]}
								onChange={ (value) => setAttributes( { layout: value } ) }
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={ __( 'Article URL', 'tesla-takedown' ) }
								value={ url }
								onChange={ ( value ) => setAttributes( { url: value } ) }
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={ __( 'Manual Title', 'tesla-takedown' ) }
								value={ titleOverride }
								onChange={(value) => setAttributes({ titleOverride: value })}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={ __( 'Manual Description', 'tesla-takedown' ) }
								value={ descriptionOverride }
								onChange={(value) => setAttributes({ descriptionOverride: value })}
							/>
						</PanelRow>
						<PanelRow>
							<p>{ __( 'Select or upload a different image from the media library.', 'tesla-takedown' ) }</p>
						</PanelRow>
						<PanelRow>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ (media) => {  
										console.log(media);
										setAttributes({ 
											imageURLOverride: media.sizes.full.url,
											imageAltOverride: media?.alt,
										});
									} }
									allowedTypes={ [ 'image' ] }
									render={ ( { open } ) => (
										<Button 
											onClick={ open }
											variant="secondary"
										>
											{ __( 'Open Media Library', 'tesla-takedown' ) }
										</Button> 
									)}
								/>
							</MediaUploadCheck>
						</PanelRow>
						<PanelRow>
							<Button
								onClick={() => setAttributes({imageURLOverride: ''})}
								variant="secondary"
							>
								{ __( 'Clear Selection', 'tesla-takedown' ) }
							</Button>
						</PanelRow>
						{error && <PanelRow>{ error }</PanelRow>}
					</PanelBody>
				</Panel>
			</InspectorControls>
			<div { ...useBlockProps({
				className: `has-layout--${layout}`
			}) }>
				<div className="wrapper">
					{error ? (
						<p>{error}</p>
					) : (
						<>
							<div className="image">
								{ ( imageURL || imageURLOverride ) && 
									<img
										src={ imageURLOverride || imageURL }
										alt={ imageAltOverride || imageAlt }
									/> 
								}
							</div>
							<div className="text">
								<div className="title">{ titleOverride || title }</div>
								<div className="description">{ descriptionOverride || description }</div>
							</div>
						</>
					)}
				</div>
			</div>
		</>
	);
}
