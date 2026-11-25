<?php
function carregarEnv(string $caminho): void
{
    if (!is_file($caminho)) {
        return;
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        if ($linha === '' || str_starts_with($linha, '#')) {
            continue;
        }

        $partes = explode('=', $linha, 2);

        if (count($partes) < 2) {
            continue;
        }

        $chave = trim($partes[0]);
        $valor = trim($partes[1], " \t\n\r\0\x0B\"'");

        if ($chave !== '') {
            putenv($chave . '=' . $valor);
            $_ENV[$chave] = $valor;
        }
    }
}