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
    public $author;
    /// @var int|null 
    private $likes;

    public static function __set_state(array $state): Article
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
        if (isset($state['likes']))
            $article->likes = $state['likes'];
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
     * @return Pagination
     */
    public static function get_published_articles(): Pagination
    {
        $pagination = new Pagination(10, 'select * from Articles where 
                                   published = 1 and approved = 1
                                   order by date desc');
        return $pagination;
    }

    /**
     * @return Pagination
     */
    
    /**
     * @return Pagination
     */
    public static function get_categorized_articles(string $genre): Pagination
    {
        $pagination = new Pagination(10, 'select * from Articles where 
                                   published = 1 and approved = 1 and category = \''. $genre .'\'
                                   order by date desc');
        return $pagination;
    }

    /**
     * @return Pagination
     */
    
    public static function search_articles(string $search): Pagination
    {
        $db = Database::getInstance();
        return new Pagination(10, 'call `SearchArticles`(\'' . $db->escape($search) . '\'', true);
    }

    /**
     * @return array<Article>
     */
    public static function search_articles_exact(?string $search = null, ?bool $published = null, ?bool $approved = null, ?bool $removed = null): Pagination
    {
        $db = Database::getInstance();
        $sql = 'select Articles.*, concat(Users.firstName, \' \', Users.lastName) as author
                from Articles join Users on (Articles.writtenBy = Users.userId)
                where 1=1';
        if (isset($search))
            $sql .= ' and title like \'%' . $db->escape($search) . '%\'';
        if (isset($published))
            $sql .= ' and published = ' . (int) $published;
        if (isset($approved))
            $sql .= ' and approved = ' . (int) $approved;
        if (isset($removed))
            $sql .= ' and removed = ' . (int) $removed;
        $pagination = new Pagination(5, $sql);
        return $pagination;
    }

    public static function count_articles(?string $search = null, ?bool $published = null, ?bool $approved = null, ?bool $removed = null): int
    {
        $db = Database::getInstance();
        $sql = 'select count(*) as count
                from Articles join Users on (Articles.writtenBy = Users.userId) where 1 = 1';
        if (isset($search))
            $sql .= ' and title like \'%' . $db->escape($search) . '%\'';
        if (isset($published))
            $sql .= ' and published = ' . (int) $published;
        if (isset($approved))
            $sql .= ' and approved = ' . (int) $approved;
        if (isset($removed))
            $sql .= ' and removed = ' . (int) $removed;
        $row = $db->query($sql)->fetch_assoc();
        return (int) $row['count'];
    }


    /**
     * @return array<Article>
     */
    public static function get_author_articles(int $authorId, bool $published = false): array
    {
        $db = Database::getInstance();
        $articles = $db->query('select Articles.*, CONCAT(Users.firstName, \' \', Users.lastName) as author 
                                from Articles 
                                join Users on (Users.userId = Articles.writtenBy)
                                where writtenBy = ' . $authorId . ' 
                                   and published = ' . (int) $published . '
                                   order by date desc')->fetch_all(MYSQLI_ASSOC);
        return array_map(function ($row) {
            return Article::__set_state($row);
        }, $articles);
    }

    /**
     * @return array<array<string, string>>
     */
    public static function get_popular_articles(int $num, string $dateBegin, string $dateEnd): array
    {
        $db = Database::getInstance();
        $articles = $db->query('select distinct
                                       Articles.*,  
                                       CONCAT(Users.firstName, \' \', Users.lastName) as author,
                                       COUNT(CASE `like` when 1 then 1 else null end) over (partition by Articles.articleId) as likes,
                                       COUNT(CASE `like` when 0 then 1 else null end) over (partition by Articles.articleId) as dislikes
                                from Articles 
                                join Ratings on (Ratings.articleId = Articles.articleId)
                                join Users on (Users.userId = Articles.writtenBy)
                                where Ratings.date between \'' . $dateBegin . '\' and \'' . $dateEnd . '\'
                                order by likes desc
                                limit ' . $num)->fetch_all(MYSQLI_ASSOC);
        return $articles;
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
                'insert into Articles values (NULL,?,?,?,?,?,?,?,?,?,?)',
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
        if (isset($this->author) && !empty($this->author))
            return $this->author;
        else {
            $db = Database::getInstance();
            $result = $db->query('select CONCAT(Users.firstName, \' \', Users.lastName)
                                  from Articles join Users on (Articles.writtenBy = Users.userId)
                                  where articleId = ' . $this->articleId);
            if ($result && $result->num_rows > 0) {
                $author = $result->fetch_array()[0];
                $this->author = $author;
                return $author;
            } else
                return null;
        }
    }

    public function get_likes(): int
    {
        if (isset($this->likes))
            return $this->likes;
        else {
            $db = Database::getInstance();
            $result = $db->query('select COUNT(CASE `like` when 1 then 1 else null end) as likes
                                from Ratings where articleId = ' . $this->articleId . '
                                order by likes desc');
            if ($result && $result->num_rows > 0) {
                $likes = $result->fetch_array()[0];
                $this->likes = $likes;
                return $likes;
            } else
                return 0;
        }
    }

}