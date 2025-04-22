const express     = require('express');
const bodyParser  = require('body-parser');
const { v4: uuid} = require('uuid');
const neo4j       = require('neo4j-driver');
const path = require('path');

const app     = express();
const driver  = neo4j.driver('bolt://localhost:7687');
const session = driver.session();

app.use(bodyParser.json());

app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});
app.get('/style.css', (req, res) => {
  res.sendFile(path.join(__dirname, 'style.css'));
});

/* ---------- 1. Listado / Búsqueda) ----------------------- */
app.get('/api/events', async (req,res) => {
  const { search } = req.query;
  let cypher;

  if (search) {
    cypher = `MATCH (e:Event) WHERE e.title CONTAINS '${search}' RETURN e`;
  } else {
    cypher = 'MATCH (e:Event) RETURN e';
  }

  try {
    const result = await session.run(cypher);
    const events = result.records.map(r => ({ id: r.get('e').properties.id,
                                              ...r.get('e').properties }));
    res.json(events);
  } catch (err) {
    res.status(500).send(err.message);
  }
});

/* ---------- 2. Creación -------------------------------------------------- */
app.post('/api/events', async (req,res) => {
  const { title, date, startTime, endTime, description, category, color } = req.body;
  const id = uuid();
  await session.run(
    `CREATE (e:Event {id:$id, title:$title, date:$date,
                      start:$startTime, end:$endTime,
                      description:$description, category:$category, color:$color})`,
    { id, title, date, startTime, endTime, description, category, color }
  );
  res.status(201).json({ id });
});

/* ---------- 3. Actualización ------------------------------------------- */
app.put('/api/events/:id', async (req,res) => {
  const { id } = req.params;
  const props  = req.body;
  const setStr = Object.keys(props).map(k => `e.${k} = $${k}`).join(', ');
  await session.run(`MATCH (e:Event {id:$id}) SET ${setStr}`, { id, ...props });
  res.sendStatus(204);
});

/* ---------- 4. Borrado -------------------------------------------------- */
app.delete('/api/events/:id', async (req,res) => {
  await session.run('MATCH (e:Event {id:$id}) DETACH DELETE e', { id:req.params.id });
  res.sendStatus(204);
});

/* ---------- 5. Inicio --------------------------------------------------- */
app.listen(80, () => console.log('📅  API lista en http://localhost:80'));