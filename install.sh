#!/bin/sh

# create a new network called "server"
docker network create server

# pull and start all services 
docker compose up -d

# install certbot timer
sudo cp certbot-renew.service  certbot-renew.timer /etc/systemd/system/
sudo systemctl enable certbot-renew.timer
