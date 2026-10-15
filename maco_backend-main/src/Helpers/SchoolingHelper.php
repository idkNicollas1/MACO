<?php

namespace MacoBackend\Helpers;

use MacoBackend\Models\SchoolingModel;

class SchoolingHelper
{

    /**
     * Função que monta a condição where com base nos parametros passados na url
     *
     * @param $params
     */
    public static function conditionByList(object $params): string
    {
        if (isset($params->schooling_id)) {
            return "id = " . $params->schooling_id;
        }
        if (isset($params->schooling_name)) {
            return "name like '%" . $params->schooling_name . "%'";
        }
        return '';
    }

    /**
     * Pega a escolaridade pelo id
     *
     * @param $id
     */
    public static function getSchoolingByID($id)
    {
        $schooling = new SchoolingModel();
        $schooling->select(['name'])
            ->where("id = $id")
            ->get();

        return $schooling->getName();
    }

}