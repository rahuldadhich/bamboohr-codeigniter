# BambooHR Codeigniter integration 
BambooHR integration with Codeigniter [BambooHR-API](https://www.bamboohr.com/api/documentation/) 

This code is usefull if you want to download BambooHR employees, department, location/site on your CRM.

## Request Rule:
1. All requests made to BambooHR APIs must be sent over HTTPS.
2. API requests are made to a URL that begins with: https://api.bamboohr.com/api/gateway.php/{company subdomain name}/
3. All requests should be in UTF-8.

## Steps
1. Login into your BambooHR account and get APP_KEY and Company-ID.
2. Set username, password, company-id and app-key in model.
3. Enter your endpoint URL in the field provided. This URL must be exposed over the internet and be secured via HTTPS.
4. Select desired events for receive webhooks data.
5. Click Save. It may take up to five minutes for your endpoint to receive its first notification.

BambooHR Employees [API](https://www.bamboohr.com/api/documentation/employees.php)
BambooHR MetaData [API](https://www.bamboohr.com/api/documentation/metadata.php)

If it doesn't work for you, feel free to contact me at rahuldadhich87@gmail.com