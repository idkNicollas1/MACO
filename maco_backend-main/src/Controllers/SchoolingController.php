<?php

namespace MacoBackend\Controllers;

use MacoBackend\Helpers\LogHelper;
use MacoBackend\Helpers\SchoolingHelper;
use MacoBackend\Helpers\UserHelper;
use MacoBackend\Models\RoleModel;
use MacoBackend\Models\SchoolingModel;
use MacoBackend\Models\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SchoolingController
{

    // listSchooling
    /**
     * Realiza a listagem da escolaridade de um usuario
     *
     * @return Response
     */
    public function listSchooling(Request $request, Response $response, $args): Response
    {
        $params = (object) $request->getQueryParams();

        if (isset($params->user_id)) {
            $schooling = new UserModel();
            $schooling->select(['schooling.*'])
                ->innerjoin('schooling on schooling.id = user.schooling')
                ->where("user.id = {$params->user_id}")
                ->get(true);
        } else {
            $condition = SchoolingHelper::conditionByList($params);

            $schooling = new SchoolingModel();
            $schooling->select()
                ->where($condition)
                ->orderby()
                ->get(true);
        }
        return ResponseController::data($response, $schooling->result());
    }

    /**
     * Realiza a listagem de escolaridades para a pagina publica de cadastro
     *
     * @return Response
     */
    public function listSchoolingPublic(Request $request, Response $response, $args): Response
    {
        $schooling = new SchoolingModel();
        $schooling->select(['id', 'name'])
            ->where('status = 1')
            ->orderby()
            ->get(true);

        return ResponseController::data($response, $schooling->result());
    }

    /**
     * Realiza a inserção de escolaridade
     *
     * @return Response
     */
    public function addSchooling(Request $request, Response $response, $args): Response
    {
        if (UserHelper::checkUserRole($request, RoleModel::ADMIN)) {
            return ResponseController::message($response, 'error', 'Operation denied! User is not a admin');
        }

        $parsedBody = $request->getParsedBody();

        $name = $parsedBody['name'];
        $status = $parsedBody['status'];

        if (empty($name)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $schooling = new SchoolingModel();
        $schooling->data([
            'name' => $name,
            'status' => $status,
        ])->insert();

        LogHelper::log('Escolaridade', 'Adição de Escolaridade', $request);

        if ($schooling->result()->status != 'success') {
            return ResponseController::message($response, 'error', $schooling->result()->message);
        }
        return ResponseController::message($response, $schooling->result()->status, 'Schooling inserted successfully');
    }

    // editSchooling
    /**
     * Realiza a edição de um curso
     *
     * @return Response
     */
    public function editSchooling(Request $request, Response $response, $args): Response
    {
        if (UserHelper::checkUserRole($request, RoleModel::ADMIN)) {
            $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Operation denied! User is not a admin']));
            return $response;
        }

        $parsedBody = $request->getParsedBody();

        $id = $parsedBody['id'];
        $name = $parsedBody['name'];
        $status = $parsedBody['status'];

        if (empty($id) || empty($name))
        {
            $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Missing information']));
            return $response;
        }

        $schooling = new SchoolingModel();
        $schooling->data([
            'name' => $name,
            'status' => $status
        ])->where("id = {$id}")->update();

        LogHelper::log('Escolaridade', 'Edição de Escolaridade', $request);

        if ($schooling->result()->status != 'success') {
            return ResponseController::message($response, 'error', $schooling->result()->message);
        }
        return ResponseController::message($response, $schooling->result()->status, 'Schooling edited successfully');
    }

}