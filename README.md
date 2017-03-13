# Lukkari
0.5.0

# Install
```bash
php-dom
```
Permission
```bash
sudo chown www-data. $FOLDER
```

### APC
```bash
apt-get install php-pear php7.0-dev make libpcre3-dev
pecl install apc
```
Add `extension = apc.so` to bottom of`/etc/php/7.0/apache2/php.ini`

# TODO
- InlineQuery (Dates?)
- Delete "No data!" entries daily
- APC
- lukkari max distance half a year.
