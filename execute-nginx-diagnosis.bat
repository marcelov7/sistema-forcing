@echo off
echo ===== EXECUTANDO DIAGNÓSTICO AVANÇADO DO NGINX =====
echo.

echo Enviando script de diagnóstico...
scp diagnose-nginx-config.sh devaxis-forcing@31.97.168.137:~/

echo.
echo Conectando via SSH e executando diagnóstico...
echo.

ssh devaxis-forcing@31.97.168.137 "cd ~/htdocs/forcing.devaxis.com.br && chmod +x ~/diagnose-nginx-config.sh && ~/diagnose-nginx-config.sh"

echo.
echo ===== DIAGNÓSTICO CONCLUÍDO =====
pause

