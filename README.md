# tanebox
My pet PJT.
Web service on WordPress with KUSANAGI VM.

## Setup

### /home/kusanagi/
- Download /home/kusanagi/
- make SSH (~/.ssh/)
- make .htpasswd for Basic Auth
- make Let's Encrypt cert

### /home/kusanagi/[Web Service]/
- make wp-config.php

## Test
/home/256hax/selenium_ide/

## Maintenance

### Control Source Code
1. Change source code in Live Server (It means deploy directly).
2. Run Test Tool (Selenium IDE).
3. Commit and Push to GitHub change source code.

### Update Server Information
Execute export-server_info.sh.
`$ /home/256hax/server_info/export-server_info.sh`

Update each list. See same directory files.