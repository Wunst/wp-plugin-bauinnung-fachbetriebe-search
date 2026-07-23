import { registerBlockType } from '@wordpress/blocks'

import { useBlockProps } from '@wordpress/block-editor'

import './style.css'
import metadata from './block.json'

registerBlockType( metadata.name, {
  edit: () => <p { ...useBlockProps() }>
    Fachbetriebesuche
  </p>,

  save: () => <div { ...useBlockProps.save() } />,
} )
