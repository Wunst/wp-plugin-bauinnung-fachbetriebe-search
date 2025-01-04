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
  Autocomplete,
  Stack,
  MenuItem,
  Chip,
  FormControl,
  InputLabel
} from "@mui/material";

const categories = await (
  await fetch("/index.php/wp-json/fachbetrieb/v1/categories")
).json()

const dummyResults = [
  {
    name: "Musterbau Mustermann",
    adresse: "Musterstr. 123, 24567 Kiel",
    url: "musterbau.de",
    icon: "https://static.vecteezy.com/system/resources/previews/010/885/577/original/tools-icon-equipment-symbol-repair-construction-illustration-work-tools-instrument-service-sign-engineer-hardware-mechanic-industrial-kit-diy-group-support-carpentry-hand-repair-icon-vector.jpg"
  },
  {
    name: "Baugeschäft Bruno Andschatzen KG",
    adresse: "Spitzenkamp 1f, 21337 Kiel",
    url: "brandschatzen.de",
    icon: "https://www.pngall.com/wp-content/uploads/4/Viking-Vector-PNG-Image.png"
  },
  {
    name: "Fliesenleger Example & Co.",
    adresse: "Winterbeker Weg 12, 24114 Kiel",
    url: "example.com",
    icon: "https://www.schmitt-fliesen.de/wp-content/uploads/2023/01/Fliesen-Schmitt_Icon_350px.png"
  }
]

function SearchApp( props ) {
  return <Grid container spacing={3}>
    <SearchForm/>
    <SearchResults/>
  </Grid>
}

function SearchForm( props ) {
  return <Grid item xs={12} md={4}>
    <Box class="fachbetrieb-search-form" component="form" onSubmit={handleSubmit} sx={{ mt: 4 }}>
      <h2>Suche</h2>
      <h3>Fachgebiet</h3>
        <Autocomplete
          id="category"
          multiple
          options={categories.map(c => c.id)}
          getOptionLabel={id => categories.find(c => c.id == id).name}
          defaultValue={[]}
          renderInput={(params) => (
            <TextField
              {...params}
              label="Kategorien"
            />
          )}
        />
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
  return <Grid item xs={12} md={8}>
    {dummyResults.map(result => <div class="fachbetrieb-search-result">
      <Grid container spacing={4}>
        <Grid item
          xs={4}
          sm={2}
          component="img"
          src={result.icon}
          alt={`Logo von ${result.name}`}
          sx={{
            aspectRatio: 1.0,
            objectFit: "cover",
          }}
        />
        <Grid item xs={8} sm={5}>
          <Stack direction="column">
            <h3>{result.name}</h3>
            <p>{result.adresse}</p>
            <a href={result.url}>🌐 {result.url}</a>
          </Stack>
        </Grid>
        <Grid item xs={6} sm={5}>
          <h4>Fachbetrieb für</h4>
          <Chip
            label="Hochbau"
            style={{
              display: "inline"
            }}
          />
          <Chip
            label="Tiefbau"
            style={{
              display: "inline"
            }}
          />
          <Chip
            label="Abriss"
            style={{
              display: "inline"
            }}
          />
          <Chip
            label="blablabla"
            style={{
              display: "inline"
            }}
          />
        </Grid>
      </Grid>
    </div>)}
  </Grid>
}

function handleSubmit() {
}

render(
  <SearchApp/>,
  document.getElementById( 'fachbetriebe-suche' )
)

