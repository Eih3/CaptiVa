# CaptiVa - Portail Captif

**CaptiVa** est une solution **Open Source** pour le partage sécurisé d'une connexion **Internet**. Il s'agit d'un point d'accès **WiFi** entièrement paramétrable par l'administrateur. Le client se connecte à ce dernier et il sera automatiquement redirigé vers une page d'autentification. Après avoir rentré ses identifiants, le client disposera d'une connexion sécurisé à **Internet**. Quand à l'administrateur, il peut voir en temps réel les sites visités par ses clients.

> C'est une solution très simple et sécurisé.  
> L'administrateur garde le contrôle total de son accés Internet.  
> Cette solution est entièrement Open Source.              
> Pour toute question ou informations : eih3.prog@outlook.fr

## SOMMAIRE

  - [Installation de CaptiVa](#installation-de-captiva)
  - [Installation à partir de zéro](#installation-à-partir-de-zéro)
    - [Adding toc to all files in a directory and sub directories](#adding-toc-to-all-files-in-a-directory-and-sub-directories)
    - [Update existing doctoc TOCs effortlessly](#update-existing-doctoc-tocs-effortlessly)
    - [Adding toc to individual files](#adding-toc-to-individual-files)
      - [Examples](#examples)
    - [Using doctoc to generate links compatible with other sites](#using-doctoc-to-generate-links-compatible-with-other-sites)
      - [Example](#example)
    - [Specifying location of toc](#specifying-location-of-toc)
    - [Specifying a custom TOC title](#specifying-a-custom-toc-title)
    - [Specifying a maximum heading level for TOC entries](#specifying-a-maximum-heading-level-for-toc-entries)

### Matériel Requis

- Raspberry Pi
- Dongle WiFi [1]
- Carte SD 8 Go
- Une distribution Raspbian
- Un ordinateur pour l'installation

Dans ce projet, nous utiliserons un **RaspBerry Pi 3 Modèle B**.
En fait ce modèle de carte inclut une interface **WiFi** et une interface **Bluetooth**. Nous n'avons donc pas à nous soucier d'un dongle **WiFi**.

[1] Votre dongle **WiFi** devra supporter le mode **AP** (mode infrastructure) ainsi que le mode **maître**.

## Installation de CaptiVa  

Il s'agit d'une image prête à être gravée sur une carte SD et vous évite donc de rentrer dans le coeur du système de votre **RaspBerry Pi**.

###Guide de démarrage avec **CaptiVa** pré-installé :

  1. Téléchargez la dernière version de l'image en cliquant sur ce lien http://bit.ly/win32disk.

  2. Gravez l'image sur une **carte SD** d'au moins **4 Go** en suivant ce guide http://bit.ly/win32disk.

  3. Insérez la carte SD dans votre **RPi**, raccordez le au réseau **Internet** avec un câble **LAN** et alimentez le avec un câble **Mini USB**.

  4. Si tout fonctionne correctement, vous devriez voir un nouveau réseau **WiFi** du nom de "CaptiVa" qui vient de se créer.

  5. Connectez vous à ce réseau **WiFi** avec votre ordinateur. Vous aller être rediriger vers la page de configuration de votre système de portail captif.

  6. L'installation de votre portail captif est désormais terminée.


Pour vous connecter en mode **SSH** :

* Login : ```pi```

* Password : ```captiva```

## Installation à partir de zéro

Vous avez choisi d'installer vous même la solution **CaptiVa** en passant par la case bidouille et programmation. Nous allons ensemble apprendre à configurer un point d'accès **WiFi** pour permettre le partage d'une connexion **Internet** pour de multiples clients.

### Téléchargement de Raspbian

En tout premier lieu il vous faut télécharger une image de la distribution **Raspbian** en cliquant sur ce lien http://bit.ly/win32disk.

Ensuite vous devez suivre ce guide http://bit.ly/win32disk pour graver votre image sur une **carte SD**.

Insérez votre **carte SD** fraîchement gravé dans le **RPi** et passez à l'étape suivante.

### Avoir un accès au système

Vous avez deux solutions qui s'offrent à vous afin d'avoir accès au système du **RPi** et ainsi pouvoir installer les packets nécessaires à la création d'un portail captif.

La première est un accès direct à l'aide d'un écran et d'un clavier / souris. C'est un choix simple qui nécessitera une connexion filaire à **Internet** à l'aide d'un câble **LAN**.

La deuxième solution est un accès à distance à l'aide d'une connexion **SSH**. Là encore il sera nécessaire d'avoir une connexion filaire à **Internet**.

Nous vous conseillons de choisir la première solution si vous êtes débutant. En revanche, la deuxième solution sera plus pratique pour ceux qui maitrisent **PuTTy** et autres outils de communication **SSH**.

Pour la suite de ce tutoriel, les commandes sont à rentrées dans une console.

Pour vous connecter en mode **SSH** :

* Login : ```pi```

* Password : ```raspberry```

### Mise à jour de votre distribution Linux

Avant de mettre à jour votre distribution **Raspbian**, il vous faut effectuer un changement de taille de partition. Cette action vous permettra de repartitionner la distribution afin de disposer de l'espace total disponible sur la **carte SD**.

```
sudo raspi-config
```

Vous devriez voir cette fenêtre. Sélectionnez la première ligne **Expand Filesystem** et ensuite redémarrer votre Raspberry Pi.

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/raspi-config.png" width="600" />


Il faut maintenant mettre à jour la distribution afin de disposer des nouveaux packets.

```
sudo apt-get update
sudo apt-get upgrade
sudo apt-get dist-upgrade
```

### Paramétrage de la carte WiFi

Pour que cette solution de portail captif fonctionne correctement, il faut être sûr que votre dongle **WiFi** (ou dans notre cas l'interface intégrée au **Raspberry Pi**) supporte bien le mode **AP**.

```
sudo iw list
```

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/iwlist.png" width="600" />


Nous allons maintenant configurer l'adresse **IP** de l'interface **WiFi**

```
sudo nano /etc/network/interfaces
```
Le contenu de notre fichier 'interfaces' est le suivant :

```
# interfaces(5) file used by ifup(8) and ifdown(8)

# Please note that this file is written to be used with dhcpcd
# For static IP, consult /etc/dhcpcd.conf and 'man dhcpcd.conf'

# Include files from /etc/network/interfaces.d:
source-directory /etc/network/interfaces.d

auto lo
iface lo inet loopback

iface eth0 inet dhcp

allow-hotplug wlan0
#iface wlan0 inet manual
#    wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf
iface wlan0 inet static
address 192.168.0.100
netmask 255.255.255.0
network 192.168.0.0

allow-hotplug wlan1
iface wlan1 inet manual
    wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf

```

Il est possible de désactiver **wpa_supplicant**

```
sudo mv /usr/share/dbus-1/system-services/fi.epitest.hostap.WPASupplicant.service ~/
```

Il vous faut maintenant redémarrer le service qui gère les interfaces réseaux afin qu'il prenne en compte les modifications des paramètres

```
sudo /etc/init.d/networking restart
```

### Création d'un point d'accès

Pour créer un point d'accès (**AP**) nous utiliserons :
 * **hostapd** pour le point d'accès,
 * **isc-dhcp-server** pour le serveur **DHCP**,
 * **iptables** pour créer un parfeu.

```
sudo apt-get install hostapd isc-dhcp-server iptables iptables-persistent
```

Pour effectuer des modifications sur ces packets :
 * **hostapd** -> _/etc/default/hostapd_ et _/etc/hostapd/hostapd.conf_
 * **isc-dhcp-server** -> _/etc/default/isc-dhcp-server_ et _/etc/dhcp/dhcpd.conf_

<h4>Configuration Hostapd</h4>

Nous devons créer un fichier de configuration pour le point d'accés **WiFi**

```
sudo nano /etc/hostapd/hostapd.conf
```

Dans ce fichier, il vous faut définir tous les paramètres de votre point d'accès **WiFi**

```
# interface wlan du WiFi
interface=wlan0
# nl80211 avec tous les drivers Linux mac80211
driver=nl80211
# Nom du hotspot WiFi
ssid=CaptiVa
# mode WiFi (a = IEEE 802.11a, b = IEEE 802.11b, g = IEEE 802.11g)
hw_mode=g
# canal de fréquence WiFi (1-14)
channel=6
# WiFi ouvert, pas d'authentification !
auth_algs=1
# Beacon interval in kus (1.024 ms)
beacon_int=100
# DTIM (delivery trafic information message)
dtim_period=2
# Maximum number of stations allowed in station table
max_num_sta=255
# RTS/CTS threshold; 2347 = disabled (default)
rts_threshold=2347
# Fragmentation threshold; 2346 = disabled (default)
fragm_threshold=2346
```

Par la suite il faut indiquer l'endroit où se trouve ce fichier de configuration

```
sudo nano /etc/default/hostapd
```

et ajoutez à la fin du fichier la ligne suivante

```
DAEMON_CONF="/etc/hostapd/hostapd.conf"
```
Il est désormais possible de démarrer le service pour voir si le point d'accès à bien été créer

```
sudo service hostapd start
```
Si tout c'est bien passé, vous devriez voir apparaître un nouveau réseau **WiFi** du nom de **CaptiVa**

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/wifi.png" width="300" />

Si ce n'est pas le cas, il vous suffit de redémarrer votre **RPi**

```
sudo reboot
```

Pour être sur que le point d'accès sera activé au prochain redémarrage

```
sudo update-rc.d hostapd enable
```

<h4>Configuration du serveur DHCP</h4>

Nous allons configurer notre serveur **DHCP** afin d'attribuer automatiquement une adresse **IP** et d'autres informations importantes pour les clients qui se connecterons sur notre point d'accès **WiFi**

```
sudo nano /etc/dhcp/dhcpd.conf
```

Ajoutez les lignes de configuration suivantes à la fin du fichier

```
subnet 192.168.0.0 netmask 255.255.255.0 {
 range 192.168.0.10 192.168.0.60;
 option routers 192.168.0.100;
 option domain-name-servers 192.168.0.100;
}
```

Le serveur **DHCP** a besoin de savoir sur quelle interface réseau il devra attribuer les données de configuration **IP**

```
sudo nano /etc/default/isc-dhcp-server
```

Modifiez la ligne ou ajoutez la en fin de fichier

```
INTERFACES="wlan0"
```

Le serveur **DHCP** peut rencontrer certains problèmes de gestion d'interfaces réseaux. Pour éviter de futur complication il est plus sage de créer un fichier

```
sudo nano /etc/default/ifplugd
```

et d'y ajouter les lignes suivantes

```
INTERFACES="eth0"
HOTPLUG_INTERFACES="eth0"
ARGS="-q -f -u0 -d10 -w -I"
SUSPEND_ACTION="stop"
```

Il faut être sur que notre serveur **DHCP** sera bien activé au prochain redémarrage de notre **RPi**

```
sudo update-rc.d isc-dhcp-server enable
```

Nous allons réaliser le routage des packets entre l'interface **eth0** et **wlan0**. Pour cela mofifiez le fichier de configuration **sysctl.conf**

```
sudo nano /etc/sysctl.conf
```

Trouvez la ligne suivante

```
#net.ipv4.ip_forward=1
```

et activez la en supprimant **#** en début de ligne. Ensuite vous devez rediriger tous les packets entrant sur le parfeu

```
sudo iptables -A POSTROUTING -t nat -o eth0 -j MASQUERADE
```


### Installation d'un serveur WEB

Nous allons installer le service **Nginx** pour créer notre serveur **WEB**. Ce service est léger et rapide, c'est donc une solution parfaite pour notre portail captif.

```
sudo aptitude install nginx php5-fpm
```

Une fois que l'installation est finie, ouvrez votre navigateur **WEB** et saisissez l'addresse de votre  **RPi** (connectez vous au point d'accès **CaptiVa**) dans la barre de recherche. Si vous obtenez la page suivante c'est que votre serveur **WEB** est bien installé

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/web_nginx.png" width="400" />


Il nous faut maintenant configurer notre serveur **Nginx**

```
nano /etc/nginx/sites-available/default
```

et cherchez la ligne suivante

```
index index.html index.htm index.nginx-debian.html;
```

pour la remplacer par cette ligne

```
index index.html index.htm index.php;
```

Cela va rajouter une redirection automatique vers les fichiers « index.php » pour les dossiers du site, comportement par défaut de Apache.

Nous allons maintenant activer php-fpm pour **Nginx**. Pour cela, toujours dans le même fichier, cherchez les lignes suivantes

```
#location ~ \.php$ {
# include snippets/fastcgi-php.conf;
#
# # With php5-cgi alone:
# fastcgi_pass 127.0.0.1:9000;
# # With php5-fpm:
# fastcgi_pass unix:/var/run/php5-fpm.sock;
#}
```

et modifier les afin d’obtenir le résultat suivant :

```
location ~ \.php$ {
include snippets/fastcgi-php.conf;
fastcgi_pass unix:/var/run/php5-fpm.sock;
}
```

de la même façon, cherchez la ligne

```
root /var/www/html
```

et remplacez la par la ligne suivante :

```
root /home/pi/www
```

Nous allons créer un dossier qui contiendra nos pages **WEB**

```
sudo mkdir /home/pi/www
```

Afin de vérifier le bon fonctionnement de ces paramètres, nous allons créer un fichier **PHP** dans le dossier où seront stockées les pages du serveur **WEB**

```
sudo nano /home/pi/www/index.php
```

et ajoutez dans ce fichier la ligne suivante :

```
<?php phpinfo(); ?>
```

Une fois tout ceci fait, vous devez redémarrer le service **Nginx**  afin d’appliquer les modifications

```
sudo /etc/init.d/nginx restart
```

Vous n'avez plus qu'à vous rendre sur la page du serveur à l'aide de votre navigateur **WEB** comme nous l'avons vu précédemment. Si tout c'est bien passé, vous devriez voir la page suivante

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/phpinfo.png" width="600" />

Si vous rencontrez l'erreur **403 Forbiden**

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/403.png" width="600" />

C'est que le serveur n'a pas accès aux fichiers ou qu'il n'y a aucun fichier à lancer par défaut : index.php, index.html ou index.htm.

### Installation d'un serveur FTP

La création d'un serveur **FTP** va nous permettre d'avoir un accès à distance à notre **RPi** afin de simplifier l'envoi de fichiers sur le serveur **WEB**.

```
sudo apt-get install vsftpd
```

Nous allons effectuer une copie du fichier initial de configuration du serveur **FTP**

```
sudo cp /etc/vsftpd.conf /etc/vsftpd.conf.old
sudo rm /etc/vsftpd.conf
```

Il faut maintenant accéder au fichier de configuration

```
sudo nano /etc/vsftpd.conf
```

et copiez les lignes suivantes dans le fichier de configuration qui doit être vide

```
listen=YES
anonymous_enable=NO
local_enable=YES
write_enable=YES
local_umask=022
anon_upload_enable=NO
anon_mkdir_write_enable=NO
xferlog_enable=NO
xferlog_file=/var/log/vsftpd.log
connect_from_port_20=YES
idle_session_timeout=300
data_connection_timeout=120
connect_timeout=60
accept_timeout=60
async_abor_enable=NO
ascii_upload_enable=NO
ascii_download_enable=NO
ftpd_banner=CaptiVa FTP
use_localtime=YES
```

Afin d'autoriser le client **FTP** à accéder aux fichiers de l'utilisateur et ainsi de disposer des droits d'écritures

```
sudo chown -R pi /home/pi
```

Pour finir, redémarrez votre serveur **FTP** et connectez vous sur le serveur à l'aide d'un outil de gestion **FTP** tel que **FileZilla FTP**.

```
sudo /etc/init.d/vsftpd restart
```

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/filezilla.png" width="600" />

***

Made with ❤ by **Waf** & **Eih3**
