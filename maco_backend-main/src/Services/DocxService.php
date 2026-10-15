<?php

namespace MacoBackend\Services;
use Exception;
use MacoBackend\Helpers\CourseHelper;
use MacoBackend\Helpers\FileHelper;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class DocxService
{   
    private $path = 'tmp/docx/'; 

    /**
     * Função que exporta para docx
     * 
     * @param $data
     */
    public function exportDocx($data): string
    {
        try {
            $article = (object) $data;

            $phpWord = new PhpWord();
            $phpWord->setDefaultFontSize(12);
            $phpWord->setDefaultFontName('Times New Roman');
            $phpWord->setDefaultParagraphStyle(['lineHeight' => 1.5]);

            $section = $phpWord->addSection([
                'marginLeft'   => 3.0 * 567,
                'marginTop'    => 3.0 * 567,
                'marginRight'  => 2.0 * 567,
                'marginBottom' => 2.0 * 567,
            ]);

            $bold = ['bold' => true, 'noProof' => true];
            $center = ['alignment' => 'center', 'noProof' => true];
            $both = ['alignment' => 'both'];

            // Título
            $section->addText(mb_strtoupper($article->title), $bold, $center);
            $section->addText('');

            // Autores
            foreach ($article->authors as $author) {
                $section->addText($author['name'] . ' - ' . $author['email']);
            }

            if ($article->origin == 'external') {
                $externalAuthors = array_filter((array) $article->external_participants, fn($p) => $p['type'] == 'author');
                foreach ($externalAuthors as $participant) {
                    $section->addText($participant['name']);
                }
            }
            $section->addText('');

            // Orientadores / Co-orientadores
            if ($article->origin == 'external') {
                $externalAdvisors = array_filter((array) $article->external_participants, fn($p) => in_array($p['type'], ['advisor', 'co_advisor']));
                foreach ($externalAdvisors as $participant) {
                    $textrun = $section->addTextRun();
                    $label = $participant['type'] == 'co_advisor' ? 'Co-orientador(a): ' : 'Orientador(a): ';
                    $textrun->addText($label, $bold);
                    $textrun->addText($participant['name']);
                }
            } else {
                foreach ($article->advisors as $advisor) {
                    $textrun = $section->addTextRun();
                    if ($advisor['is_coadvisor']) {
                        $textrun->addText('Co-orientador(a): ', $bold);
                    } else {
                        $textrun->addText('Orientador(a): ', $bold);
                    }
                    $textrun->addText($advisor['name'] . ' - ' . $advisor['email']);
                }
            }

            // Curso ou Escolaridade
            if ($article->origin == 'external') {
                $schoolings = [];
                $textrun3 = $section->addTextRun();
                $textrun3->addText('Escolaridade: ', $bold);
                foreach ($article->authors as $author) {
                    if (!empty($author['schooling_name']) && !in_array($author['schooling_name'], $schoolings)) {
                        array_push($schoolings, $author['schooling_name']);
                    }
                }
                $textrun3->addText(implode(', ', $schoolings));
            } else {
                $courses = [];
                $textrun3 = $section->addTextRun();
                $textrun3->addText('Curso(s): ', $bold);
                foreach ($article->authors as $author) {
                    $course = CourseHelper::getCourseByID($author['course']);
                    if (!in_array($course, $courses)) {
                        array_push($courses, $course);
                    }
                }
                $textrun3->addText(implode(', ', $courses));
            }
            $section->addText('');

            // Resumo
            $section->addText("Resumo:", $bold);
            $section->addText($article->summary, null, $both);
            $section->addText('');

            // Palavras chaves
            $section->addText("Palavras chaves:", $bold);
            $section->addText(trim(str_replace(';', ', ', $article->keywords)));
            $section->addText('');

            // Referências
            $section->addText("Referências:", $bold);
            foreach ($article->references as $reference) {
                $section->addText(htmlspecialchars($reference['reference']), null, $both);
            }

            FileHelper::delFiles($this->path);

            $docx = $this->path . FileHelper::formatFileName($article->title) . '.docx';

            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($docx);

            return $docx;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
