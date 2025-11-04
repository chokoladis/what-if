<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    public $guarded = [];

    const SUBJECTS_RU = [
        'Проблемы с сайтом', 'Предложения и идеи', 'Сотрудничество/реклама', 'Другое'
    ];
    const SUBJECTS_EN = [
        'Problem on site', 'Suggestions and ideas', 'Cooperation/advertising', 'Other'
    ];
    
}
