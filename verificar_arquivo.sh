#!/bin/bash

echo "🔍 VERIFICANDO ARQUIVO INDEX.BLADE.PHP..."

cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

echo "📋 Primeiras 30 linhas do arquivo:"
head -n 30 resources/views/forcing/index.blade.php

echo ""
echo "🔍 Verificando DOCTYPE:"
grep -n "DOCTYPE\|<html\|<head" resources/views/forcing/index.blade.php

echo ""
echo "🔍 Verificando se há caracteres invisíveis no início:"
hexdump -C resources/views/forcing/index.blade.php | head -n 5

echo ""
echo "🔍 Tamanho do arquivo:"
wc -l resources/views/forcing/index.blade.php
