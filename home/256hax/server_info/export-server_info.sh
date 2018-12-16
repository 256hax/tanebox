#!/usr/bin/bash

df -h > df-h.txt
free -h > free-h.txt
ps -aux > ps-aux.txt
yum list > yum-list.txt

sudo find / > find.txt
