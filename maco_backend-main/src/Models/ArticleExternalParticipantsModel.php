<?php

namespace MacoBackend\Models;

use SimpleDB\Opers;

class ArticleExternalParticipantsModel extends Opers {

    private $table = 'article_external_participants';
    private $columns = [
        'article:int:not null',
        'name:varchar:not null',
        'email:varchar:not null',
        'type:enum:not null',
    ];

    public function __construct() {
        parent::__construct($this->table, $this->columns, false);
    }

    public function getID()
    {
        return parent::result()->id;
    }

    public function getArticle()
    {
        return parent::result()->article;
    }

    public function getName()
    {
        return parent::result()->name;
    }

    public function getEmail()
    {
        return parent::result()->email;
    }

    public function getType()
    {
        return parent::result()->type;
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