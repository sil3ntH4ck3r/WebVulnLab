FROM python:3.9

WORKDIR /var/www/html

COPY requirements.txt .
RUN apt update
RUN apt install libyaml-dev
RUN pip install --no-cache-dir -r requirements.txt

COPY src/ ./src

EXPOSE 5000

CMD ["python", "app.py"]