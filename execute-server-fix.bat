@echo off
echo === EXECUTANDO CORREÇÃO NO SERVIDOR ===
echo.

echo 1. Fazendo upload do script de correção...
scp fix-server-config.sh devaxis-forcing@forcing.devaxis.com.br:~/
if errorlevel 1 (
    echo ERRO: Falha no upload do script
    pause
    exit /b 1
)
echo ✓ Script enviado com sucesso
echo.

echo 2. Executando o script no servidor...
ssh devaxis-forcing@forcing.devaxis.com.br "chmod +x ~/fix-server-config.sh && ~/fix-server-config.sh"
if errorlevel 1 (
    echo ERRO: Falha na execução do script
    pause
    exit /b 1
)
echo.

echo 3. Removendo o script temporário do servidor...
ssh devaxis-forcing@forcing.devaxis.com.br "rm ~/fix-server-config.sh"
echo.

echo === CORREÇÃO CONCLUÍDA ===
echo Agora teste o site: https://forcing.devaxis.com.br
echo.
pause

