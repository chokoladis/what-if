<?php

namespace App\Jobs;

use App\Exceptions\FileValidationException;
use App\Models\TempFile;
use App\Models\User;
use App\Services\AI\Gemini\AvatarValidatorService;
use App\Services\FileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UserAvatarVerify implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user,
        protected TempFile $photo,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        todo отдельный контейнер с воркером ?
        [$isLegal, $error] = (new AvatarValidatorService)->isContentFileLegal($this->photo);

        if (!$isLegal) {
            throw new FileValidationException($error);
        } else {
            FileService::saveFromQueue($this->photo, 'users');
        }
    }
}
