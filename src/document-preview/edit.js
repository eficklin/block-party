import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, Panel, PanelBody } from '@wordpress/components';
import { store } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { filesize } from "filesize";

import mimes from './util/mimes';

/**
 *
 * @return {Element} Element to render.
 */
export default function edit({ attributes, setAttributes }) {
	const { documentTitle, description, mediaId } = attributes;
	
	const buttonLabel = mediaId > 0
		? __( 'Change Document', 'document-preview' ) 
		: __( 'Select Document', 'document-preview' );

	const { media } = useSelect(
		( select ) => ( {
			media:
				mediaId === undefined
					? undefined
					: select( store ).getMedia( mediaId ),
		} ),
		[ mediaId ]
	);

	let preview = '';
	let fileMeta = '';
	let download = '';
	if (media) {
		preview = media.mime_type === 'application/pdf'
			? 
				<object
					className="document-preview__embed--pdf"
					data={media.source_url} 
					type="application/pdf"
				>
				</object>
			: 
				<iframe
					className="document-preview__embed--mso"
					src={`https://view.officeapps.live.com/op/embed.aspx?src=${media.source_url}`}
				>
				</iframe>;
		fileMeta = 
			<div 
				className="document-preview__meta"
			>
				{ __( 'File:', 'document-preview' )} {media.title.rendered} ({filesize(media.media_details.filesize)})
			</div>;
		download = 
			<div className="document-preview__download">
				<a
					href={media.source_url}
					className="wp-element-button"
					download
				>
					{ __( 'Download', 'document-preview' ) }
				</a>
			</div>;
	}

	return (
		<>
			<div { ...useBlockProps() }>
				<RichText
					tagName="h2"
					identifier="documentTitle"
					value={documentTitle}
					onChange={(val) => setAttributes({ documentTitle: val })}
					placeholder={__('Document Title', 'document-preview')}
				/>
				<RichText
					tagName="p"
					identifier="description"
					value={description}
					onChange={(val) => setAttributes({ description: val })}
					placeholder={__('A description of the document', 'document-preview')}
				/>
				{preview}
				{fileMeta}
				{download}
			</div>
			<InspectorControls>
				<Panel>
					<PanelBody>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ mediaId : media.id })}
								value={mediaId}
								allowedTypes={mimes}
								render={({ open }) => <Button onClick={open} variant="primary">{ buttonLabel }</Button>}
							/>
						</MediaUploadCheck>
						{media && (
							<>
								<div>{__('Selected file', 'document-preview')}: <strong>{media.title.rendered}</strong></div>
								<div>{__('Type', 'document-preview')}: {media.mime_type}</div>
								<div>{__('Size', 'document-preview')}: {filesize(media.media_details.filesize)}</div>
							</>
						)}
					</PanelBody>
				</Panel>
			</InspectorControls>
		</>
	);
}
