import { Autocomplete, Box, Button, Grid, TextField } from "@mui/material"
import { createRoot, useState } from "@wordpress/element"
import List from 'list.js'


const rest_url = '/index.php?rest_route=/'

const categories = await (
  await fetch(rest_url + 'fachbetrieb/v2/categories')
).json()

const list = new List('fachbetrieb', {
    valueNames: [
      'name',
      'address',
      'email',
      'phone',
      'categories',
    ]
})

function SearchForm(props) {
  const [query, setQuery] = useState({
    categories: [],
    address: "", 
    max_distance: 0,

  })

  // On query update, filter and sort list 
  // accordingly.
  // TODO

  return <Box component="form" sx={{ mt: 4 }}>
      <h2>Suche</h2>
      <h3>Fachgebiet</h3>
        <Autocomplete
          id="category"
          multiple
          options={categories}
          getOptionLabel={category => category}
          defaultValue={[]}
          renderInput={(params) => (
            <TextField
              {...params}
              label="Kategorien"
            />
          )}
          onChange={(event, selected_categories) => {
            setQuery({ ...query, categories: selected_categories })
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
        <Grid item xs={2}>
          <Button
            onClick={() => {
              setQuery({ ...query, address: [
                // TODO: make this not suck
                // FIXME: when empty, reset to "" instead of ",,,"
                document.getElementById("number").value,
                document.getElementById("street").value,
                document.getElementById("city").value,
                document.getElementById("plz").value
              ].join(",")
            })}}
          >
            Aktualisieren
          </Button>
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
            setQuery({ ...query, max_distance: event.target.value })
          }}
        />
      </Box>
      km
    </Box>
}

createRoot(
  document.getElementById("fachbetrieb-searchform")
).render(<SearchForm/>)
