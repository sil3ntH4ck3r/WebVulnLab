#!/usr/bin/env bash
set -e

neo4j console --verbose &

# Esperamos a Bolt
until nc -z localhost 7687; do
  echo "⏳  Neo4j aún no está listo…"
  sleep 2
done

exec node server.js
