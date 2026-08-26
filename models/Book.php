<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class Book extends ActiveRecord
{
    public $imageFile;

    public static function tableName(): string
    {
        return 'book';
    }

    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['year'], 'integer', 'min' => 0, 'max' => date('Y')],
            [['description'], 'string'],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['cover_image'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /*
     * Получение списка авторов
     */
    public function getAuthorNames(): string
    {
        return implode(', ', array_column($this->authors, 'full_name'));
    }

    /*
     * Загрузка фото главной страницы
     */
    public function upload(): bool
    {
        if ($this->validate(['imageFile'])) {
            if ($this->imageFile instanceof UploadedFile) {
                // Удаляем старую картинку если есть
                if ($this->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $this->cover_image)) {
                    unlink(Yii::getAlias('@webroot/uploads/covers/') . $this->cover_image);
                }

                $fileName = 'cover_' . time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $this->imageFile->extension;
                $uploadPath = Yii::getAlias('@webroot/uploads/covers/');
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($this->imageFile->saveAs($uploadPath . $fileName)) {
                    $this->cover_image = $fileName;
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Получение ссылки на фото главной страницы
     */
    public function getCoverUrl(): ?string
    {
        if ($this->cover_image) {
            return Yii::getAlias('@web/uploads/covers/') . $this->cover_image;
        }
        return null;
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            // Удаляем связи
            BookAuthor::deleteAll(['book_id' => $this->id]);
            // Удаляем картинку
            if ($this->cover_image && file_exists(Yii::getAlias('@webroot/uploads/covers/') . $this->cover_image)) {
                unlink(Yii::getAlias('@webroot/uploads/covers/') . $this->cover_image);
            }
            return true;
        }
        return false;
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'imageFile' => 'Фото главной страницы',
        ];
    }
}
