<?php

namespace MacoBackend\Models;

use SimpleDB\Opers;

class SchoolingModel extends Opers
{
    private $table = 'schooling';
    private $columns = [
        'name:varchar(255):not null',
        'status:boolean:not null',
    ];

    public function __construct()
    {
        parent::__construct($this->table, $this->columns, false);
    }

    public function getID()
    {
        return parent::result()->id;
    }

    public function getName()
    {
        return parent::result()->name;
    }

    public function getStatus()
    {
        return parent::result()->status;
    }

    public function getReturnID()
    {
        return parent::result()->returnid;
    }

    public function getCreatedAt()
    {
        return parent::result()->created_at;
    }
}