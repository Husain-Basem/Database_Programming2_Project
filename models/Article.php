<?php
declare(strict_types=1);

include_once '../prelude.php';

class Article
{
    /// @var int
    public $articleId;
    /// @var string
    public $title;
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
    /// @var bool
    public $approved;
    /// @var bool
    public $removed;
    /// @var string
    public $thumbnail;
    /// @var string|null $author Author name
    private $author;

    private static function __set_state(array $state): Article
    {
        $article = new Article(
            (int) $state['articleId'],
            $state['title'],
            $state['content'],
            (int) $state['readTime'],
            (int) $state['writtenBy'],
            $state['date'],
            $state['category'],
            (bool) $state['published'],
            (bool) $state['approved'],
            (bool) $state['removed'],
            $state['thumbnail']
        );
        if (isset($state['author']))
            $article->author = $state['author'];
        return $article;

    }
    public function __construct(
        ?int $articleId,
        string $title,
        string $content,
        int $readTime,
        int $writtenBy,
        string $date,
        string $category,
        bool $published,
        bool $approved,
        bool $removed,
        ?string $thumbnail
    ) {
        $this->articleId = $articleId;
        $this->title = $title;
        $this->content = $content;
        $this->readTime = $readTime;
        $this->writtenBy = $writtenBy;
        $this->date = $date;
        $this->category = $category;
        $this->published = $published;
        $this->approved = $approved;
        $this->removed = $removed;
        $this->thumbnail = $thumbnail;
    }

    public function is_valid(): bool
    {
        // TODO: validate article
        return true;
    }

    /**
     * @return Article|null
     */
    public static function from_articleId(int $articleId): ?Article
    {
        $db = Database::getInstance();
        $result = $db->query("select * from Articles where articleId = $articleId");
        if ($result != null) {
            $row = $result->fetch_assoc();
            return Article::__set_state($row);
        } else
            return null;

    }

    /**
     * @return array<Article>
     */
    public static function get_published_articles(): array
    {
        $db = Database::getInstance();
        $articles = $db->query('select * from Articles where 
                                   published = 1
                                   order by date desc')->fetch_all(MYSQLI_ASSOC);
        return array_map(function ($row) {
            return Article::__set_state($row);
        }, $articles);
    }

    /**
     * @return array<Article>
     */
    public static function search_articles(string $search): array
    {
        $db = Database::getInstance();
        $articles = $db->query('call `SearchArticles`(\'' . $db->escape($search) . '\');')->fetch_all(MYSQLI_ASSOC);
        return array_map(function ($row) {
            return Article::__set_state($row);
        }, $articles);
    }



    /**
     * @return array<Article>
     */
    public static function get_author_articles(int $authorId, bool $published = false): array
    {
        $db = Database::getInstance();
        $articles = $db->query('select * from Articles where writtenBy = ' . $authorId . ' 
                                   and published = ' . (int) $published . '
                                   order by date desc')->fetch_all(MYSQLI_ASSOC);
        return array_map(function ($row) {
            return Article::__set_state($row);
        }, $articles);
    }

    /**
     * @return ?int inserted article id or null
     */
    public function insert_article(): ?int
    {
        if ($this->is_valid()) {
            $date = date('Y-m-d\TH:i:s');
            $db = Database::getInstance();
            $id = $db->pquery_insert(
                'insert into Articles values (NULL,?,?,?,?,?,?,?,?)',
                'ssiissiiis',
                $this->title,
                $this->content,
                $this->readTime,
                $this->writtenBy,
                $date,
                $this->category,
                (int) $this->published,
                (int) $this->approved,
                (int) $this->removed,
                $this->thumbnail
            );
            $this->articleId = $id;
            return $id;
        }
    }

    public static function delete_article(int $articleId): bool
    {
        $db = Database::getInstance();
        return $db->pquery('delete from Articles where articleId = ?', 'i', $articleId);
    }

    public function update_article(): bool
    {
        $date = date('Y-m-d\TH:i:s');
        $db = Database::getInstance();

        return $db->pquery(
            'update Articles 
          set title = ?, content = ?, readTime = ?,
              writtenBy = ?, date = ?, category = ?,
              published = ?, approved = ?,
              removed = ?, thumbnail = ?
          where articleId = ?',
            'ssiissiiisi',
            $this->title,
            $this->content,
            $this->readTime,
            $this->writtenBy,
            $date,
            $this->category,
            $this->published,
            $this->approved,
            $this->removed,
            $this->thumbnail,
            $this->articleId
        );
    }

    public function display_category(): string
    {
        switch ($this->category) {
            case 'local':
                return 'Local News';
            case 'international':
                return 'International News';
            case 'economy':
                return 'Economy News';
            case 'weather':
                return 'Weather Forecasts';
            case 'tourism':
                return 'Tourism';
            default:
                return '';
        }

    }

    public function get_author_name(): ?string
    {
        return $this->author;
    }

}