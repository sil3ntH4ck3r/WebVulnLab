FROM postgres:latest

RUN apt-get update && apt-get install -y \
    postgresql-client-13 \
    postgresql-contrib-13 \
    python3.9 \
    python3-psycopg2 \
    python3-flask \
    python3-flask-sqlalchemy \
    python3-pip \
    python3-jwt \
    python3-flask-login \
    python3-requests-oauthlib \
    python3-reportlab \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

ADD . /app

ENV FLASK_ENV development

ENV DATABASE_URI=postgresql://username:password@gallery_db:5432/gallery_db

EXPOSE 5001

CMD ["python3", "app.py"]