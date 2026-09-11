<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Author extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'author';
    }

    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
            [['full_name'], 'unique'],
        ];
    }

    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('book_author', ['author_id' => 'id']);
    }

    public function getSubscriptions(): ActiveQuery
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    /*
     * Получение отсортированного списка книг автора
     */
    public function getBooksSorted(): array
    {
        return $this->getBooks()
            ->orderBy(['year' => SORT_DESC, 'title' => SORT_ASC])
            ->all();
    }

    /*
     * Получение отсортированного списка авторов
     */
    public static function getListSorted(): array
    {
        return self::find()->orderBy(['full_name' => SORT_ASC])->all();
    }

    /*
     * Получение списка ТОП-10 авторов за год
     */
    public static function getTopAuthors(int $year): array
    {
        return static::find()
            ->select([
                'author.id',
                'author.full_name',
                'COUNT(book_author.book_id) as books_count'
            ])
            ->join('INNER JOIN', 'book_author', 'author.id = book_author.author_id')
            ->join('INNER JOIN', 'book', 'book_author.book_id = book.id')
            ->where(['book.year' => $year])
            ->groupBy('author.id, author.full_name')
            ->orderBy(['books_count' => SORT_DESC, 'full_name' => SORT_ASC])
            ->limit(10)
            ->asArray()
            ->all();
    }

    /*
     * Получение списка годов, в которые выходили книги
     */
    public static function getAvailableYears(): array
    {
        return Book::find()
            ->select(['year'])
            ->distinct()
            ->where(['not', ['year' => null]])
            ->orderBy(['year' => SORT_DESC])
            ->column();
    }

    /*
     * Получение количества книг
     */
    public function getBooksCount(): int
    {
        return $this->getBooks()->count();
    }

    public static function getAllSorted(): array
    {
        return static::find()->orderBy(['full_name' => SORT_ASC])->all();
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            // Удаляем связи
            BookAuthor::deleteAll(['author_id' => $this->id]);
            // Удаляем подписки
            Subscription::deleteAll(['author_id' => $this->id]);
            return true;
        }
        return false;
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО автора',
        ];
    }
}
