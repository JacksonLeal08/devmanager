<?php
// api/projetos.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["message" => "Não foi possível conectar ao banco de dados."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "SELECT id, cliente_id AS clienteId, modalidade_id AS modalidadeId, nome, status, 
                             data_inicio AS dataInicio, prazo_final AS prazoFinal, valor_total AS valorTotal, 
                             descricao, insight_ia AS insightIa 
                      FROM projetos WHERE id = :id LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $projeto = $stmt->fetch();
            if ($projeto) {
                echo json_encode($projeto);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Projeto não encontrado."]);
            }
        } else {
            // Check for status filter
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            if ($status) {
                $query = "SELECT id, cliente_id AS clienteId, modalidade_id AS modalidadeId, nome, status, 
                                 data_inicio AS dataInicio, prazo_final AS prazoFinal, valor_total AS valorTotal, 
                                 descricao, insight_ia AS insightIa 
                          FROM projetos WHERE status = :status ORDER BY criado_em DESC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
                $projetos = $stmt->fetchAll();
            } else {
                $query = "SELECT id, cliente_id AS clienteId, modalidade_id AS modalidadeId, nome, status, 
                                 data_inicio AS dataInicio, prazo_final AS prazoFinal, valor_total AS valorTotal, 
                                 descricao, insight_ia AS insightIa 
                          FROM projetos ORDER BY criado_em DESC";
                $stmt = $db->query($query);
                $projetos = $stmt->fetchAll();
            }
            echo json_encode($projetos);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->nome) && !empty($data->clienteId) && !empty($data->status) && 
            !empty($data->dataInicio) && isset($data->valorTotal)) {
            
            $query = "INSERT INTO projetos (cliente_id, modalidade_id, nome, status, data_inicio, prazo_final, valor_total, descricao, insight_ia) 
                      VALUES (:cliente_id, :modalidade_id, :nome, :status, :data_inicio, :prazo_final, :valor_total, :descricao, :insight_ia)";
            
            $stmt = $db->prepare($query);
            
            $clienteId = intval($data->clienteId);
            $modalidadeId = !empty($data->modalidadeId) ? intval($data->modalidadeId) : null;
            $nome = $data->nome;
            $status = $data->status;
            $dataInicio = $data->dataInicio;
            $prazoFinal = !empty($data->prazoFinal) ? $data->prazoFinal : null;
            $valorTotal = floatval($data->valorTotal);
            $descricao = !empty($data->descricao) ? $data->descricao : null;
            $insightIa = !empty($data->insightIa) ? $data->insightIa : null;
            
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->bindParam(':modalidade_id', $modalidadeId);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':prazo_final', $prazoFinal);
            $stmt->bindParam(':valor_total', $valorTotal);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':insight_ia', $insightIa);
            
            if ($stmt->execute()) {
                $newId = $db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    "message" => "Projeto criado com sucesso.",
                    "id" => intval($newId)
                ]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Erro ao criar projeto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->id) && !empty($data->nome) && !empty($data->clienteId) && 
            !empty($data->status) && !empty($data->dataInicio) && isset($data->valorTotal)) {
            
            $query = "UPDATE projetos 
                      SET cliente_id = :cliente_id, modalidade_id = :modalidade_id, nome = :nome, status = :status, 
                          data_inicio = :data_inicio, prazo_final = :prazo_final, valor_total = :valor_total, 
                          descricao = :descricao 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            
            $id = intval($data->id);
            $clienteId = intval($data->clienteId);
            $modalidadeId = !empty($data->modalidadeId) ? intval($data->modalidadeId) : null;
            $nome = $data->nome;
            $status = $data->status;
            $dataInicio = $data->dataInicio;
            $prazoFinal = !empty($data->prazoFinal) ? $data->prazoFinal : null;
            $valorTotal = floatval($data->valorTotal);
            $descricao = !empty($data->descricao) ? $data->descricao : null;
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':cliente_id', $clienteId);
            $stmt->bindParam(':modalidade_id', $modalidadeId);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':prazo_final', $prazoFinal);
            $stmt->bindParam(':valor_total', $valorTotal);
            $stmt->bindParam(':descricao', $descricao);
            
            if ($stmt->execute()) {
                echo json_encode(["message" => "Projeto atualizado com sucesso."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Erro ao atualizar projeto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "DELETE FROM projetos WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                echo json_encode(["message" => "Projeto excluído com sucesso."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Erro ao excluir projeto."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID do projeto não informado."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}
?>
