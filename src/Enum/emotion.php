<?php
namespace App\Enum;

enum emotion: string
{
    case JOY = 'joy';           // Joie
    case SADNESS = 'sadness';   // Tristesse
    case FEAR = 'fear';         // Peur
    case ANGER = 'anger';       // Colère
    case SURPRISE = 'surprise'; // Surprise
    case DISGUST = 'disgust';   // Dégoût
    case ADMIRATION = 'admiration'; // Admiration
    case INDIFERENCE = 'indifference'; // Indifférence
}
