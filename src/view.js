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

import {
  render,
  useEffect,
  useState
} from '@wordpress/element'

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

import placeholderLogo from "./placeholder-logo.png"

const categories = await (
  await fetch("/index.php/wp-json/fachbetrieb/v1/categories")
).json()

function SearchApp( props ) {
  const [query, setQuery] = useState({})

  return <Grid container spacing={3}>
    <SearchForm query={query} setQuery={setQuery}/>
    <SearchResults query={query}/>
  </Grid>
}

function SearchForm({ query, setQuery }) {
  return <Grid item 
    xs={12} 
    md={5}
    lg={4}
  >
    <Box class="fachbetrieb-search-form" component="form" sx={{ mt: 4 }}>
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
          onChange={(event, newValue) => {
            setQuery({ ...query, c: newValue.join(",") })
          }}
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
      <Box sx={{ display: "inline-flex", padding: "1em", width: "130px" }}>
        <TextField
          id="distance"
          name="distance"
          label="Entfernung"
          inputProps={{
            type: "number"
          }}
          onChange={(event) => {
            setQuery({ ...query, d: event.target.value })
          }}
        />
      </Box>
      km
    </Box>
  </Grid>
}

function SearchResults({ query }) {
  const [search, setSearch] = useState({
    results: []
  })

  useEffect(() => {
    (async () =>
      setSearch(await (
        await fetch("/index.php/wp-json/fachbetrieb/v1/search?" +
          new URLSearchParams(query)
        )
      ).json())
    )();
    return () => {}
  }, [query])

  return <Grid item
    xs={12} 
    md={7}
    lg={8}
  >
    {
      // TODO: warn if address invalid
    }
    {search.results.map(result => <div class="fachbetrieb-search-result">
      <Grid container spacing={4}>
        <Grid item
          xs={5}
          sm={3}
          md={4}
          xl={2}
          component="img"
          src={result.logo ||
            placeholderLogo}
          alt={`Logo von ${result.name}`}
          sx={{
            aspectRatio: 1.0,
            objectFit: "cover",
          }}
        />
        <Grid item
          xs={7}
          sm={8}
          xl={4}
        >
          <Stack direction="column">
            <h3>{result.name}</h3>
            <p>{result.adresse}</p>
            {result.url &&
              <a href={result.url}>{result.url}</a>
            }
          </Stack>
        </Grid>
        <Grid item xs={6}>
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

render(
  <SearchApp/>,
  document.getElementById( 'fachbetriebe-suche' )
)

