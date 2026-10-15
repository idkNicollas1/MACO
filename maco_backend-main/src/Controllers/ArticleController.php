<?php

namespace MacoBackend\Controllers;

use Dompdf\Exception\ImageException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MacoBackend\Helpers\ArticleHelper;
use MacoBackend\Helpers\LogHelper;
use MacoBackend\Helpers\UserHelper;
use MacoBackend\Helpers\SystemHelper;
use MacoBackend\Models\ArticleAdvisorsModel;
use MacoBackend\Models\ArticleAuthorsModel;
use MacoBackend\Models\ArticleExternalParticipantsModel;
use MacoBackend\Models\ArticleCommentsModel;
use MacoBackend\Models\ArticleModel;
use MacoBackend\Models\ArticleReferencesModel;
use MacoBackend\Models\RoleModel;
use MacoBackend\Models\UserCourseModel;
use MacoBackend\Models\UserModel;
use MacoBackend\Services\DocxService;
use MacoBackend\Services\EmailService;

final class ArticleController
{
    /**
     * Realiza a listagem dos artigos
     *
     * @return Response
     */
    public function listArticles(Request $request, Response $response, $args): Response
    {
        $params = (object)$request->getQueryParams();
        $condition = ArticleHelper::conditionByList($params);
        switch ($args['role']) {
            case 'admin':
                if (UserHelper::checkUserRole($request, RoleModel::ADMIN)) {
                    return ResponseController::message($response, 'error', 'Operation denied! User is not authorized');
                }
                break;
            case 'advisor':
                if (UserHelper::checkUserRole($request, RoleModel::ADVISOR)) {
                    return ResponseController::message($response, 'error', 'Operation denied! User is not authorized');
                }
                $condition = ArticleHelper::getConditionAdvisor(UserHelper::getUserInToken($request, 'id'), $condition);
                break;
            case 'author':
                if (UserHelper::checkUserRole($request, RoleModel::AUTHOR)) {
                    return ResponseController::message($response, 'error', 'Operation denied! User is not authorized');
                }
                $condition = ArticleHelper::getConditionAuthor(UserHelper::getUserInToken($request, 'id'), $condition);
                break;
            case 'external':
                if (UserHelper::checkUserRole($request, RoleModel::EXTERNAL)) {
                    return ResponseController::message($response, 'error', 'Operation denied! User is not authorized');
                }
                $condition = ArticleHelper::getConditionAuthor(UserHelper::getUserInToken($request, 'id'), $condition);
                break;
            default:
                throw new Exception('User is not authorized');
                break;
        }

        $data = ArticleHelper::getArticle($condition);

        return ResponseController::data($response, (object)$data);
    }

    /**
     * Realiza a inserção de um artigo
     *
     * @return Response
     */
    public function addArticle(Request $request, Response $response, $args): Response
    {
        $user_id = UserHelper::getUserInToken($request, 'id');

        $user = new UserModel();
        $user->select(['role', 'schooling'])
            ->where("id = {$user_id}")
            ->get();
        $user_role = $user->result()->role;
        $user_schooling = $user->result()->schooling;

        if (!in_array($user_role, [RoleModel::AUTHOR, RoleModel::EXTERNAL])) {
            return ResponseController::message($response, 'error', 'Operation denied! User is not authorized to create articles');
        }

        $parsedBody = $request->getParsedBody();
        $event = $parsedBody['event'] ?? null;
        $title = $parsedBody['title'] ?? null;
        $keywords = $parsedBody['keywords'] ?? null;
        $summary = $parsedBody['summary'] ?? null;

        if (empty($event) || empty($title) || empty($keywords) || empty($summary)) {
            return ResponseController::message($response, 'error', 'Missing required field: event, title, keywords or summary');
        }

        if ($user_role == RoleModel::EXTERNAL && empty($user_schooling)) {
            return ResponseController::message($response, 'error', 'External user is missing required schooling information');
        }

        if ($user_role == RoleModel::AUTHOR) {
            $userCourse = new UserCourseModel();
            $userCourse->select(['course'])
                ->where("user = {$user_id}")
                ->orderby('id', 'DESC')
                ->limit(1)
                ->get();
        }

        $article = new ArticleModel();
        $article->data([
            'event' => $event,
            'title' => $title,
            'keywords' => $keywords,
            'summary' => $summary,
            'status' => 1,
            'origin' => $user_role == RoleModel::EXTERNAL ? 'external' : 'internal',
        ])->insert();

        if ($article->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to create article: ' . $article->result()->message);
        }

        $data = [
            'article' => $article->getReturnID(),
            'user' => $user_id,
        ];

        if ($user_role == RoleModel::AUTHOR) {
            $data += [
                'course' => $userCourse->getCourse(),
                'schooling' => null,
            ];
        } else {
            $data += [
                'course' => null,
                'schooling' => $user_schooling,
            ];
        }

        $author = new ArticleAuthorsModel();
        $author->data($data)->insert();

        if ($author->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to link author to article: ' . $author->result()->message);
        }

        LogHelper::log('Artigo', 'Adição de artigo', $request);

        return ResponseController::data($response, (object)[
            'status' => 'success',
            'message' => 'Article inserted successfully',
            'returnid' => $article->getReturnID(),
        ]);
    }

    /**
     * Realiza a edição do status do artigo
     *
     * @return Response
     */
    public function editStatus(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'] ?? null;
        $status = $parsedBody['status'] ?? null;

        if (empty($article) || empty($status)) {
            return ResponseController::message($response, 'error', 'Missing required field: article or status');
        }

        $user_id = UserHelper::getUserInToken($request, 'id');
        $user = new UserModel();
        $user->select(['role'])
            ->where("id = {$user_id}")
            ->get();
        $user_role = $user->result()->role;

        switch ($user_role) {
            // Autor interno e externo só podem submeter para revisão (status 2)
            case RoleModel::AUTHOR:
            case RoleModel::EXTERNAL:
                if ($status != 2) {
                    return ResponseController::message($response, 'error', 'Operation denied! Author can only submit for revision');
                }

                $article_details = (array)ArticleHelper::getArticle("article.id = {$article}");
                $userInArticle = false;
                foreach ((array)$article_details[0]['authors'] as $author) {
                    if ($user_id == $author['id']) {
                        $userInArticle = true;
                    }
                }
                if (!$userInArticle) {
                    return ResponseController::message($response, 'error', 'Operation denied! User not in article');
                }
                break;

            // Revisor atualiza para in_correction (3) ou approved (4), nunca em artigo externo
            case RoleModel::ADVISOR:
                if (!in_array($status, [3, 4])) {
                    return ResponseController::message($response, 'error', 'Operation denied!');
                }

                $article_details = (array)ArticleHelper::getArticle("article.id = {$article}");
                if ($article_details[0]['origin'] == 'external') {
                    return ResponseController::message($response, 'error', 'Operation denied! Advisor cannot alter external article status');
                }
                break;

            // Admin: interno pode 4/5/6, externo só 4/6 (sem etapa de correção/revisão)
            case RoleModel::ADMIN:
                $article_details = (array)ArticleHelper::getArticle("article.id = {$article}");
                $article_origin = $article_details[0]['origin'];

                if (!in_array($status, [4, 5, 6])) {
                    return ResponseController::message($response, 'error', 'Operation denied!');
                }
                break;

            default:
                return ResponseController::message($response, 'error', 'Operation denied! User is not authorized');
        }

        $articleStatus = new ArticleModel();
        $articleStatus->data(['status' => $status])
            ->where("id = {$article}")
            ->update();

        if ($articleStatus->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to update status: ' . $articleStatus->result()->message);
        }

        // Notifica os autores sobre a mudança de status
        $articleAuthors = new ArticleAuthorsModel();
        $articleAuthors->select()
            ->where("article_authors.article = {$article}")
            ->get(true);

        $articleInfo = new ArticleModel();
        $articleInfo->select()
            ->where("article.id = {$article}")
            ->get();

        foreach ($articleAuthors->result() as $author) {
            $userInfo = new UserModel();
            $userInfo->select()
                ->where("user.id = " . $author['user'])
                ->get();

            $html = file_get_contents('./layout-email/status_update.html');
            $html = str_replace('{{host}}', SystemHelper::getFrontendHost(), $html);
            $html = str_replace('{{user_name}}', $userInfo->getName(), $html);
            $html = str_replace('{{article}}', $articleInfo->getTitle(), $html);
            $html = str_replace('{{status}}', ArticleHelper::getStatusFormated($status), $html);

            $email = EmailService::sendMail(
                'Alteração de status do seu artigo - MACO',
                $html,
                $userInfo->getEmail(),
                $userInfo->getName(),
            );
        }

        LogHelper::log('Artigo', 'Edição de status de artigo', $request);

        return ResponseController::message($response, $articleStatus->result()->status, 'Article status update successfully');
    }

    /**
     * Realiza a edição do titulo e resumo do artigo
     *
     * @return Response
     */
    public function editArticle(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'];
        $title = $parsedBody['title'];
        $summary = $parsedBody['summary'];

        if (empty($article) || empty($title) || empty($summary)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleEdit = new ArticleModel();
        $articleEdit->data(['title' => $title, 'summary' => $summary])
            ->where("id = {$article}")
            ->update();

        LogHelper::log('Artigo', 'Edição do artigo', $request);

        if ($articleEdit->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleEdit->result()->message);
        }
        return ResponseController::message($response, $articleEdit->result()->status, 'Article data update successfully');
    }

    /**
     * Realiza a edição da palavras chave do artigo
     *
     * @return Response
     */
    public function editKeywords(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'];
        $keywords =  $parsedBody['keywords'];

        if (empty($article)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleKeys = new ArticleModel();
        $articleKeys->data(['keywords' => $keywords])
            ->where("id = {$article}")
            ->update();

        LogHelper::log('Artigo', 'Edição de palavras chaves', $request);

        if ($articleKeys->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleKeys->result()->message);
        }
        return ResponseController::message($response, $articleKeys->result()->status, 'Article keywords update successfully');
    }

    /**
     * Realiza a exclusão de um artigo
     *
     * @return Response
     */
    public function delArticle(Request $request, Response $response, $args): Response
    {
        $articleID = $args['articleid'];

        if (empty($articleID)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $authorDel = new ArticleAuthorsModel();
        $authorDel->where("article = {$articleID}")
            ->delete();

        $advisorDel = new ArticleAdvisorsModel();
        $advisorDel->where("article = {$articleID}")
            ->delete();

        $referenceDel = new ArticleReferencesModel();
        $referenceDel->where("article = {$articleID}")
            ->delete();

        $articleDel = new ArticleModel();
        $articleDel->where("id = {$articleID} and status = 1")
            ->delete();

        LogHelper::log('Artigo', 'Exclusão de artigo', $request);

        if ($authorDel->getStatus() != 'success' && $advisorDel->getStatus() != 'success' && $referenceDel->getStatus() != 'success' && $articleDel->getStatus() != 'success') {
            return ResponseController::message($response, 'error', $articleDel->result()->message);
        }
        return ResponseController::message($response, $articleDel->result()->status, 'Article delete successfully');
    }

    /**
     * Realiza a inserção de autores no artigo
     *
     * @return Response
     */
    public function addAuthor(Request $request, Response $response, $args): Response
    {
        $user_id = UserHelper::getUserInToken($request, 'id');

        $user = new UserModel();
        $user->select(['role'])
            ->where("id = {$user_id}")
            ->get();
        $user_role = $user->result()->role;

        // Externo não pode vincular usuários reais como autor - deve usar o endpoint de participantes
        if ($user_role == RoleModel::EXTERNAL) {
            return ResponseController::message($response, 'error', 'Operation denied');
        }

        $parsedBody = $request->getParsedBody();
        $article = $parsedBody['article'] ?? null;
        $author = $parsedBody['author'] ?? null;

        if (empty($article) || empty($author)) {
            return ResponseController::message($response, 'error', 'Missing required field');
        }

        $authorCourse = new UserCourseModel();
        $authorCourse->select(['course'])
            ->where("user = {$author}")
            ->orderby('id', 'DESC')
            ->limit(1)
            ->get();

        $articleAuthor = new ArticleAuthorsModel();
        $articleAuthor->data([
            'user' => $author,
            'article' => $article,
            'course' => $authorCourse->getCourse(),
        ])->insert();

        if ($articleAuthor->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to add author: ' . $articleAuthor->result()->message);
        }

        LogHelper::log('Artigo', 'Adição de autor', $request);

        return ResponseController::message($response, 'success', 'Author inserted successfully');
    }

    /**
     * Realiza a exclusão de um autor do artigo
     *
     * @return Response
     */
    public function delAuthor(Request $request, Response $response, $args): Response
    {
        $articleID = $args['articleid'];
        $userID = $args['authorid'];

        if (empty($articleID) || empty($userID)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $authorDel = new ArticleAuthorsModel();
        $authorDel->where("article = {$articleID} and user = {$userID}")
            ->delete();

        LogHelper::log('Artigo', 'Remoção de autor', $request);

        if ($authorDel->result()->status != 'success') {
            return ResponseController::message($response, 'error', $authorDel->result()->message);
        }
        return ResponseController::message($response, $authorDel->result()->status, 'Author delete successfully');
    }

    /**
     * Realiza a inserção de revisor no artigo
     *
     * @return Response
     */
    public function addAdvisor(Request $request, Response $response, $args): Response
    {
        $user_id = UserHelper::getUserInToken($request, 'id');

        $user = new UserModel();
        $user->select(['role'])
            ->where("id = {$user_id}")
            ->get();
        $user_role = $user->result()->role;

        // Externo não pode vincular revisor real - deve usar o endpoint de participantes
        if ($user_role == RoleModel::EXTERNAL) {
            return ResponseController::message($response, 'error', 'Operation denied');
        }

        $parsedBody = $request->getParsedBody();
        $article = $parsedBody['article'] ?? null;
        $advisor = $parsedBody['advisor'] ?? null;
        $coadvisor = $parsedBody['coadvisor'] ?? null;

        if (empty($article) || empty($advisor)) {
            return ResponseController::message($response, 'error', 'Missing required field');
        }

        $advisorAuthor = new ArticleAdvisorsModel();
        $advisorAuthor->data([
            'user' => $advisor,
            'article' => $article,
            'is_coadvisor' => $coadvisor,
        ])->insert();

        if ($advisorAuthor->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to add advisor: ' . $advisorAuthor->result()->message);
        }

        LogHelper::log('Artigo', 'Adição de revisor', $request);

        return ResponseController::message($response, 'success', 'Advisor inserted successfully');
    }

    /**
     * Realiza a exclusão de um revisor do artigo
     *
     * @return Response
     */
    public function delAdvisor(Request $request, Response $response, $args): Response
    {
        $articleID = $args['articleid'];
        $advisorID = $args['advisorid'];

        if (empty($articleID) || empty($advisorID)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $advisorDel = new ArticleAdvisorsModel();
        $advisorDel->where("article = {$articleID} and user = {$advisorID}")
            ->delete();

        LogHelper::log('Artigo', 'Remoção de revisor', $request);

        if ($advisorDel->result()->status != 'success') {
            return ResponseController::message($response, 'error', $advisorDel->result()->message);
        }
        return ResponseController::message($response, $advisorDel->result()->status, 'Advisor delete successfully');
    }

    /**
     * Realiza a inserção de participante externo no artigo (orientador, co-orientador ou autor)
     *
     * @return Response
     */
    public function addExternalParticipant(Request $request, Response $response, $args): Response
    {
        $user_id = UserHelper::getUserInToken($request, 'id');

        $user = new UserModel();
        $user->select(['role'])
            ->where("id = {$user_id}")
            ->get();
        $user_role = $user->result()->role;

        if ($user_role != RoleModel::EXTERNAL) {
            return ResponseController::message($response, 'error', 'Only external users can add text-based participants');
        }

        $parsedBody = $request->getParsedBody();
        $article = $parsedBody['article'] ?? null;
        $name = $parsedBody['name'] ?? null;
        $email = $parsedBody['email'] ?? null;
        $type = $parsedBody['type'] ?? null;

        if (empty($article) || empty($name) || empty($email) || empty($type)) {
            return ResponseController::message($response, 'error', 'Missing required field: article, name or type');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ResponseController::message($response, 'error', 'Invalid email format');
        }

        if (!in_array($type, ['advisor', 'co_advisor', 'author'])) {
            return ResponseController::message($response, 'error', 'Invalid type: must be advisor, co_advisor or author');
        }

        // Garante que o artigo pertence ao usuário logado
        $ownership = new ArticleAuthorsModel();
        $ownership->select(['id'])
            ->where("article = {$article}")
            ->where("user = {$user_id}")
            ->get();

        if (empty($ownership->result())) {
            return ResponseController::message($response, 'error', 'Operation denied! Article does not belong to this user');
        }

        // Limite de 1 advisor e 1 co_advisor por artigo
        if (in_array($type, ['advisor', 'co_advisor'])) {
            $existing = new ArticleExternalParticipantsModel();
            $existing->select(['id'])
                ->where("article = {$article}")
                ->where("type = '{$type}'")
                ->get(true);

            $existingResult = $existing->result();
            $existingCount = is_array($existingResult)
                ? count($existingResult)
                : (is_object($existingResult) && count((array) $existingResult) > 0 ? 1 : 0);

            if ($existingCount > 0) {
                $label = $type == 'advisor' ? 'advisor' : 'co-advisor';
                return ResponseController::message($response, 'error', "Article already has an assigned {$label}");
            }
        }

        // Limite de 10 autores por artigo
        if ($type == 'author') {
            $existingAuthors = new ArticleExternalParticipantsModel();
            $existingAuthors->select(['id'])
                ->where("article = {$article}")
                ->where("type = 'author'")
                ->get(true);

            $authorsResult = $existingAuthors->result();
            $authorsCount = is_array($authorsResult) ? count($authorsResult) : (empty($authorsResult) ? 0 : 1);

            if ($authorsCount >= 10) {
                return ResponseController::message($response, 'error', 'Article already has the maximum of 10 authors');
            }
        }

        $participant = new ArticleExternalParticipantsModel();
        $participant->data([
            'article' => $article,
            'name' => $name,
            'email' => $email,
            'type' => $type,
        ])->insert();

        if ($participant->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to add participant: ' . $participant->result()->message);
        }

        LogHelper::log('Artigo', 'Adição de participante externo', $request);

        return ResponseController::message($response, 'success', 'Participant inserted successfully');
    }

    /**
     * Remove um participante externo do artigo
     *
     * @return Response
     */
    public function delExternalParticipant(Request $request, Response $response, $args): Response
    {
        $user_id = UserHelper::getUserInToken($request, 'id');
        $article = $args['articleid'];
        $participant_id = $args['participantid'];

        $user = new UserModel();
        $user->select(['role'])
            ->where("id = {$user_id}")
            ->get();

        if ($user->result()->role != RoleModel::EXTERNAL) {
            return ResponseController::message($response, 'error', 'Only external users can remove text-based participants');
        }

        $ownership = new ArticleAuthorsModel();
        $ownership->select(['id'])
            ->where("article = {$article}")
            ->where("user = {$user_id}")
            ->get();

        if (empty($ownership->result())) {
            return ResponseController::message($response, 'error', 'Operation denied! Article does not belong to this user');
        }

        $participant = new ArticleExternalParticipantsModel();
        $participant->where("id = {$participant_id}")
            ->where("article = {$article}")
            ->delete();

        if ($participant->result()->status != 'success') {
            return ResponseController::message($response, 'error', 'Failed to remove participant: ' . $participant->result()->message);
        }

        LogHelper::log('Artigo', 'Remoção de participante externo', $request);

        return ResponseController::message($response, 'success', 'Participant removed successfully');
    }

    /**
     * Realiza a inserção de um comentário de revisão no artigo
     *
     * @return Response
     */
    public function addComment(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $user = UserHelper::getUserInToken($request, 'id');
        $article = $parsedBody['article'];
        $comment = $parsedBody['comment'];

        if (empty($article) || empty($comment)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleComment = new ArticleCommentsModel();
        $articleComment->data([
            'user' => $user,
            'article' => $article,
            'comment' => $comment,
        ])->insert();

        LogHelper::log('Artigo', 'Adição de comentário', $request);

        if ($articleComment->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleComment->result()->debug);
        }
        return ResponseController::message($response, $articleComment->result()->status, 'Article comment inserted successfully');
    }

    /**
     * Realiza a inserção de um comentário de revisão no artigo
     *
     * @return Response
     */
    public function addReference(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'];
        $reference = $parsedBody['reference'];

        if (empty($article) || empty($reference)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleRef = new ArticleReferencesModel();
        $articleRef->data([
            'article' => $article,
            'reference' => $reference,
        ])->insert();

        LogHelper::log('Artigo', 'Adição de referências', $request);

        if ($articleRef->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleRef->result()->debug);
        }
        return ResponseController::message($response, $articleRef->result()->status, 'Article reference inserted successfully');
    }

    /**
     * Realiza a exclusão de uma referência de revisão no artigo
     *
     * @return Response
     */
    public function delReference(Request $request, Response $response, $args): Response
    {
        $article = $args['articleid'];
        $refID = $args['refid'];

        if (empty($article) || empty($refID)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleRef = new ArticleReferencesModel();
        $articleRef->where("article = {$article} and id = {$refID}")
            ->delete();

        LogHelper::log('Artigo', 'Remoção de referências', $request);

        if ($articleRef->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleRef->result()->debug);
        }
        return ResponseController::message($response, $articleRef->result()->status, 'Article reference deleted successfully');
    }


    /**
     * Realiza a edição das referências bibliograficas de um artigo
     *
     * @return Response
     */
    public function editReference(Request $request, Response $response, $args): Response
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'];
        $refID = $parsedBody['ref_id'];
        $refStr = $parsedBody['ref_str'];

        if (empty($article)) {
            return ResponseController::message($response, 'error', 'Missing information');
        }

        $articleKeys = new ArticleReferencesModel();
        $articleKeys->data(['reference' => $refStr])
            ->where("id = {$refID} and article = {$article}")
            ->update();

        LogHelper::log('Artigo', 'Edição de referências', $request);

        if ($articleKeys->result()->status != 'success') {
            return ResponseController::message($response, 'error', $articleKeys->result()->message);
        }
        return ResponseController::message($response, $articleKeys->result()->status, 'Article references update successfully');
    }

    /**
     * Realiza o export do artigo
     *
     * @return Response
     */
    public function export(Request $request, Response $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $article = $parsedBody['article'];
        $type = $parsedBody['type'];

        $data = (array)ArticleHelper::getArticle("article.id = {$article}");

        LogHelper::log('Artigo', 'Exportação para ' . strtoupper($type), $request);

        try {
            $docxService = new DocxService();
            switch ($type) {
                case 'docx':
                    $docx = $docxService->exportDocx($data[0]);
                    return ResponseController::data($response, (object)['status' => 'success', 'file' => $docx]);
                    break;
                default:
                    throw new Exception('Type export not informed');
                    break;
            }
        } catch (Exception $e) {
            return ResponseController::message($response, 'error', 'Error generating file');
        }
    }
}