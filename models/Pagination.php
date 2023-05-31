<?php
declare(strict_types=1);

class Pagination
{

    /// @var int 
    public $entriesPerPage;
    /// @var string
    public $sql;
    /// @var ?int
    private $totalEntries;
    /// @var ?int
    private $totalPages;

    public function __construct(int $entriesPerPage, string $sql)
    {
        $this->entriesPerPage = $entriesPerPage;
        $this->sql = $sql;
    }

    public function get_total_entries(): ?int
    {
        if (isset($this->totalEntries))
            return $this->totalEntries;

        $db = Database::getInstance();
        $result = $db->query($this->sql);
        if ($result) {
            $this->totalEntries = $result->num_rows;
            return $this->totalEntries;
        } else
            return null;
    }

    public function get_total_pages(): ?int
    {
        if (isset($this->totalPages))
            return $this->totalPages;

        $totalEntries = $this->get_total_entries();
        if ($totalEntries) {
            $totalPages = (int) ceil($totalEntries / $this->entriesPerPage);
            $totalPages = $totalPages <= 0 ? 1 : $totalPages;
            $this->totalPages = $totalPages;
            return $totalPages;
        } else
            return null;
    }

    public function get_page(int $page): ?array
    {
        $query = $this->sql . ' limit ' . $this->entriesPerPage . ' offset ' . $this->entriesPerPage * ($page - 1) . ';';
        $db = Database::getInstance();
        $result = $db->query($query);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else
            return null;
    }

    public function pagination_controls(?int $currPage = null): string
    {
        if (!isset($currPage))
            $currPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $lastPage = $this->get_total_pages();
        $prevPage = $currPage > 1 ? $currPage - 1 : 1;
        $nextPage = $currPage < $lastPage ? $currPage + 1 : $lastPage;
        $out = '
          <nav aria-label="Page navigation">
              <ul class="pagination">
        ';
        for ($p = 1; $p <= $lastPage; $p++) {
            $out .= '<li class="page-item' . ($p == $currPage ? ' active' : '') . '"><a class="page-link page-number" data-page="' . $p . '" href="?p=' . $p . '">' . $p . '</a></li>';
        }
        $out .= '
              </ul>
          </nav>
        ';
        return $out;
    }
}

?>