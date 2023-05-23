<?php

include_once '../prelude.php';

class Article
{
    /// @var int
    public $articleId;
    /// @var string
    public $title ;
    /// @var string
    public $content;
    /// @var int
    public $readTime;
    /// @var int
    public $writtenBy;
    /// @var string
    public $date;
    /// @var string
    public $category;
    /// @var bool
    public $published;

    public function __construct(
        ?int $articleId,
        string $title,
        string $content,
        int $readTime,
        int $writtenBy,
        string $date,
        string $category,
        bool $published
    ) {
        $this->articleId = $articleId;
        $this->title = $title;
        $this->content = $content;
        $this->readTime = $readTime;
        $this->writtenBy = $writtenBy;
        $this->date = $date;
        $this->category = $category;
        $this->published = $published;
    }

    public function is_valid(): bool
    {
        // TODO: validate article
        return true;
    }

    /**
     * @return ?int inserted article id or null
     */
    public function insert_article(): ?int
    {
        if ($this->is_valid()) {
            $date = date('Y-m-d\TH:i:s');
            $db = Database::getInstance();
            return $db->pquery_insert(
                'insert into Articles values (NULL,?,?,?,?,?,?,?)',
                'ssiisss',
                $this->title,
                $this->content,
                $this->readTime,
                $this->writtenBy,
                $date,
                $this->category,
                $this->published
            );
        }
    }

    public function delete_article(): bool
    {
        $db = Database::getInstance();
        return $db->pquery('delete from Articles where articleId = ?', 'i', $this->articleId);
    }

    public function update_article(): bool
    {
        $date = date('Y-m-d\TH:i:s');
        $db = Database::getInstance();

        return $db->pquery(
            'update Articles 
          set title = ?, content = ?, readTime = ?,
              writtenBy = ?, date = ?, category = ?,
              published = ?
          where articleId = ?',
            'ssiisssi',
            $this->title,
            $this->content,
            $this->readTime,
            $this->writtenBy,
            $date,
            $this->category,
            $this->published,
            $this->articleId
        );
    }
}
