<?php

namespace App\Enums;

enum NotificationType:string
{
    case QUESTION_PUBLISHED = 'question_published';
    case QUESTION_COMMENTED = 'question_commented';
    case QUESTION_LIKED = 'question_liked';

    case RESPONDED_TO_COMMENT = 'responded_to_comment';
    case COMMENT_LIKED = 'comment_liked';
}
