<?php

namespace MacoBackend\Helpers;

use MacoBackend\Models\ArticleAdvisorsModel;
use MacoBackend\Models\ArticleAuthorsModel;
use MacoBackend\Models\ArticleCommentsModel;
use MacoBackend\Models\ArticleExternalParticipantsModel;
use MacoBackend\Models\ArticleModel;
use MacoBackend\Models\ArticleReferencesModel;
use MacoBackend\Models\UserCourseModel;

class ArticleHelper
{    
    /**
     * Função que monta a condição where com base nos parametros passados na url
     * 
     * @param $params
     */
    public static function conditionByList(object $params): string
    {
        if (isset($params->article_id)) {
            return "article.id = {$params->article_id}";
        }

        $conditions = [];

        if (isset($params->article_title)) {
            $conditions[] = "article.title like '%{$params->article_title}%'";
        }

        if (isset($params->article_status)) {
            $conditions[] = "article.status = {$params->article_status}";
        }

        if (isset($params->event_id)) {
            $conditions[] = "article.event = {$params->event_id}";
        }

        if (isset($params->origin) && $params->origin != 'all') {
            $conditions[] = "article.origin = '{$params->origin}'";

            if ($params->origin == 'internal' && isset($params->course_id)) {
                $conditions[] = "({$params->course_id} in (select course from article_authors where article = article.id))";
            } elseif ($params->origin == 'external' && isset($params->schooling_id)) {
                $conditions[] = "({$params->schooling_id} in (select schooling from article_authors where article = article.id))";
            }
        }

        return implode(' and ', $conditions);
    }
    
    /**
     * Função que retorna a condição da consulta de revisores
     * 
     * A condição retorna artigos somente dos cursos no qual o revisor está matriculado
     * 
     * @param $advisorID
     * @param $condition
     */
    public static function getConditionAdvisor($advisorID, $condition): string
    {
        if (strlen($condition) > 5) {
            $condition = " and {$condition}";
        }

        $advisor = new UserCourseModel();
        $advisor->select(['course'])
                ->where("user = {$advisorID}")
                ->get(true);
                
        $queryCourse = '';
        foreach($advisor->result() as $c) {
            $queryCourse .= $c['course'] . ' in (select course from article_authors where article = article.id) or ';
        }
        $queryCourse = '(' . substr($queryCourse, -0, -4) . ')'; 

        return "article.status = 2 and {$queryCourse} " . $condition;
    }

    /**
     * Função que retorna a condição da consulta dos autores
     * 
     * @param $authorID
     * @param $condition
     */
    public static function getConditionAuthor($authorID, $condition): string
    {
        if (strlen($condition) > 5) {
            $condition = " and {$condition}";
        }
        return "({$authorID} in (select user from article_authors where article_authors.article = article.id)) " . $condition;
    }   

    /**
     * Função que pega todas as informações do artigo
     * 
     * @param $condition
     */
    public static function getArticle($condition): object
    {
        $article = new ArticleModel();       
        $article->select(['article.*', 'article_status.name as status', 'event.name as event_name'])                                                
                ->innerjoin('article_status on article.status = article_status.id')
                ->innerjoin('event on article.event = event.id')
                ->where($condition)     
                ->orderby('id', 'DESC')
                ->get(true);              
    
        $data = [];
        foreach($article->result() as $article) {        
            $articleID = $article['id'];
            
            $comments = new ArticleCommentsModel();
            $comments->select(['user.id as user_id', 'user.name as user_name', 'comment', 'article_comments.created_at'])
                     ->innerjoin('user on user.id = article_comments.user')
                     ->where("article_comments.article = {$articleID}")
                     ->get(true);

            $authors = new ArticleAuthorsModel();
            $authors->select(['user.id', 'user.name', 'user.cpf', 'user.email', 'user.ra', 'article_authors.course', 'course.name as course_name', 'article_authors.schooling', 'schooling.name as schooling_name'])
                ->innerjoin('user on user.id = article_authors.user')
                ->leftjoin('course on course.id = article_authors.course')
                ->leftjoin('schooling on schooling.id = article_authors.schooling')
                ->where("article = {$articleID}")
                ->orderby('article_authors.id', 'ASC')
                ->get(true);

            $externalParticipants = new ArticleExternalParticipantsModel();
            $externalParticipants->select(['id', 'name', 'email', 'type'])
                ->where("article = {$articleID}")
                ->orderby('id', 'ASC')
                ->get(true);
                    
            $advisors = new ArticleAdvisorsModel();
            $advisors->select(['user.id', 'user.name', 'user.cpf', 'user.email', 'user.ra', 'article_advisors.is_coadvisor'])
                     ->innerjoin('user on user.id = article_advisors.user')                     
                     ->where("article = {$articleID}")
                     ->orderby('article_advisors.id', 'ASC')
                     ->get(true);            
            
            $references = new ArticleReferencesModel();
            $references->select(['id', 'reference'])
                       ->where("article = {$articleID}")
                       ->orderby('reference', 'ASC')
                       ->get(true);

            array_push($data, array_merge($article, [
                'authors' => $authors->result(),
                'external_participants' => $externalParticipants->result(),
                'advisors' => $advisors->result(),
                'comments' => $comments->result(),
                'references' => $references->result(),
            ]));                                 
        }
        return (object) $data;
    }

    /**
     * Função que retorna o status com base no ID
     * 
     * @param $id
     */
    public static function getStatusFormated($id): string
    {
        $status = '';
        switch($id) {
            case 1:
                $status = 'Submissão';
                break;
            case 2: 
                $status = 'Revisão';
                break;
            case 3: 
                $status = 'Correção';
                break;
            case 4: 
                $status = 'Aprovado';
                break;                
            case 5: 
                $status = 'Finalizado';
                break;
        }
        return $status;
    }
}
