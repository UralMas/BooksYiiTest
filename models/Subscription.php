<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'subscription';
    }

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^8\d{10}$/', 'message' => 'Телефон должен содержать только цифры, длиной 11 символов'],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone']],
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /*
     * Сохранение подписки на автора
     */
    public function saveForAuthor(Author $author)
    {
        $this->author_id = $author->id;

        return $this->save();
    }

    public function attributeLabels(): array
    {
        return [
            'phone' => 'Телефон',
        ];
    }
}