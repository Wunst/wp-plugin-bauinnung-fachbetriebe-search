/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

import { render } from '@wordpress/element'

import {
  Box, 
  TextField, 
  Button, 
  Grid,
  Select,
  MenuItem,
  Chip,
  FormControl,
  InputLabel
} from "@mui/material";

const categories = [
  { id: 1, name: "Testkategorie 1" },
  { id: 2, name: "Testkategorie 2" },
  { id: 3, name: "Testkategorie 3" },
]

function SearchApp( props ) {
  return <Grid container>
    <SearchForm/>
    <SearchResults/>
  </Grid>
}

function SearchForm( props ) {
  return <Grid item xs={12} md={4}>
    <Box class="fachbetrieb-search-form" component="form" onSubmit={handleSubmit} sx={{ mt: 4 }}>
      <h2>Suche</h2>
      <h3>Fachgebiet</h3>
      <FormControl fullWidth>
        <InputLabel id="category-select-small-label">Kategorie</InputLabel>
        <Select
          id="category"
          name="category"
          label="Kategorie"
          multiple
          defaultValue={[]}
          renderValue={(selected) => (
            <Box sx={{ display: 'flex', gap: '0.25rem' }}>
              {selected.map((id) => (
                <Chip sx={{ fontSize: '1rem' }}
                  key={id}
                  label={categories.find(o => o.id == id).name}/>
              ))}
            </Box>
          )}
          sx={{ height: "7rem" }}
          fullWidth
        >
          {categories.map(o =>
            <MenuItem value={o.id} label={o.name}>{o.name}</MenuItem>
          )}
        </Select>
      </FormControl>
      <h3>Ihre Baustelle</h3>
      <Grid container spacing={2}>
        <Grid item xs={9}>
          <TextField
            id="street"
            name="street"
            label="Straße"
            fullWidth
          />
        </Grid>
        <Grid item xs={3}>
          <TextField
            id="number"
            name="number"
            label="Nr."
            fullWidth
          />
        </Grid>
        <Grid item xs={4}>
          <TextField
            required
            id="plz"
            name="plz"
            label="PLZ"
            fullWidth
          />
        </Grid>
        <Grid item xs={8}>
          <TextField
            required
            id="city"
            name="city"
            label="Ort"
            fullWidth
          />
        </Grid>
      </Grid>
      <h3>Suche im Umkreis</h3>
      Suche im Umkreis von
      <Box sx={{ display: "inline-flex", padding: "1em", width: "120px" }}>
        <TextField
          id="distance"
          name="distance"
          label="Entfernung"
        />
      </Box>
      km
    </Box>
  </Grid>
}

function SearchResults( props ) {
  return <div class="fachbetrieb-search-results">
  </div>
}

function handleSubmit() {
}

render(
  <SearchApp/>,
  document.getElementById( 'fachbetriebe-suche' )
)

