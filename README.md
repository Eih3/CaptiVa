# CaptiVa - Portail Captif

**CaptiVa** est une solution **Open Source** pour le partage sécurisé d'une connexion **Internet**. Il s'agit d'un point d'accès **WiFi** entièrement paramétrable par l'administrateur. Le client se connecte à ce dernier et il sera automatiquement redirigé vers une page d'autentification. Après avoir rentré ses identifiants, le client disposera d'une connexion sécurisé à **Internet**. Quand à l'administrateur, il peut voir en temps réel les sites visités par ses clients.

> C'est une solution très simple et sécurisé.  
> L'administrateur garde le contrôle total de son accés Internet.  
> Cette solution est entièrement Open Source.              
> Pour toute question ou informations : eih3.prog@outlook.fr

### Matériel Requis

- Raspberry Pi
- Dongle WiFi [1]
- Carte SD 8 Go
- Une distribution Raspbian
- Un ordinateur pour l'installation

Nous allons partir d'une image Linux neuve afin d'installer le strict nécessaire pour notre solution de portail captif.

Dans ce projet, nous utiliserons un **RaspBerry Pi 3 Modèle B**.
En fait ce modèle de carte inclut une interface **WiFi** et une interface **Bluetooth**. Nous navons donc pas à nous soucier d'un dongle **WiFi**.

[1] Votre dongle WiFi devra supporter le mode **AP** (mode infrastructure) ainsi que le mode **maître**.


## Installation de CaptiVa

Téléchargez la dernière image **Raspbian** en cliquant sur ce lien
https://downloads.raspberrypi.org/raspbian_latest

Login : ```pi```

Password : ```raspberry```

Suivez ce guide pour installer l'image sur votre carte SD http://bit.ly/win32disk

### Mise à jour de votre distribution Linux

En premier lieu il faut repartitionner la distribution afin de disposer de l'espace total sur la carte SD.

```
sudo raspi-config
```

Vous devriez voir cette fenêtre. Sélectionnez la première ligne **Expand Filesystem** et ensuite redémarrer votre Raspberry Pi.

<div style="text-align:center">
  <img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/raspi-config.PNG" width="600" />
</div>

Il faut maintenant mettre à jour la distribution afin de disposer des nouveaux packets.

```
sudo apt-get update
sudo apt-get dist-upgrade
```

### Vérification de l'interface WiFi

Pour que la solution CaptiVa fonctionne correctement, il faut être sûr que votre dongle **WiFi** (ou dans notre cas l'interface intégrée au **Raspberry Pi**) supporte bien le mode **AP**.

```
sudo iw list
```

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/iwlist.PNG" width="600" align="middle">


### Installation du serveur WEB

Nous allons installer le service **nginx** pour le serveur WEB ainsi que **PHP**.

```
sudo apt-get install php5-fpm php5-gmp nginx
```

Modification de la configuration

```
sudo sed -i 's/worker_processes 4/worker_processes 1/g' /etc/nginx/nginx.conf

sudo sed -i 's/application\/octet-stream/text\/html/g' /etc/nginx/nginx.conf

sudo sed -i 's/var\/log\/nginx\/access.log/home\/pi\/\/lon\/log\/nginx-access.log/g' /etc/nginx/nginx.conf

sudo sed -i 's/var\/log\/nginx\/error.log/home\/pi\/\/lon\/log\/nginx-error.log/g' /etc/nginx/nginx.conf
```

Création d'un nouveau serveur WEB

```
sudo sh -c 'echo "" > /etc/nginx/sites-available/default'

sudo nano /etc/nginx/sites-available/default
```

Insert this:

```
##
# Pilon captive portal server
##
server {
  listen 80;
  server_name localhost;

  root /home/pi/lon/www;
  index index.html;

  location / {
    try_files $uri $uri/ /redirect.php;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_index /redirect.php;
    fastcgi_param PHP_VALUE "include_path=/home/pi/lon/etc";
    include fastcgi.conf;
  }
}
```

Give it a final touch with:

```
sudo /etc/init.d/nginx reload
```

Test this setup by visiting the ip address of your pi.

Use ifconfig if you're not sure what's your ip.

### Wifi dongle as bridge

Follow this guide to get your wifi dongle working and bridging:

http://www.daveconroy.com/turn-your-raspberry-pi-into-a-wifi-hotspot-with-edimax-nano-usb-ew-7811un-rtl8188cus-chipset/

I suggest to skip the first part (except for passwd) and start from installing hostapd and bridge-utils

### Redirect to captive portal

These settings should work:

```
# Start from scratch
iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X
iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT

# Redirect to nginx server
iptables -t mangle -N internet
iptables -t mangle -A PREROUTING -p tcp --dport 80:50000 -j internet
iptables -t mangle -A internet -j MARK --set-mark 99
iptables -t nat -A PREROUTING -p tcp -m mark --mark 99 -j DNAT --to-destination $(ifconfig eth0 | grep "inet addr" | awk -F: '{print $2}' | awk '{print $1}'):80

# Whitelisting
# Coinbase
iptables -I internet 1 -t mangle -p tcp -d coinbase.com --dport 80 -j RETURN
iptables -I internet 1 -t mangle -p tcp -d coinbase.com --dport 443 -j RETURN
# blockchain.info
iptables -I internet 1 -t mangle -p tcp -d blockchain.info --dport 80 -j RETURN
iptables -I internet 1 -t mangle -p tcp -d blockchain.info --dport 443 -j RETURN
# bitcoin.org
iptables -I internet 1 -t mangle -p tcp -d bitcoin.org --dport 80 -j RETURN
iptables -I internet 1 -t mangle -p tcp -d bitcoin.org --dport 443 -j RETURN
# fonts
iptables -I internet 1 -t mangle -p tcp -d fonts.googleapis.com --dport 80 -j RETURN
iptables -I internet 1 -t mangle -p tcp -d fonts.googleapis.com --dport 443 -j RETURN

```
