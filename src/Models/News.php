<?php

namespace Swing\Models;

class News extends Model
{
    protected $table = 'news';

    /**
     * @param int|null $id
     * @return array
     */
    public function select(?int $id = null): array
    {
        $sql = "SELECT * FROM $this->table";

        if ($id) {
            $sql .= " WHERE id = :id";
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        return $statement->fetchAll();
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $text
     * @param string $date
     * @return bool
     */
    public function update(int $id, string $title, string $text, string $date): bool
    {
        $sql = "UPDATE $this->table SET `title` = :title, `text` = :text, `date` = :date WHERE `id` = :id";

        return $this->pdo->prepare($sql)->execute([
            'id' => $id,
            'title' => $title,
            'text' => $text,
            'date' => $date
        ]);
    }

    /**
     * @param string $title
     * @param string $text
     * @return bool
     */
    public function insert(string $title, string $text): bool
    {
        //just random number that makes sense for user id
        $userId = mt_rand(1, 65536);

        $sql = "INSERT INTO $this->table VALUES (NULL,:user_id,:title,:text,NULL)";

        return $this->pdo->prepare($sql)->execute(['user_id' => $userId, 'title' => $title, 'text' => $text]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";

        return $this->pdo->prepare($sql)->execute(['id' => $id]);
    }
}