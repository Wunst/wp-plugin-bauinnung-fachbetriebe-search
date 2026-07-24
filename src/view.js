import { Alert, Autocomplete, Box, Button, Grid, TextField } from "@mui/material"
import { createRoot, useEffect, useState } from "@wordpress/element"
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
      'latitude',
      'longitude',
      'distance'
    ]
})

/**
 * Computes distance between coordinates.
 */
function haversine(latitude1, longitude1, latitude2, longitude2) {
  // Convert to radians.
  const lat1 = Math.PI * latitude1 / 180
  const lon1 = Math.PI * longitude1 / 180

  const lat2 = Math.PI * latitude2 / 180;
  const lon2 = Math.PI * longitude2 / 180


  const earthRadius = 6371 // km

  return earthRadius * 2 * Math.asin(
    Math.sqrt(
      Math.pow(Math.sin((lat1 - lat2) / 2), 2) +
      Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin((lon1 - lon2) / 2), 2)
    )
  )
}

function SearchForm(props) {
  const [query, setQuery] = useState({
    categories: [],
    address: "", 
    max_distance: 0,

  })

  const [userCoordinates, setUserCoordinates] = useState(null);

  // On query update, filter and sort list 
  // accordingly.
  useEffect(() => {
    (async () => {
      const newCoordinates = ( query.address && await (
        await fetch(rest_url + 'fachbetrieb/v2/coordinates&' + new URLSearchParams({
            address: query.address
          }))
        ).json())
      // Wordpress REST API cannot return null, only an empty array
      if (newCoordinates.length == 0)
        setUserCoordinates(null)
      else
        setUserCoordinates(newCoordinates)
    })()
    return () => {}
  }, [query])

  list.filter(item => {
    for (const category of query.categories) {
      if (!item.values().categories.includes('<li>' + category + '</li>')) 
        return false
    }

    return true
  })

  if (userCoordinates) {
    list.items.forEach(item => {
      item.values({
        distance: Math.round(
          haversine(userCoordinates.lat, userCoordinates.lon, item.values().latitude, item.values().longitude)
          * 10
        ) / 10,
      })
    })

    list.sort("distance")
  } else {
    list.items.forEach(item => {
      item.values({
        distance: ""
      })
    })

    list.sort("name")
  }

  return <Box component="form" sx={{ mt: 4 }}>
      <h2>Suche</h2>
      <h3>Fachgebiet</h3>
        <Autocomplete
          id="category"
          multiple
          options={categories}
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
              let address = [
                document.getElementById("number").value,
                document.getElementById("street").value,
                document.getElementById("city").value,
                document.getElementById("plz").value
              ].join(",")
              if (address == ",,,") {
                address = "";
              }
              setQuery({ ...query, address })
            }}
          >
            Aktualisieren
          </Button>
        </Grid>
        <Grid item xs={12}>
          {query.address && !userCoordinates && <Alert severity="warning">
            Ihre Adresse konnte nicht zugeordnet werden. 
            Die Ergebnisse sind daher nicht sortiert.
            Sind Sie sicher, dass Sie die Adresse richtig geschrieben haben?
          </Alert>}
        </Grid>
      </Grid>
    </Box>
}

createRoot(
  document.getElementById("fachbetrieb-searchform")
).render(<SearchForm/>)
