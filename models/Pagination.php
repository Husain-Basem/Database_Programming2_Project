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
    /// @var bool
    private $procedure;

    public function __construct(int $entriesPerPage, string $sql, bool $procedure = false)
    {
        $this->entriesPerPage = $entriesPerPage;
        $this->sql = $sql;
        $this->procedure = $procedure;
    }

    public function get_total_entries(): ?int
    {
        if (isset($this->totalEntries))
            return $this->totalEntries;

        $db = Database::getInstance();
        if ($this->procedure)
            $result = $db->query($this->sql . ',0,0)');
        else
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

    public function get_page(?int $page = null, ?callable $map = null): ?array
    {
        if (!isset($page))
            $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($this->procedure)
            $query = $this->sql . ', ' . $this->entriesPerPage . ', ' . $this->entriesPerPage * ($page - 1) . ');';
        else
            $query = $this->sql . ' limit ' . $this->entriesPerPage . ' offset ' . $this->entriesPerPage * ($page - 1) . ';';
        $db = Database::getInstance();
        $result = $db->query($query);
        if ($result) {
            if (isset($map))
                return array_map($map, $result->fetch_all(MYSQLI_ASSOC));
            else
                return $result->fetch_all(MYSQLI_ASSOC);
        } else
            return null;
    }

    public function pagination_controls(?int $currPage = null, ?string $urlParams = null, ?string $fragment = null): string
    {
        
        if (!isset($currPage))
            $currPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $lastPage = $this->get_total_pages();
$cat = $_GET['c'];
        $out = '
          <nav aria-label="Page navigation">
              <ul class="pagination">
 ';
        for ($p = 1; $p <= $lastPage; $p++) {
            if (isset($urlParams))
                $href = '?' . preg_replace('/&{0,}p=[0-9]+/', '', $urlParams) . '&p=' . $p . '&c=' . $cat;
            else
                $href = '?p=' . $p. '&c=' . $cat;
            if (!empty($fragment))
                $href .= '#' . $fragment;

            $out .= '<li class="page-item' . ($p == $currPage ? ' active' : '') . '"><a class="page-link page-number" data-page="' . $p . '" href="' . $href . '">' . $p . '</a></li>';
        }
        $out .= '
              </ul>
          </nav>
        ';
        return $out;
    }
}

?>