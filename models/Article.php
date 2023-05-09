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

    private function __construct(
        int $articleId,
        string $title,
        string $content,
        int $readTime,
        int $writtenBy,
        string $date
    ) {
        $this->articleId = $articleId;
        $this->title = $title;
        $this->content = $content;
        $this->readTime = $readTime;
        $this->writtenBy = $writtenBy;
        $this->date = $date;
    }

    public function is_valid(): bool
    {
        // TODO: validate article
        return true;
    }

    public function insert_user(): void
    {
        if ($this->is_valid()) {
            // TODO: insert article
        }
    }

    public function delete_user(): void
    {
        // TODO: delete article
    }

    public function update_user(): void
    {
        // TODO: update article
    }
}
