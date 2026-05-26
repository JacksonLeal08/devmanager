<?php
// api/clientes.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
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
            $query = "SELECT id, nome_fantasia AS nome, razao_social AS razao, email, telefone, data_cadastro AS dataCadastro 
                      FROM clientes WHERE id = :id LIMIT 0,1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $cliente = $stmt->fetch();
            if ($cliente) {
                echo json_encode($cliente);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Cliente não encontrado."]);
            }
        } else {
            $query = "SELECT id, nome_fantasia AS nome, razao_social AS razao, email, telefone, data_cadastro AS dataCadastro 
                      FROM clientes ORDER BY criado_em DESC";
            $stmt = $db->query($query);
            $clientes = $stmt->fetchAll();
            echo json_encode($clientes);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->nome) && !empty($data->email)) {
            $query = "INSERT INTO clientes (nome_fantasia, razao_social, email, telefone, data_cadastro) 
                      VALUES (:nome, :razao, :email, :telefone, :data_cadastro)";
            
            $stmt = $db->prepare($query);
            
            $nome = $data->nome;
            $razao = !empty($data->razao) ? $data->razao : null;
            $email = $data->email;
            $telefone = !empty($data->telefone) ? $data->telefone : null;
            $dataCadastro = !empty($data->dataCadastro) ? $data->dataCadastro : date('Y-m-d');
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':razao', $razao);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':data_cadastro', $dataCadastro);
            
            try {
                if ($stmt->execute()) {
                    $newId = $db->lastInsertId();
                    http_response_code(201);
                    echo json_encode([
                        "message" => "Cliente criado com sucesso.",
                        "id" => intval($newId)
                    ]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Erro ao criar cliente."]);
                }
            } catch (PDOException $e) {
                http_response_code(400);
                if ($e->getCode() == 23000) {
                    echo json_encode(["message" => "E-mail já cadastrado para outro cliente."]);
                } else {
                    echo json_encode(["message" => "Erro no banco de dados: " . $e->getMessage()]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->id) && !empty($data->nome) && !empty($data->email)) {
            $query = "UPDATE clientes 
                      SET nome_fantasia = :nome, razao_social = :razao, email = :email, telefone = :telefone 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            
            $id = intval($data->id);
            $nome = $data->nome;
            $razao = !empty($data->razao) ? $data->razao : null;
            $email = $data->email;
            $telefone = !empty($data->telefone) ? $data->telefone : null;
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':razao', $razao);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            
            try {
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Cliente atualizado com sucesso."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Erro ao atualizar cliente."]);
                }
            } catch (PDOException $e) {
                http_response_code(400);
                if ($e->getCode() == 23000) {
                    echo json_encode(["message" => "E-mail já cadastrado para outro cliente."]);
                } else {
                    echo json_encode(["message" => "Erro no banco de dados: " . $e->getMessage()]);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            // Note: Since projects reference client_id, we need to set client_id to NULL or restrict.
            // Let's check the projects table schema: cliente_id is NOT NULL (Null = NO, Key = MUL).
            // Since it's NOT NULL, we must first delete associated projects or let the database handle it.
            // Let's set it up so we update projects to null? No, database says Null = NO for cliente_id in table 'projetos'.
            // So if we delete a customer, we must either delete their projects first, or return an error.
            // Let's delete projects of that client or update them. But wait, if cliente_id is NOT NULL, we must delete projects first or tell the user.
            // Let's delete the projects of the client first, or run transaction.
            
            $db->beginTransaction();
            try {
                // Excluir projetos vinculados
                $queryProj = "DELETE FROM projetos WHERE cliente_id = :id";
                $stmtProj = $db->prepare($queryProj);
                $stmtProj->bindParam(':id', $id);
                $stmtProj->execute();
                
                // Excluir cliente
                $queryCli = "DELETE FROM clientes WHERE id = :id";
                $stmtCli = $db->prepare($queryCli);
                $stmtCli->bindParam(':id', $id);
                $stmtCli->execute();
                
                $db->commit();
                echo json_encode(["message" => "Cliente e seus projetos excluídos com sucesso."]);
            } catch (PDOException $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir cliente: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID do cliente não informado."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
        break;
}
?>