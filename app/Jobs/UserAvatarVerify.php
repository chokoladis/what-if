<?php

namespace App\Jobs;

use App\Exceptions\FileValidationException;
use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AiApiInterface;
use App\Models\TempFile;
use App\Models\User;
use App\Notifications\User\AvatarValidatedNotification;
use App\Notifications\User\TemporaryErrorNotification;
use App\Services\AI\Gemini\AvatarValidatorService;
use App\Services\FileService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAvatarVerify implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected AiApiInterface $AIAvatarValidator, //todo rework
        protected User     $user,
        protected TempFile $photo,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //todo сделать код почище и читабельнее
        try {
            [$isLegal, $error] = (new AvatarValidatorService)->isContentFileLegal($this->photo);
        } catch (AIWorkException $e) {
            $fileName = mb_strlen($this->photo->original_name) > 10 ? mb_substr($this->photo->original_name, 0, 10) . '...' : $this->photo->original_name;
            $this->user->notify(new TemporaryErrorNotification($fileName));
            return;
        }

        $this->user->notify(new AvatarValidatedNotification($this->photo, $isLegal));

        if (!$isLegal) {
            throw new FileValidationException(
                is_null($error) ? 'not legal' : (string)$error
            );
        } else {
            DB::beginTransaction();

            try {
                $photo = FileService::saveFromQueue($this->photo, 'users');
            } catch (Exception $e) {
                Log::emergency('photo not saved in queue', ['user' => $this->user, 'photo' => $this->photo]);
                DB::rollBack();
                return;
            }

            if (!$this->user->update(['photo_id' => $photo->id])) {
                Log::emergency('photo not updated in queue', ['user' => $this->user, 'photo' => $photo]);
                DB::rollBack();
            }

            DB::commit();
        }
    }
}
