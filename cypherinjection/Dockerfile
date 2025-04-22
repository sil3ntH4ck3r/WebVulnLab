FROM neo4j:5.15-community

# ───────── Neo4j ───────────────────────────────────────────────────────────
ENV NEO4J_AUTH=none
ENV NEO4JLABS_PLUGINS='["apoc"]'
ENV NEO4J_dbms_security_procedures_unrestricted=apoc.*

COPY neo4j.conf /var/lib/neo4j/conf/neo4j.conf

# ───────── Node.js 18 LTS ──────────────────────────────────────────────────
RUN apt-get update -qq && \
    apt-get install -y curl gnupg netcat && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

RUN echo 'precedence ::ffff:0:0/96  100' >> /etc/gai.conf
RUN npm config set registry http://registry.npmjs.org/

# ───────── App ─────────────────────────────────────────────────────────────
WORKDIR /app
COPY src/ .
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 7474 7687 3000
ENTRYPOINT ["/start.sh"]