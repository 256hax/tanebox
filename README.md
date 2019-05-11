# tanebox
My pet PJT.
Web service on WordPress with KUSANAGI VM.

## Technologies
### Frontend Layer
- WordPress Theme
- Google Japanese Fonts [(font-family: 'Noto Sans JP')](https://fonts.google.com/specimen/Noto+Sans+JP)

### Application Layer
- [WordPress x KUSANAGI](https://kusanagi.tokyo/) 🗡
- Test Tool: [Selenium IDE](https://chrome.google.com/webstore/detail/selenium-ide/mooikfkahbdckldjjndioackbalphokd?hl=ja)

### Middleware Layer
- [Nginx](https://nginx.org/en/)
- [MariaDB](https://mariadb.org/) 膃肭臍

### Infra Layer
- [CentOS](https://www.centos.org/)
- [Sakura VPS](https://vps.sakura.ad.jp/)

### Development
- CI Tool: [Travis CI](https://travis-ci.org/256hax/ujull-gnote)
- Repository: [GitHub](https://github.com/256hax/tanebox)

## Test Tool
/home/256hax/selenium_ide/

## Maintenance

### Control Source Code
1. Change source code in Live Server (It means deploy directly).
2. Run Test Tool (Selenium IDE).
3. Commit and Push to GitHub change source code.

### Update Server Information
Run export-server_info.sh.
`$ /home/256hax/server_info/export-server_info.sh`
