<?php
// api/ai_insight.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';
include_once '../config/config.php';

$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->projetoId) && !empty($dados->nome) && !empty($dados->descricao)) {
    
    $projetoId = intval($dados->projetoId);
    $nome = $dados->nome;
    $descricao = $dados->descricao;
    $insight = "";

    // Check if the API key is configured and not the placeholder
    if (defined('GEMINI_API_KEY') && GEMINI_API_KEY !== 'SUA_CHAVE_DE_API_AQUI' && !empty(GEMINI_API_KEY)) {
        
        $prompt = "Como um gerente de projetos sênior, analise este projeto de software. Nome: {$nome}. Descrição: {$descricao}. Forneça uma análise breve (máximo 3 linhas) focada em possíveis riscos e dicas de execução.";

        // Use gemini-1.5-flash as the default model
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY;
        $data = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ]
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
                'ignore_errors' => true // allows capturing response body on error
            ]
        ];

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        if ($response !== FALSE) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $insight = $result['candidates'][0]['content']['parts'][0]['text'];
            } else {
                // If the key is invalid or returned an API error
                $errorMsg = $result['error']['message'] ?? 'Erro desconhecido na API Gemini.';
                $insight = "⚠️ Erro ao gerar com a IA real: " . $errorMsg . " (Simulação: Avalie riscos de integração e garanta escopo bem alinhado).";
            }
        } else {
            $insight = "⚠️ Não foi possível contatar a API da IA. (Simulação: Foco em testes robustos e validação do escopo).";
        }
    } else {
        // Mock Insight (Simulated/Fallback) for zero-configuration testing
        $riscos = [
            "Riscos de alinhamento de escopo e possíveis atrasos nas entregas devido a requisitos dinâmicos.",
            "Possíveis gargalos na integração com APIs de terceiros e dependências tecnológicas externas.",
            "Risco de gargalo de produtividade caso não haja divisão clara de tarefas entre os desenvolvedores.",
            "Atraso potencial na entrega devido a testes insuficientes e ausência de ambientes de homologação."
        ];
        $dicas = [
            "Dica: Utilize metodologias ágeis (como Scrum ou Kanban) e faça entregas parciais de 2 em 2 semanas.",
            "Dica: Crie uma documentação Swagger/OpenAPI clara para as integrações no início do projeto.",
            "Dica: Defina marcos de entrega (milestones) específicos com o cliente antes do início da codificação.",
            "Dica: Desenvolva testes automatizados para as principais rotas críticas da aplicação."
        ];
        
        $riscoAleatorio = $riscos[array_rand($riscos)];
        $dicaAleatoria = $dicas[array_rand($dicas)];
        
        $insight = "💡 [Simulação - Chave Gemini não configurada] \nRiscos: {$riscoAleatorio}\nExecução: {$dicaAleatoria}";
    }

    // Connect to database and update project with the insight
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "UPDATE projetos SET insight_ia = :insight WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':insight', $insight);
        $stmt->bindParam(':id', $projetoId);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "insight" => $insight
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao salvar o insight no banco de dados."]);
        }
    } else {
        // Return success with insight but database warning
        echo json_encode([
            "status" => "success_no_db",
            "insight" => $insight,
            "message" => "Insight gerado, mas o banco de dados não pôde ser atualizado."
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Dados incompletos para geração de insights."]);
}
?>