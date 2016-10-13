#!/usr/bin/python
import ConfigParser
import string, os, sys

cf = ConfigParser.ConfigParser()
cf.read("config.conf")

list = cf.items("conf");

for index,data in enumerate(list):
	os.system("php /data/wwwroot/vote/index.php %s %s &" % (data[0], data[1]));
