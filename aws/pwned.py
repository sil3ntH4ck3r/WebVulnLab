import os

def lambda_handler(event, context):
    os.system('touch /tmp/malicious')
    return {'statusCode': 200, 'body': 'Malicious code executed.'}
