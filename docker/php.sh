#VSCode[PHP IntelliSense]で、Docker内のPHPを参照するために使う。
#!/bin/sh
docker-compose exec web php "$@"
exit $?