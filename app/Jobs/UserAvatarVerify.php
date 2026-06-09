<?php

namespace App\Jobs;

use App\Exceptions\FileValidationException;
use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AI\ValidatorAvatarContract;
use App\Models\TempFile;
use App\Models\User;
use App\Notifications\User\AvatarValidatedNotification;
use App\Notifications\User\TemporaryErrorNotification;
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
        protected ValidatorAvatarContract $AIAvatarValidator,
        protected User             $user,
        protected TempFile         $photo,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->checkAvatar())
            return;

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

    private function checkAvatar(): ?true
    {
        try {
            $isLegal = $this->AIAvatarValidator->isContentFileLegal($this->photo);
        } catch (AIWorkException $e) {
            $this->user->notify(
                new TemporaryErrorNotification($this->photo->getShortOriginalName())
            );
            $this->photo->delete(); // todo retry in rabbitMQ or redis
            return null;
        } catch (FileValidationException $error) {
            $this->user->notify(
                new AvatarValidatedNotification($this->photo, false)
            );
            $this->photo->delete();
            throw $error;
        }

        $this->user->notify(new AvatarValidatedNotification($this->photo, $isLegal));

        return $isLegal ?? null;
    }
}
