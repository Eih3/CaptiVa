# CaptiVa - Portail Captif

**CaptiVa** est une solution **Open Source** pour le partage sécurisé d'une connexion **Internet**. Il s'agit d'un point d'accès **WiFi** entièrement paramétrable par l'administrateur. Le client se connecte à ce dernier et il sera automatiquement redirigé vers une page d'autentification. Après avoir rentré ses identifiants, le client disposera d'une connexion sécurisé à **Internet**. Quand à l'administrateur, il peut voir en temps réel les sites visités par ses clients.

> C'est une solution très simple et sécurisé.  
> L'administrateur garde le contrôle total de son accés Internet.  
> Cette solution est entièrement Open Source.              
> Pour toute question ou informations : eih3.prog@outlook.fr

Make with ❤ by Waf & Eih3

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

Pour toutes maintenances sur la distribution :

  Login : ```pi```

  Password : ```captiva```

## Installation à partir d'une image neuve

### Mise à jour de votre distribution Linux

En premier lieu il faut repartitionner la distribution afin de disposer de l'espace total sur la carte SD.

```
sudo raspi-config
```

Vous devriez voir cette fenêtre. Sélectionnez la première ligne **Expand Filesystem** et ensuite redémarrer votre Raspberry Pi.


<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/raspi-config.PNG" width="600" />



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

<img src="https://github.com/Eih3/CaptiVa/blob/master/screenshot/iwlist.PNG" width="600" />

### Création d'un point d'accès

Pour créer un point d'accès (AP) nous utiliserons
 **hostapd** pour le point d'accès,
 **isc-dhcp-server** est un serveur **DHCP**,
 **iptables** est un parfeu.

```
sudo apt-get install hostapd isc-dhcp-server iptables iptables-persistent
```



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
