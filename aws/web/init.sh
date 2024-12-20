#!/bin/bash

echo "Esperando a LocalStack..."
until curl -s http://localstack:4566/_localstack/health | grep '"lambda": "available"' > /dev/null; do
  sleep 2
done

echo "LocalStack está listo."

aws configure set aws_access_key_id test
aws configure set aws_secret_access_key test
aws configure set region us-east-1

cd function_source
zip -r ../function.zip .
cd ..

aws lambda create-function \
  --function-name horarios \
  --runtime nodejs18.x \
  --role arn:aws:iam::123456789012:role/execution_role \
  --handler index.handler \
  --zip-file fileb://function.zip \
  --endpoint-url http://localstack:4566

echo "Función lambda horarios creada."
