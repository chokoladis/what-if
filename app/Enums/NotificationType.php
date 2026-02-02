<?php

namespace App\Enums;

enum NotificationType
{
    const QUESTION_PUBLISHED = 'question_published';
    const QUESTION_COMMENTED = 'question_commented';
    const QUESTION_LIKED = 'question_liked';

    const RESPONDED_TO_COMMENT = 'responded_to_comment';
    const COMMENT_LIKED = 'comment_liked';

}
