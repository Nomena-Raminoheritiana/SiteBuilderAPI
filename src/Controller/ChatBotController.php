<?php

namespace App\Controller;

use App\Repository\ModelRepository;
use App\Services\ChatBot\PromptBuilder;
use App\Services\ChatBot\GeminiClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class ChatBotController extends AbstractController
{
    public function __construct(
       private GeminiClient $gemini, 
       private PromptBuilder $promptBuilder, 
       private ModelRepository $modelRepository
    ) {}
    #[Route(
        path: '/api/chatbot/ask',
        name: 'chatbot_ask',
        methods: ['POST']
    )]
    public function ask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        $chatId = $data['chatId'] ?? '';

        try {
            $model = $this->modelRepository->findOneByChatId($chatId);
            $parent = $model->getParent() ?? $model;
            $subject = $this->promptBuilder->buildSubject($parent);
            $reply = $this->gemini->generateContent($message, $subject);
            return new JsonResponse(['reply' => $reply]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
