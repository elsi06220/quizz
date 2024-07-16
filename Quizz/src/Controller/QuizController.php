<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class QuizController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/quiz', name: 'QuizPage')]
    public function QuizPage(): Response
    {
        $categories = $this->doctrine->getRepository(Categorie::class)->findAll();

        return $this->render('quiz/quiz.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/questions/{id}', name: 'app_questions_by_category')]
    public function questionsByCategory(int $id): Response
    {
        $category = $this->doctrine->getRepository(Categorie::class)->find($id);
        $questions = $category->getQuestions();

        return $this->render('quiz/questions_by_category.html.twig', [
            'category' => $category,
            'questions' => $questions,
        ]);
    }

    #[Route('/quiz/submit', name: 'quiz_submit', methods: ['POST'])]
    public function submitQuiz(Request $request, EntityManagerInterface $em): Response
    {
        $categoryId = $request->request->get('category');
        $category = $em->getRepository(Categorie::class)->find($categoryId);
        $questions = $category->getQuestions(); 
        $userResponses = $request->request->all();
        $score = 0;
        $results = [];

        foreach ($questions as $question) {
            $questionId = $question->getId();
            $responses = [];

            if (!isset($userResponses[$questionId])) {
                $results[] = [
                    'question' => $question->getQuestion(),
                    'responses' => $responses
                ];
                continue;
            }

            $responseId = $userResponses[$questionId];
            $correctResponse = $em->getRepository(Reponse::class)->findOneBy([
                'question' => $question,
                'reponse_expected' => 1
            ]);

            $givenResponse = $em->getRepository(Reponse::class)->find($responseId);
            $isCorrect = $correctResponse && $correctResponse->getId() == $responseId;

            foreach ($question->getReponses() as $reponse) {
                $responses[] = [
                    'reponse' => $reponse->getReponse(),
                    'isSelected' => $reponse->getId() == $responseId,
                    'isCorrect' => $isCorrect && $reponse->getId() == $responseId,
                    'isExpected' => $reponse->getId() == $correctResponse->getId()
                ];
            }

            if ($isCorrect) {
                $score++;
            }

            $results[] = [
                'question' => $question->getQuestion(),
                'responses' => $responses
            ];
        }

        $totalQuestions = count($questions);

        return $this->render('quiz/result.html.twig', [
            'results' => $results,
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'category' => $category
        ]);
    }
}
    