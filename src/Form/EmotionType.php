<?php
namespace App\Form;

use App\Enum\emotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class EmotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('emotion', EnumType::class, [
            'class' => emotion::class,
            'expanded' => true,
            'multiple' => true,
            'choice_label' => function (emotion $choice) {
                return match ($choice) {
                    emotion::JOY => '😄 Joie',
                    emotion::SADNESS => '😢 Tristesse',
                    emotion::FEAR => '😨 Peur',
                    emotion::ANGER => '😡 Colère',
                    emotion::SURPRISE => '😲 Surprise',
                    emotion::DISGUST => '🤢 Dégoût',
                    emotion::ADMIRATION => '😍 Admiration',
                    emotion::INDIFERENCE => '😐 Indifférence',
                };
            },
        ]);
    }
}
