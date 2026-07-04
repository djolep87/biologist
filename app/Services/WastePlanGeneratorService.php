<?php

namespace App\Services;

use App\Models\User;
use App\Models\WastePlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;

class WastePlanGeneratorService
{
    public function __construct(
        private WastePlanPromptBuilder $promptBuilder,
        private WastePlanContentParser $contentParser
    ) {}

    /**
     * @param  array<string, mixed>  $formData
     * @param  callable|null  $onProgress  fn(int $step): void
     * @return array{plan: WastePlan, word_count: int, missing_pages: array<int, int>, needs_regeneration: bool}
     */
    public function generate(array $formData, User $user, ?callable $onProgress = null): array
    {
        $this->ensureRateLimit($user);

        $formData = $this->normalizeFormData($formData);
        $apiKey = $this->apiKey();

        $this->reportProgress($onProgress, 1);

        $userPrompt = $this->promptBuilder->buildUserPrompt($formData);
        $messages = [
            ['role' => 'system', 'content' => WastePlanPromptBuilder::SYSTEM_PROMPT],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        $this->reportProgress($onProgress, 2);

        $content = $this->callOpenAi($apiKey, $messages);

        $wordCount = $this->contentParser->countWords($content);
        $missingPages = $this->contentParser->getMissingPages($content);

        if ($wordCount < WastePlanContentParser::MIN_WORD_COUNT) {
            $messages[] = ['role' => 'assistant', 'content' => $content];
            $messages[] = [
                'role' => 'user',
                'content' => $this->promptBuilder->buildContinuationPrompt($content, $wordCount, $missingPages),
            ];

            $continuation = $this->callOpenAi($apiKey, $messages);
            $content = trim($content)."\n\n".trim($continuation);
            $wordCount = $this->contentParser->countWords($content);
            $missingPages = $this->contentParser->getMissingPages($content);
        }

        $this->reportProgress($onProgress, 3);

        RateLimiter::hit($this->rateLimitKey($user), 3600);

        $plan = WastePlan::create([
            'company_name' => $formData['naziv_firme'],
            'created_by' => $user->id,
            'form_data' => $formData,
            'plan_content' => trim($content),
            'generated_at' => now(),
        ]);

        $this->reportProgress($onProgress, 4);

        return [
            'plan' => $plan,
            'word_count' => $wordCount,
            'missing_pages' => $missingPages,
            'needs_regeneration' => $missingPages !== [] || $wordCount < WastePlanContentParser::MIN_WORD_COUNT,
        ];
    }

    public function remainingAttempts(User $user): int
    {
        return RateLimiter::remaining($this->rateLimitKey($user), 5);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function callOpenAi(string $apiKey, array $messages): string
    {
        $response = Http::withToken($apiKey)
            ->timeout((int) config('services.openai.timeout', 180))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o'),
                'messages' => $messages,
                'max_tokens' => 8000,
                'temperature' => 0.2,
                'presence_penalty' => 0.1,
                'frequency_penalty' => 0.2,
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message') ?? $response->body();

            throw new RuntimeException('OpenAI greška: '.$message);
        }

        $content = $response->json('choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('OpenAI nije vratio sadržaj plana. Pokušajte ponovo.');
        }

        return trim($content);
    }

    private function apiKey(): string
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('OpenAI API ključ nije podešen. Dodajte OPENAI_API_KEY u .env fajl.');
        }

        return $apiKey;
    }

    private function ensureRateLimit(User $user): void
    {
        $key = $this->rateLimitKey($user);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw new RuntimeException(
                'Dostigli ste limit od 5 generisanja po satu. Pokušajte ponovo za '.ceil($seconds / 60).' minuta.'
            );
        }
    }

    private function rateLimitKey(User $user): string
    {
        return 'waste-plan:'.$user->id;
    }

    private function reportProgress(?callable $onProgress, int $step): void
    {
        if ($onProgress) {
            $onProgress($step);
        }
    }

    /**
     * @param  array<string, mixed>  $formData
     * @return array<string, mixed>
     */
    private function normalizeFormData(array $formData): array
    {
        $optional = [
            'kontakt_eko', 'telefon_email', 'broj_zaposlenih', 'vrste_otpada',
            'indeksni_brojevi', 'procenjene_kolicine', 'nacin_postupanja', 'ugovori_operateri',
            'lokacija_pogon', 'povrsina_objekta', 'opis_skladista', 'ciljevi_smanjenja', 'posebne_napomene',
        ];

        foreach ($optional as $key) {
            if (empty($formData[$key])) {
                $formData[$key] = '—';
            }
        }

        if (empty($formData['opis_skladista']) && empty($formData['ima_skladiste'])) {
            $formData['opis_skladista'] = '—';
        }

        return $formData;
    }
}
