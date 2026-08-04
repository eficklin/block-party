import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RadioControl, TextControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { dateI18n, getSettings } from '@wordpress/date';
import { __ } from '@wordpress/i18n';

const Edit = ({ attributes, setAttributes }) => {
  let { dateFormat, prependText, displayModified } = attributes;

  /* Get site's date settings. */
  const {formats} = getSettings();
  if (!dateFormat) {
    dateFormat = formats.date;
  }

  /* Get post dates */
  const postDate = useSelect((select) => select('core/editor').getEditedPostAttribute('date'), []);
  const modifiedDate = useSelect((select) => select('core/editor').getEditedPostAttribute('modified'), []);

  /* Prepare display options. Start with the site setting. */
  const displayOptions = [
    {label: `${dateI18n(formats.date, postDate)} (default)`, value: formats.date},
  ];
  /* 
    Hard-coded options for the sake of example; real world usage might be
    to pull from site/network options instead.
  */
  ['F Y', 'Y-m-d', 'm/d/Y', 'd/m/Y'].forEach((val) => {
    displayOptions.push({label: dateI18n(val, postDate), value: val});
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title={ __('Formatting Options', 'publication-date') }>
          <RadioControl
            label={ __('Select a date format', 'ef-publication-date') }
            selected={ dateFormat }
            options={ displayOptions }
            onChange={ (newValue) => setAttributes({ dateFormat: newValue }) }
          />
          <TextControl
            label={ __('Text to prepend to date', 'ef-publication-date') }
            value={ prependText }
            onChange={ (newValue) => setAttributes({ prependText: newValue }) }
          />
          <ToggleControl
            label={ __('Show modified date', 'ef-publication-date') }
            checked={ displayModified }
            onChange={ () => setAttributes({ displayModified: !displayModified }) }
          />
        </PanelBody>
      </InspectorControls>
      <div { ...useBlockProps() }>
        <div className="publication-date-post-date">
          { prependText && <span className="publication-date-prepend">{ prependText }</span> }
          { prependText && '\u00a0' }
          <time dateTime={postDate}>{ dateI18n(dateFormat, postDate) }</time>
        </div>
        { displayModified && (
          <div className="publication-date-modified-date">
            <span className="publication-date-prepend">{ __('Updated on', 'ef-publication-date') }</span>
            &nbsp;
            <time dateTime={modifiedDate}>{ dateI18n(dateFormat, modifiedDate) }</time>
          </div>
        ) }
      </div>
    </>
  );
};

export default Edit;
