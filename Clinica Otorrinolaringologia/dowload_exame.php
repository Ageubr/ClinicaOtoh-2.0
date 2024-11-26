<?php
include('db.php');
$pdoConsultas = conectarBanco('ConsultasDB');

if (isset($_GET['consulta_id'])) {
    $consulta_id = $_GET['consulta_id'];

    // Consulta o banco para obter o conteúdo do arquivo
    $sql = "SELECT exame_file FROM consultas WHERE id = :consulta_id";
    $stmt = $pdoConsultas->prepare($sql);
    $stmt->bindParam(':consulta_id', $consulta_id, PDO::PARAM_INT);
    $stmt->execute();
    $exame = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exame && !empty($exame['exame_file'])) {
        // O conteúdo do arquivo é um stream ou binário armazenado como BYTEA
        $arquivo_binario = $exame['exame_file'];

        // Se o conteúdo for um recurso (stream), converte-o em string
        if (is_resource($arquivo_binario)) {
            $arquivo_binario = stream_get_contents($arquivo_binario);
        }

        // Define os cabeçalhos apropriados para o download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="exame_' . $consulta_id . '.png"');
        header('Content-Length: ' . strlen($arquivo_binario));

        // Envia o conteúdo binário do arquivo
        echo $arquivo_binario;
        exit;
    } else {
        echo "Erro: Arquivo de exame não encontrado.";
    }
} else {
    echo "Erro: ID da consulta não fornecido.";
}
?>
