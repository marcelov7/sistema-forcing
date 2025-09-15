<?php
// Script simples para debug das rotas
echo "<h1>Debug - Sistema de Forcing</h1>";
echo "<h2>Teste de Links</h2>";

$baseUrl = "http://127.0.0.1:8000";

$links = [
    "Página Principal" => "/",
    "Lista de Forcing" => "/forcing",
    "Termos" => "/forcing/terms",
    "Teste Bootstrap" => "/teste-bootstrap.html",
    "Teste Debug" => "/teste-forcing-debug.html"
];

echo "<ul>";
foreach($links as $nome => $url) {
    echo "<li><a href='$baseUrl$url' target='_blank' style='color: blue; text-decoration: underline;'>$nome</a></li>";
}
echo "</ul>";

echo "<h2>Informações do Sistema</h2>";
echo "<p><strong>URL Base:</strong> $baseUrl</p>";
echo "<p><strong>Pasta atual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #333; }
h2 { color: #666; }
li { margin: 5px 0; }
</style>
